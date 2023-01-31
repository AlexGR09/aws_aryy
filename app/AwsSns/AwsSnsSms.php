<?php

namespace App\AwsSns;

use Aws\Exception\AwsException;
use Aws\Sns\SnsClient;

class AwsSnsSms
{
    protected string $access_key;

    protected string $secret_access_key;

    protected string $region;

    /**
     * Función contructura del cliente de AWS SNS
     *
     * @return object Instancia del objeto Snsclient con los parámetros correspondientes
     * @var string $access_key El token de acceso que brinda la cuenta de AWS (Recomendable user IAM)
     * @var string $secret_access_key Clave secreta que brinda la cuenta de AWS (Recomendable user IAM)
     * @var string $region Zona geográfica que se usa en la cuenta de AWS
    */
    public function __construct(string $access_key = NULL, string $secret_access_key = NULL, string $region = NULL) 
    {
        $this->access_key = $access_key ?: $_ENV['APP_AWS_SNS_ACCESS_KEY']  ?? NULL;
        $this->secret_access_key = $secret_access_key ?: $_ENV['APP_AWS_SNS_SECRET_ACCESS_KEY']  ?? NULL;
        $this->region = $region ?: $_ENV['APP_AWS_SNS_REGION']  ?? NULL;
    }

    public function accessKey(): string
    {
        return $this->access_key;
    }

    public function secretAccessKey(): string
    {
        return $this->secret_access_key;
    }

    public function region(): string
    {
        return $this->region();
    }

    /**
     * Función que arma el sms y lo envía al destinatario correspondiente
     * 
     * @return mixed
     * @var string $phone_number Número de teléfono del destinatario usando el formato E.164
     * @var string $message Cuerpo del mensaje que se envía al destinatario
    */
    public function SnsSmsClient(string $phone_number = NULL, string $message = NULL)
    {
        $SnSclient = new SnsClient([
            'credentials' => [
                'key' => $this->accessKey(),
                'secret' => $this->secretAccessKey()
            ],
            'region' => $this->region(),
            'version' => 'latest'
        ]);

        $args = [
            'MessageAttributes' => [
                // DESCOMENTAR EL CÓDIGO SÓLO SI SE TIENE UN AWS SENDER ID
                // 'AWS.SNS.SMS.SenderID' => [
                //     'DataType' => 'String',
                //     'StringValue' => ''
                // ],
                'AWS.SNS.SMS.SMSType' => [
                    'DataType' => 'String',
                    'StringValue' => 'Transactional'
                ]
            ],
            'PhoneNumber' => $phone_number,
            'Message' => $message
        ];

        try {
            $result = $SnSclient->publishAsync($args);

            $res = $result->wait();

            return response()->json([
                'message' => 'Enviando mensaje...',
                'message_id' => $res->get('MessageId')
            ]);
        } catch (AwsException $e) {
            error_log($e->getMessage());
        }
    }

}