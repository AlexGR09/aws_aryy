<?php

namespace App\Http\Controllers;

use App\AwsSns\AwsSnsSms;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function remember()
    {
        $message = '';

        $today = Carbon::now();

        $date_today = $today->toDateString(); 

        $time_today = $today->toTimeString();

        $date_time_today = Carbon::now()->format('Y-m-d H:i:s');

        $prueba = Carbon::createFromFormat('Y-m-d H', '2023-02-1 22')->toDateTimeString();


        $date_time_todayP = Carbon::now();

        $pruebaP = Carbon::createFromFormat('Y-m-d H', '2023-02-6 22');

        // se obtiene la diferencia en horas 
        $diferencia = $date_time_todayP->diffInHours($pruebaP);

        return 'fecha de hoy - ' . $date_time_today . ' otra fecha - ' .$prueba . 'diferencia - ' . $diferencia;

        return $date_time_today;

        // return $date_today . '---' . $time_today;

        $citas = DB::table('medical_appointments')
            ->where('appointment_date', $date_today)
            ->get();

        return $citas;

        switch ($today) {
            case '> 6 horas < 24 horas':
                # code...
                break;

            case '6 horas':
                # code...
                break;
            
        
            default:
                # code...
                break;
        }

    }
}
