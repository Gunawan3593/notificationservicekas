<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\OtpEmail;

class TestEmailController extends Controller
{
    //

    public function testEmail(Request $request)
    {
        $data = [
            'name' => 'BPR KAS',
            'otp' => rand(100000, 999999),
            'email' => 'mitrakas@gmail.com'
        ];

        \Mail::to($data['email'])->send(new OtpEmail($data));

        echo 'success';
    }
}
