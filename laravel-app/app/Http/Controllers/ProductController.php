<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function show(Product $product)
    {
        // dd($product->images);
        $randomProduct = Product::where('quantity', '>', 0)->where('status', 1)->get()->random(4);
        return view('products.show', compact('product', 'randomProduct'));
    }

    public function menu(Request $request){
        $categories= Category::all();
        $products = Product::search($request->search)->filter()->paginate(9);
        return view('products.menu', compact('products', 'categories'));
    }
}
