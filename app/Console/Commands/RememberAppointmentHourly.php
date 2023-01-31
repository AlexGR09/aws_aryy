<?php

namespace App\Console\Commands;

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
    protected $description = 'Emite un recordatorio a los usuarios de cita médicas próximas via sms';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $message = '';

        $today = $;

        $citas = DB::table('medical_appointments')->where
        switch ($fecha) {
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



        $mesage = '';

        return Command::SUCCESS;
    }
}
