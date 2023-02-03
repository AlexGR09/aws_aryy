<?php

namespace App\Http\Controllers;

use App\AwsSns\AwsSnsSms;
use App\Models\MedicalAppointment;
use App\Models\Patient;
use App\Models\Physician;
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
        $today = Carbon::now()->format('Y-m-d H');

        $tomorrow = Carbon::now()->addHours(25)->format('Y-m-d H');   

        $appointments = MedicalAppointment::whereBetween(DB::raw(
            'CONCAT(appointment_date, " ", DATE_FORMAT(appointment_time, "%H"))'), [$today, $tomorrow])
            ->get();

        $res = [];

        foreach ($appointments as $key => $appointment) {

            // CREA UN OBJETO CARBON CON LA FECHA Y HORA DE LA CITA => '2023-02-03T03:00:00.000000Z'
            $appoinment_date_time = new Carbon($appointment->appointment_date . ' ' . $appointment->appointment_time); 

            // DA FORMATO DE FECHA DÍA/MES/AÑO => '02/02/2023'
            $appointment_date = Carbon::parse($appointment->appointment_date)->translatedFormat('d/m/Y');

            // DA FORMATO DE HORARIO HORA:MINUTO => '21:30'
            $appointment_time = Carbon::parse($appointment->appointment_time)->format('H:i');

            // DIFERENCIA EN ENTERO ENTRE LA FECHA DE HOY Y LA FECHA DE LA CITA => '24'
            $hours_apart = Carbon::now()->diffInHours($appoinment_date_time, false);

            // INSTANCIA DEL MODELO MÉDICO
            $physician = Physician::where('id', $appointment->physician_id)->firstOrFail();

            // INSTANCIA DEL MODELO PACIENTE
            $patient = Patient::where('id', $appointment->patient_id)->firstOrFail();

            // COMBINA EL CÓDIGO DEL PAÍS Y EL NÚMERO TELÉFONICO DEL PERFIL DE USUARIO DEL PACIENTE => '+5219611234567'
            $phone_number = $patient->user->country_code . $patient->user->phone_number;

            switch ($hours_apart) {
                case $hours_apart == 24:
                    $message = [
                        'message' => '¡Hola!, Aryy te recuerda que tienes una cita próxima el día ' . $appointment_date . ' a las ' . $appointment_time . ' hrs con tu especialista.',
                        'hours_apart' => $hours_apart,
                        'phone_number' => $phone_number
                    ];
                    array_push($res, $message);
                    break;
    
                case $hours_apart == 6:
                    $message = [
                        'message' => '¡Hola!, Aryy te recuerda que tienes una cita el día de hoy' . ' a las ' . $appointment_time . ' hrs con tu ' . $physician->professional_name . '.',
                        'hours_apart' => $hours_apart,
                        'phone_number' => $phone_number
                    ];
                    array_push($res, $message);
                    break;
                
                default:
                    break;
            }

        }

        return $res;
    }
}
