<?php

namespace App\Http\Controllers;

use App\AwsSns\AwsSnsSms;
use App\Models\MedicalAppointment;
use App\Models\Patient;
use App\Models\User;
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

        $date_time_today = Carbon::now();

        $today = Carbon::now()->format('Y-m-d H');

        $tomorrow = Carbon::now()->addHours(24)->format('Y-m-d H');   

        $appointments = MedicalAppointment::whereBetween(DB::raw(
            'CONCAT(appointment_date, " ", DATE_FORMAT(appointment_time, "%H"))'), [$today, $tomorrow])
            ->get();

        foreach ($appointments as $key => $appointment) {

            $date_time_appoinment = new Carbon($appointment->appointment_date . ' ' . $appointment->appointment_time);

            $hours_apart = $date_time_today->diffInHours($date_time_appoinment, false);

            $patient = Patient::where('id', $appointment->patient_id)->firstOrFail();

            switch ($hours_apart) {
                case $hours_apart > 22 && $hours_apart <= 24:
                    $user = $patient->user; 
                    return 'Phone_number: ' . $user->country_code . $user->phone_number;
                    return 'mayor que 22, menor igual que 24';
                    break;
    
                case $hours_apart > 5 && $hours_apart <= 7:
                    return 'son 6 horas, el día de hoy';
                    break;
                
                default:
                    return 'Ninguno';
                    break;
            }

            return $hours_apart;
        }

        return $appointments;
    }
}
