<?php

namespace App\Console\Commands;

use App\AwsSns\AwsSnsSms;
use App\Models\MedicalAppointment;
use App\Models\Patient;
use App\Models\Physician;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RememberAppointmentHourly extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rememberAppointment:hourly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Emite un recordatorio a los usuarios de citas médicas próximas vía sms';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $key = 'AKIA3CW3V6YQHFEXAYR3';
        $secret = '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';
        $region = 'us-east-1';

        $aws_sms= new AwsSnsSms($key, $secret, $region);

        $today = Carbon::now()->format('Y-m-d H');

        $tomorrow = Carbon::now()->addDay()->format('Y-m-d H');  

        $appointments = MedicalAppointment::whereBetween(DB::raw(
            'CONCAT(appointment_date, " ", DATE_FORMAT(appointment_time, "%H"))'), [$today, $tomorrow])
            ->get();
        
        $remember_appoinment = 0; // BANDERA DE LA CANTIDAD DE MENSAJES ENVIADOS

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

            if ($hours_apart === 23) {
                $message = '¡Hola!, Aryy te recuerda que tienes una cita próxima el día ' . $appointment_date . ' a las ' . $appointment_time . ' hrs con tu especialista.';
                $remember_appoinment++;
                $aws_sms->SnsSmsClient($phone_number, $message);
            }
            else if ($hours_apart === 5){
                $message = '¡Hola!, Aryy te recuerda que tienes una cita el día de hoy' . ' a las ' . $appointment_time . ' hrs con tu ' . $physician->professional_name . '.';
                $remember_appoinment++;
                $aws_sms->SnsSmsClient($phone_number, $message);
            }
        }

        $this->info('Se enviaron ' . $remember_appoinment . ' mensaje(s) de texto de recordatorio de citas médicas enviado(s) con éxito. *Cada hora*');
    }
}
