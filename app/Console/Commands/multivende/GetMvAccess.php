<?php

namespace App\Console\Commands\multivende;

use Illuminate\Console\Command;
use Carbon\Carbon;
use GuzzleHttp\Client;
use App\Models\MvAccess;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

#https://app.multivende.com/apps/authorize?response_type=code&client_id=636354652132&redirect_uri=asd&scope=read:checkouts

class GetMvAccess extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:mvaccess';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $startTime = Carbon::now();

        try {
            $access = MvAccess::query()->first();

            if ($access->token == NULL && $access->expires_token == NULL) {
                $this->getToken($access->id, $access->base_url, $access->client_id, $access->client_secret, $access->code);
            } else {
                $diff = $this->getHourDifference($access->expires_token);

                if ($diff === 0) {
                    $this->getTokenRefresh($access->id, $access->base_url, $access->client_id, $access->client_secret, $access->token_refresh);
                } elseif ($diff > 5 || $diff < 0) {
                    $destinatario = 'mathias.madrid@ynk.cl';
                    $asunto = 'ERROR Multivende';
                    $mensaje = 'ERROR, Renovar Code Manual en Multivende';
                    $this->sendEmail($destinatario, $mensaje, $asunto);
                    throw new \Exception('ERROR: Renovar Code Manual en Multivende');
                } else {
                    Log::channel('mv-multivende')->error("Faltan " . $diff . " horas para renovar token");
                }
            }
        } catch (\Exception $e) {
            Log::channel('mv-multivende')->error('Error de Conexión: ' . $e->getMessage());
            return Command::FAILURE; // Indica que ocurrió un error
        }

        $endTime = Carbon::now();
        $executionTime = $endTime->diffInSeconds($startTime);
        Log::channel('mv-multivende')->error('Get MV->Acces: ' . $executionTime . ' seconds');

        return Command::SUCCESS;
    }

    private function getToken($id, $base_url, $client_id, $client_secret, $code)
    {
        try {
            $httpClient = new Client();
            $startTime = Carbon::now();

            $url = $base_url . 'oauth/access-token';
            $response = $httpClient->post($url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'client_id' => $client_id,
                    'client_secret' => $client_secret,
                    'grant_type' => 'authorization_code',
                    'code' => $code,
                ],
            ]);

            $data = json_decode($response->getBody(), true);

            $token = $data['token'];
            $merchant_id = $data['MerchantId'];
            $token_refresh = $data['refreshToken'];
            $expires_token = $startTime->addHours(6);

            $access = MvAccess::find($id);

            if ($access) {
                // Realiza la actualización en los campos 
                $access->token = $token;
                $access->merchant_id = $merchant_id;
                $access->token_refresh = $token_refresh;
                $access->expires_token = $expires_token;
                $access->save();

                Log::channel('mv-multivende')->error('Token Generado.');
            } else {
                //Log::info('No se encontró el registro con el ID especificado.');
                throw new \Exception('No se encontró el registro con el ID especificado.');
            }
        } catch (\Exception $e) {
            //Log::error('Error al intentar obtener el token: ' . $e->getMessage());
            throw new \Exception('Error al intentar obtener el token: ' . $e->getMessage());
        }
    }

    private function getTokenRefresh($id, $base_url, $client_id, $client_secret, $token_refresh)
    {
        try {
            $httpClient = new Client();
            $startTime = Carbon::now();

            $url = $base_url . 'oauth/access-token';
            $response = $httpClient->post($url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'client_id' => $client_id,
                    'client_secret' => $client_secret,
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $token_refresh,
                ],
            ]);

            $data = json_decode($response->getBody(), true);

            $token = $data['token'];
            $merchant_id = $data['MerchantId'];
            $token_refresh = $data['refreshToken'];
            $expires_token = $startTime->addHours(6);

            $access = MvAccess::find($id);

            if ($access) {
                // Realiza la actualización en los campos 
                $access->token = $token;
                $access->merchant_id = $merchant_id;
                $access->token_refresh = $token_refresh;
                $access->expires_token = $expires_token;
                $access->save();

                Log::channel('mv-multivende')->error('Token Renovado');
            } else {
                //Log::info('No se encontró el registro con el ID especificado.');
                throw new \Exception('ERROR: No se encontró el registro con el ID especificado');
            }
        } catch (\Exception $e) {
            //Log::error('Error al intentar renovar el token: ' . $e->getMessage());
            throw new \Exception('Error al intentar renovar el token: ' . $e->getMessage());
        }
    }

    private function getHourDifference($expires_token)
    {
        // Convertir la fecha y hora de expiración del token a un objeto Carbon
        $expiresToken = Carbon::parse($expires_token);

        // Obtener la fecha y hora actual
        $now = Carbon::now();

        // Calcular la diferencia en horas entre la fecha y hora de expiración del token y la fecha y hora actual
        $differenceInHours = $expiresToken->diffInHours($now);

        // Invertir el resultado si la fecha y hora de expiración es anterior a la fecha y hora actual
        return ($expiresToken < $now) ? -$differenceInHours : $differenceInHours;
    }

    private function sendEmail($destinatario, $mensaje, $asunto)
    {
        Mail::raw($mensaje, function ($message) use ($destinatario, $asunto) {
            $message->to($destinatario)->subject($asunto);
        });

        return "Correo enviado correctamente a " . $destinatario;
    }
}
