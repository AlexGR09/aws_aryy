<?php

namespace App\Http\Controllers\Sms\Physician;

use App\AwsSns\AwsSnsSms;
use App\Http\Controllers\Controller;
use App\Models\MedicalAppointment;
use App\Models\Patient;
use App\Models\Physician;
use Carbon\Carbon;

class RememberAppointment extends Controller
{
    protected $physician;
    protected $key = 'AKIA3CW3V6YQHFEXAYR3';
    protected $secret = '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';
    protected $region = 'us-east-1';
    protected $aws_sms;

    public function __construct()
    {
        $this->middleware('role:Physician')->only([
            'rememberAppointmentSms',
        ]);

        $this->physician = empty(auth()->user()->id) ? null : Physician::where('user_id', auth()->user()->id)->firstOrFail();
    
        $this->aws_sms = new AwsSnsSms($this->key, $this->secret, $this->region);
    }

    public function rememberAppointmentSms($appointment_id)
    {
        try {
            $date_now = Carbon::now()->format('Y-m-d');

            $time_now_add_twenty_minutes = Carbon::now()->addMinutes(20)->format('H:i:s');
    
            // Obtiene la cita corrrespondiente al id, id del médico, con la fecha de hoy y que el horario de la cita sea mayor a la hora actual más 20 minutos
            $appointment = MedicalAppointment::where('id', $appointment_id)
                ->where('physician_id', $this->physician->id)
                ->where('appointment_date', $date_now)
               ->whereTime('appointment_time', '>=', $time_now_add_twenty_minutes)
                ->where('notified_by_physician', false)
                ->first();
    
            if ($appointment) {
                // INSTANCIA DEL MODELO MÉDICO
                $physician = Physician::where('id', $appointment->physician_id)->firstOrFail();
    
                // INSTANCIA DEL MODELO PACIENTE
                $patient = Patient::where('id', $appointment->patient_id)->firstOrFail();
    
                // COMBINA EL CÓDIGO DEL PAÍS Y EL NÚMERO TELÉFONICO DEL PERFIL DE USUARIO DEL PACIENTE => '+5219611234567'
                $phone_number = $patient->user->country_code . $patient->user->phone_number;
    
                $message = '¡Hola!, Aryy te recuerda que tienes una cita el día de hoy' . ' a las ' . Carbon::parse($appointment->appointment_time)->format('H:i') . ' hrs con tu ' . $physician->professional_name . '.';
    
                $appointment->notified_by_physician = true;
                $appointment->save();
    
                return $this->aws_sms->SnsSmsClient($phone_number, $message);
            }
            return response()->json(['message' => 'Cita muy próxima o ya ha sido notificada.']);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 503);
        }
       
    }
}
