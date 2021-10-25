<?php

namespace App\WhatsApp;

use App\Exceptions\MazeException;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Support\Facades\Validator;
use App\Log;

class WhatsApp
{
    private $client;
    /* 
    private $mazepag_id;
    private $mazepag_token;
 */
    public function __construct()
    {
        $this->client = new Client([
            "base_uri" => env("WHATSAPP_BASE_URL", "127.0.0.1:80"),
            "timeout" => 300.0,
        ]);
    }

    public function send_message($data) {
        try
        {
            
            $res = $this->client->request("POST", "/chat/sendmessage_array", [ 'json' => $data]);
            return json_decode($res->getBody()->getContents());
        }
        catch(RequestException $e)
        {
            Log::create([
                'controller' => 'WhatsApp notification',
                'uri' => 'App\Console\Commands\SendRecurrency',
                'body'=> 'Resposta inesperada da Api Node WhatsApp. Código: ' .$e->getCode()
            ]);
            
            throw new MazeException('Erro ao conectar com a API Node WhatsApp, status esperado: 200, status recebido: ' . $e->getCode());
        }
    }
}