<?php

namespace App\Http\Controllers;

use App\AwsSns\AwsSnsSms;
use Illuminate\Http\Request;

class TestController extends Controller
{
    
    public function index(Request $request)
    {
        $key = 'AKIA3CW3V6YQHFEXAYR3';
        $secret = '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';
        $region = 'us-east-1';
        // $phone_number = '+5219612491813';
        // $message = now() . ' Test message jajaja';

        $test = new AwsSnsSms($key, $secret, $region);

        return $test->SnsSmsClient($request->phone_number, $request->message);
    }
}
