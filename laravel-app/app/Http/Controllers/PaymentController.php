<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function send(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'address_id' => 'required|integer|exists:user_addresses,id',
            'coupon_code' => 'nullable|string',
        ]);
        
        if (!$request->session()->has('cart')) {
            return redirect()->route('cart.index')->with('error', 'سبد خرید شما خالی میباشد');
        }

        $cart = $request->session()->get('cart');

        $totalAmount = 0;
        foreach ($cart as $key => $orderItem) {
            $product = Product::findOrFail($key);

            if ($product->quantity < $orderItem['qty']) {
                return redirect()->route('cart.index')->with('error', 'تعداد محصول وارد شده اشتباه است');
            }

            $totalAmount += $product->is_sale ? $product->sale_price * $orderItem['qty'] : $product->price * $orderItem['qty'];
        }

        $couponAmount = 0;
        $coupon = null;
        if ($request->coupon_code) {

            $coupon = Coupon::where('code', $request->coupon_code)->where('expired_at', '>', Carbon::now())->first();
            
            if ($coupon == null) {
                dd($coupon);
                return redirect()->route('cart.index')->withErrors(['code' => 'کد تخفیف وارد شده وجود ندارد']);
            }

            if (Order::where('user_id', auth()->id())->where('coupon_id', $coupon->id)->where('payment_status', 1)->exists()) {
                return redirect()->route('cart.index')->withErrors(['code' => 'شما قبلا از این کد تخفیف استفاده کرده اید']);
            }

            $couponAmount = ($totalAmount * $coupon->percentage) / 100;
        }

        $payingAmount = $totalAmount - $couponAmount;

        $amounts = [
            'totalAmount' => $totalAmount,
            'couponAmount' => $couponAmount,
            'payingAmount' => $payingAmount,
        ];

        $merchant = 'zibal';
        $amount = $payingAmount ;
        $callbackUrl = 'https://myfastfoodtest.loca.lt/payment/verify';

        

        

        $result = $this->sendRequest($merchant, $amount, $callbackUrl);
        $result = json_decode($result);

    
        if ($result->result == 100) {
            OrderController::create($cart, $request->address_id, $coupon, $amounts, $result->trackId);
            
            return redirect()->to("https://gateway.zibal.ir/start/$result->trackId");
        } else {
            return redirect()->route('cart.index')->with('error', 'تراکنش با خطا مواجه شد');
        }
    }

    public function verify(Request $request)
    {
        $request->validate([
            'result' => 'required',
            'trackId' => 'required|integer'
        ]);

        $merchant = 'zibal';
        $trackId= $request->trackId;
        $result = json_decode($this->verifyRequest($merchant, $trackId));
    
        dd($result);
        if ($result->result == 100) {
            OrderController::update($merchant, $result->trackId);

            request()->session()->put('cart', []);
            return redirect()->route('payment.status', ['status'=> 1, 'transId'=>$result->trackId ])->with('success', 'تراکنش با موفقیت مواجه شد');

        } else {
            return redirect()->route('payment.status', ['status'=> 0])->with('error', 'تراکنش با خطا مواجه شد');
        }
        
        
    }

    public function sendRequest($merchant, $amount, $callbackUrl, $mobile = null, $factorNumber = null, $description = null)
    {
        return $this->curl_post('https://gateway.zibal.ir/v1/request', [
            'merchant'     => $merchant,
            'amount'       => $amount,
            'callbackUrl'  => $callbackUrl,
            'mobile'       => $mobile,
            'factorNumber' => $factorNumber,
            'description'  => $description,
        ]);
    }

    public function verifyRequest($merchant, $trackId)
    {
        return $this->curl_post('https://gateway.zibal.ir/v1/verify', [
            'merchant' => $merchant,
            'trackId'  => $trackId,
        ]);
    }

    public function curl_post($url, $params)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);
        $res = curl_exec($ch);
        curl_close($ch);

        return $res;
    }

    public function status(Request $request){
        $request->validate([
            'status'=> 'required',
            'transId'=> 'nullable'
        ]);

        $status= $request->status;
        $transId= $request->has('transId')? $request->transId : null;
        return view('payment.verify', compact('status', 'transId'));
    }
}