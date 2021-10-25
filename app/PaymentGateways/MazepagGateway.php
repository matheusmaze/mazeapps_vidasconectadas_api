<?php

namespace App\PaymentGateways;

use App\Exceptions\MazeException;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Support\Facades\Validator;
use App\Log;

class MazepagGateway
{
    private $client;
    /* 
    private $mazepag_id;
    private $mazepag_token;
 */
    public function __construct()
    {
        $this->client = new Client([
            //url de produção: https://api.mazeapps.com.br
            "base_uri" => env("MAZEPAG_BASE_URL", "127.0.0.1:80"),
            "timeout" => 300.0,
        ]);
    }

    public function pix($data) {
        try
        {
            
            $res = $this->client->request("POST", "/api/pix", [ 'json' => $data]);
            return json_decode($res->getBody()->getContents());
        }
        catch(RequestException $e)
        {
            Log::create([
                'controller' => 'Recurrency Routine',
                'uri' => 'App\Console\Commands\SendRecurrency',
                'body'=> 'Resposta inesperada da Mazepag. Código: ' .$e->getCode()
            ]);
            
            throw new MazeException('Erro ao conectar com a API Mazepag, status esperado: 200, status recebido: ' . $e->getCode());
        }
    }

    public function pix_array($data) {
        try
        {
            
            $res = $this->client->request("POST", "/api/pix_array", [ 'json' => $data]);
            return json_decode($res->getBody()->getContents());
        }
        catch(RequestException $e)
        {
            Log::create([
                'controller' => 'Recurrency Routine',
                'uri' => 'App\Console\Commands\SendRecurrency',
                'body'=> 'Resposta inesperada da Mazepag. Código: ' .$e->getCode()
            ]);
            
            throw new MazeException('Erro ao conectar com a API Mazepag, status esperado: 200, status recebido: ' . $e->getCode());
        }
    }

    public function consult_array($data) {
        try
        {
            $content['consultas'] = $data;
            
            $res = $this->client->request("POST", "/api/consultar_array", [ 'json' => $content]);
            return json_decode($res->getBody()->getContents());
        }
        catch(RequestException $e)
        {
            Log::create([
                'controller' => 'Conciliation Routine',
                'uri' => 'App\Console\Commands\Conciliation',
                'body'=> 'Resposta inesperada da Mazepag. Código: ' .$e->getCode()
            ]);
            
            throw new MazeException('Erro ao conectar com a API Mazepag, status esperado: 200, status recebido: ' . $e->getCode());
        }
    }
}