<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\SendMail;

class MailController extends Controller{
    
    public function create(){

        SendMail::dispatch()->delay(now()->addMinutes(5));
        return '送信完了';
        
    }


}
    
