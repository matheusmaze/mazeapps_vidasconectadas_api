<?php

namespace App;
use Exception;
use App\Exceptions\MazeException;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Storage;
use \PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use SimpleXMLElement;


class MazeHelper
{
    private static $mensagens_validacao = [
        'required' => 'campo :attribute é obrigatório.',
        'numeric' => 'campo :attribute deve ser um número',
        'date' => 'campo :attribute deve ser uma data',
        'integer' => 'campo :attribute deve ser um número inteiro',
        'exists' => 'campo correspondente a :attribute = :input não existe no banco de dados.',
        'regex' => 'campo :attribute possui formato incorreto',
        'size' => 'campo :attribute deve possuir tamanho :size.',
        'string' => 'campo :attribute deve ser uma string',
        'file' => 'campo :attribute deve ser um arquivo',
        'max' => 'campo :attribute deve ser no máximo :max',
        'unique' => 'campo :attribute já presente no banco, deve ser único',
        'required_without' => 'é obrigatório ao menos um dos campos: :attribute, :values',
    ];

    /**
     * Retorna as mensagens padrão para o validator e adiciona mensagens customizadas enviadas por parametro
     */
    static public function get_mensagens_validacao ($mensagens_custom = []){
        return array_merge(self::$mensagens_validacao, $mensagens_custom);
    }

    static public function hora_atual_brasil($data)
    {
        preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/', $data, $matches); // verifica se data tem formato YYYY-mm-dd hh:ii:ss

        if($matches) // se for do formato YYYY-mm-dd hh:ii:ss
        {
            return $matches[0];
        }
        else
        {
            date_default_timezone_set("America/Sao_Paulo");         // seta a correta timezone
            $hora = (intval(date('H', time())) -1) %24;             // por culpa do bolsonaro os horarios estão 1 hora na adiantados
            $hora = $hora < 0 ? 24 + $hora : $hora;                 // ajusta para que -1h vire 23h
            $hora = strval($hora) . ':' . date('i:s', time());      // adiciona o restante do horário
            $data = $data . ' ' . $hora;
            return $data;
        }
    }
    static public function calcula_distancia_em_metros_entre_enderecos($origin, $destination)
    {
        try
        {
            $origin = str_replace(' ','+',$origin);
            $destination = str_replace(' ','+',$destination);

            $retorno = json_decode(file_get_contents("https://maps.googleapis.com/maps/api/distancematrix/json?units=metric&origins='". $origin."'&destinations='".$destination."'&key=AIzaSyCks9nsG_uiFIL83Mhk1vYRRHGD-a7EC6E"));

            if($retorno->status != 'OK')
            {
                throw new Exception('Retorno da GOOGLE API distance matrix incorreto. Esperava-se ok, foi recebido: ' . $retorno->status);  // Exceptions são logadas e não são enviadas para o usuario.
            }
            if($retorno->rows[0]->elements[0]->status != 'OK')
            {
                throw new Exception('Retorno da GOOGLE API distance matrix incorreto. Esperava-se ok, foi recebido: ' . $retorno->rows[0]->elements[0]->status);  // Exceptions são logadas e não são enviadas para o usuario.
            }

            $distancia_em_m = $retorno->rows[0]->elements[0]->distance->value;
            return $distancia_em_m;
        }
        catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível calcular a distancia', 500);
        }
    }

    static public function troca_espaco_por_underline_retira_chars_especiais($string)
    {
        $string = str_replace(' ', '_', $string); // Replaces all spaces with hyphens.

        return preg_replace('/[^A-Za-z0-9\-\.]/', '', $string); // Removes special chars.
    }

    static public function salva_arquivo_aws($path, $arquivo, $nome_salvar)
    {
        try
        {
            $nome_salvar = self::troca_espaco_por_underline_retira_chars_especiais($nome_salvar);
            $url = 'https://s3.' . env('AWS_DEFAULT_REGION') . '.amazonaws.com/' . env('AWS_BUCKET') . '/';
            $name = time() . '_' . self::troca_espaco_por_underline_retira_chars_especiais(strtolower($arquivo));
            $filePath = $path . '/' . $nome_salvar;
            Storage::disk('s3')->put($filePath, file_get_contents($arquivo));
            return $url.$filePath;
        }
        catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível salvar o arquivo!', 500);
        }
    }

    /**
     * Verifica se o usuario é o dono do user_id, neste caso retorna true
     * ou se o usuario possui permissão pra acessar a rota,
     */
    static public function verifica_user_eh_o_dono_ou_admmaster($user, $user_id)
    {
        if($user->id == $user_id)  //
        {
            return true;
        }
        if($user->tipo == 3) // user é adm
        {
            return true;
        }
        // se tudo falhar, retorne false
        return false;
    }

    static function xmlToArray(SimpleXMLElement $xml): array
    {
        $parser = function (SimpleXMLElement $xml, array $collection = []) use (&$parser) {
            $nodes = $xml->children();
            $attributes = $xml->attributes();

            if (0 !== count($attributes)) {
                foreach ($attributes as $attrName => $attrValue) {
                    $collection['attributes'][$attrName] = strval($attrValue);
                }
            }

            if (0 === $nodes->count()) {
                //$collection['value'] = strval($xml);
                $collection = strval($xml);
                return $collection;
            }

            foreach ($nodes as $nodeName => $nodeValue) {
                if (count($nodeValue->xpath('../' . $nodeName)) < 2) {
                    $collection[$nodeName] = $parser($nodeValue);
                    continue;
                }

                $collection[$nodeName][] = $parser($nodeValue);
            }

            return $collection;
        };

        return [
            $xml->getName() => $parser($xml)
        ];
    }

    /**
     * Calcula a distancia de haversine entre dois pontos
     *
     * Calcula a distancia em metros entre dois pontos A e B a partir de suas latitudes e longitudes
     * @param mixed $a_latitude latitude do ponto A
     * @param mixed $a_longitude longitude do ponto A
     * @param mixed $b_latitude latitude do ponto B
     * @param mixed $b_longitude longitude do ponto B
     * @return float distancia em metros entre A e B
     */
    static function haversine ($a_latitude, $a_longitude, $b_latitude, $b_longitude)
    {
        return (6371e3 * acos(cos(deg2rad($a_latitude)) * cos(deg2rad($b_latitude)) * cos(deg2rad($b_longitude) - deg2rad($a_longitude)) + sin(deg2rad($a_latitude)) * sin(deg2rad($b_latitude))));
    }

    static function tirarPontos($valor){
        $valor = preg_replace('/[^0-9]/', "", $valor);
        return $valor;
    }

}
