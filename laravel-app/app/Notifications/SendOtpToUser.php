<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Ghasedaksms\GhasedaksmsLaravel\Message\GhasedaksmsVerifyLookUp;
use Ghasedaksms\GhasedaksmsLaravel\Notification\GhasedaksmsBaseNotification;
use Ghasedak\DataTransferObjects\Request\InputDTO;
use Ghasedak\DataTransferObjects\Request\ReceptorDTO;
use Carbon\Carbon;

class SendOtpToUser extends GhasedaksmsBaseNotification
{
    use Queueable;

    protected $code;

    public function __construct($code)
    {
        $this->code = $code;
    }

    public function via($notifiable):
     array   
    {
        return ['ghasedaksms'];
    }

    public function toGhasedaksms($notifiable): GhasedaksmsVerifyLookUp
    {
        $message = new GhasedaksmsVerifyLookUp();
        $message->setSendDate(Carbon::now());
        $message->setReceptors([
            new ReceptorDTO($notifiable->cellphone, 'user')
        ]);
        $message->setTemplateName('Ghasedak'); 
        $message->setInputs([
            new InputDTO('code', $this->code)
        ]);
        return $message;
    }
}