<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\Recurrency;
use App\Donation;
use App\Transaction;
use App\Log;
use App\PaymentGateways\MazepagGateway;
use App\WhatsApp\WhatsApp;

class SendRecurrency extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'routine:recurrency';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send recurrent payments of the day to donators';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        //Algumas variáveis estão em português porque o padrão do mazepag é em português

        $recurrencies = Donation::join('users', 'users.id', 'donations.users_id')
                                ->where('donations.recurrence_interval', 'MENSAL')
                                ->where('donations.recurrence_day', date('j'))
                                ->where('donations.end_recurrence', '>', now())
                                ->where('donations.payment_type', 'PIX')
                                ->where('donations.status', 'ACTIVE')
                                ->select('users.name as nome', 
                                        'users.document as documento',
                                        'users.email as email',
                                        'users.phone_number as phone_number', 
                                        'donations.id as donations_id',
                                        'donations.notification_type as notification_type',
                                        'fixed_value as valor')
                                ->get();
                            
        if (!isset($recurrencies[0])) {
            
            Log::create([
                'controller' => 'Recurrency Routine',
                'uri' => 'App\Console\Commands\SendRecurrency',
                'body'=> 'Nenhuma recorrência agendada para este dia'
            ]);

            return 'Nenhuma recorrência agendada para este dia';
        }
        
        $content['pixes'] = $recurrencies;
        
        $mazepag = new MazepagGateway();
        $res = $mazepag->pix_array($content);  //Retorna os objetos de recurrencies, adicionando os campos pix e txid em cada objeto. Pix: Código Pix copia e cola
        
        $count['email'] = 0;
        $count['wpp'] = 0;
        $count['erro'] = 0;
        $wpp_notifications = [];

        foreach($res as $r) {

            if($r->pix == 'Erro') {

                Log::create([
                    'controller' => 'Recurrency Routine',
                    'uri' => 'App\Console\Commands\SendRecurrency',
                    'body'=> 'Erro ao gerar código Pix. Usuário: ' . $r->nome . ". Donations_id: " . $r->donations_id
                ]);
                $count['erro'] += 1;
                
            }
            else {
                if ($r->notification_type == 'EMAIL') {
                    
                    Transaction::create([
                        'value' => $r->valor,
                        'donations_id' => $r->donations_id,
                        'status' => 'PENDING',
                        'txid' => $r->txid
                    ]);
                    
                    $body['name'] = $r->nome;
                    $body['value'] = $r->valor;
                    $body['pix'] = $r->pix;
                    
                    Mail::to($r->email)->send(new Recurrency($body));
                    $count['email'] += 1;
                }

                if ($r->notification_type == 'WHATSAPP') {

                    $phone = $this->set_phone($r->phone_number);

                    //Cada mensagem é colocada em uma posição do array e, depois do foreach, o array é passado para a api node
                    //A mensagem é colocada na posição (tamanho do array -1), para ser na mesma posição do número e criar um objeto  
                    $wpp_notifications[count($wpp_notifications)]['phone'] = $phone;
                    $wpp_notifications[count($wpp_notifications) -1]['message'] = $this->set_text($r->nome, $r->valor);

                    //Mensagem com o código Pix separado
                    $wpp_notifications[count($wpp_notifications)]['phone'] = $phone;
                    $wpp_notifications[count($wpp_notifications) -1]['message'] = $r->pix;

                    Transaction::create([
                        'value' => $r->valor,
                        'donations_id' => $r->donations_id,
                        'status' => 'PENDING',
                        'txid' => $r->txid
                    ]);
                    
                    $count['wpp'] += 1;
                }
            }
        }

        Log::create([
            'controller' => 'Recurrency Routine',
            'uri' => 'App\Console\Commands\SendRecurrency',
            'body'=> json_encode($count)
        ]);

        if (!empty($wpp_notifications)) {
            $wpp['send'] = $wpp_notifications;
            $whatsApp = new WhatsApp();
            $res = $whatsApp->send_message($wpp);
        }
        
    }

    //Não indentar essa parte do código, influencia na formatação do texto.
    public function set_text($name, $value) {
        return 'Olá, *' .$name. '!* Obrigado por fazer parte do nosso grupo de doadores.

' . 'Para facilitar o processo de copiar e colar, o código Pix será enviado separado. Copie todo o conteúdo da mensagem com o código.

' . 'Depois de colar o código Pix, antes de realizar o pagamento, confira as seguintes informações.
' . '*Valor*: ' . str_replace('.', ',', strval($value)) . ' reais
' . '*Nome*: Vidas Conectadas LTDA
' . '*CNPJ*: XX.XXX.XXX / XXX1-XX
' . '*Banco*: GerenicaNet S.A
' . '*Chave*: contatomazeapps@gmail.com

' . 'Qualquer dúvida, entre em contato. Atenciosamente, equipe *Vidas Conectadas*.';
    }

    /**
     *  Números com DDD > 30 não podem ter o 9 no começo para enviar wpp, DDD < 30, precisam ter.
     *  Como, atualmente, é uma regra simples, foi decidido tratar através dessa função.
     */
    public function set_phone($phone) {
        
        //Tira 0 no começo, espaço, () e -, se houver
        $phone = preg_replace('/[\(\)\-\" "]/', '', $phone);
        $phone = preg_replace('/^[0]/', '', $phone);
        
        if (preg_match('/^[3-9][0-9]{10}$/', $phone)) {
            //DDD > 30 e tem o 9: tira o 9
            $ddd = $phone[0] . $phone[1];
            return '55' . $ddd . preg_replace('/^[0-9]{2}[9]/', '', $phone);
        } else if (preg_match('/^[3-9][0-9]{9}$/', $phone)) { 
            //DDD > 30 e não tem o 9: deixa sem 
            return '55' . $phone;
        } else if (preg_match('/^[0-2][0-9]{10}$/', $phone)) {
            //DDD < 30 e tem o 9: deixa o 9
            return '55' . $phone;
        } else if (preg_match('/^[0-2][0-9]{9}$/', $phone)) {
            //DDD < 30 e não tem o 9: adiciona o 9
            $ddd = $phone[0] . $phone[1];
            return '55' . $ddd . '9' . preg_replace('/^[0-2][0-9]/', '', $phone);
        }
    }
}

/*
Query antiga, com outros intervalos de recorrência

 $recurrencies = Donation::join('users', 'users.id', 'donations.users_id')
                                ->where(function($query) {
                                    $query->where('donations.recurrence_interval', 'MENSAL')
                                          ->whereDay('donations.recurrence_day', date('d'))
                                          ->where('donations.end_recurrence', '>', now())
                                          ->where('donations.payment_form', 'PIX');
                                })
                                ->orWhere(function($query) {
                                    $query->where('donations.recurrence_interval', 'BIMESTRAL')
                                    ->whereDay('donations.recurrence_day', date('d'))
                                    ->whereRaw('(month(now()) - month(donations.recurrence_day)) % 2 = 0')
                                    ->where('donations.end_recurrence', '>', now())
                                    ->where('donations.payment_form', 'PIX');
                                })
                                ->orWhere(function($query) {
                                    $query->where('donations.recurrence_interval', 'TRIMESTRAL')
                                    ->whereDay('donations.recurrence_day', date('d'))
                                    ->whereRaw('(month(now()) - month(donations.recurrence_day)) % 3 = 0')
                                    ->where('donations.end_recurrence', '>', now())
                                    ->where('donations.payment_form', 'PIX');
                                })
                                ->orWhere(function($query) {
                                    $query->where('donations.recurrence_interval', 'SEMESTRAL')
                                    ->whereDay('donations.recurrence_day', date('d'))
                                    ->whereRaw('(month(now()) - month(donations.recurrence_day)) % 6 = 0')
                                    ->where('donations.end_recurrence', '>', now())
                                    ->where('donations.payment_form', 'PIX');
                                })
                                ->orWhere(function($query) {
                                    $query->where('donations.recurrence_interval', 'ANUAL')
                                          ->whereDay('donations.recurrence_day', date('d'))
                                          ->whereMonth('donations.recurrence_day', date('m'))
                                          ->where('donations.end_recurrence', '>', now())
                                          ->where('donations.payment_form', 'PIX');
                                }) 
                                ->orWhere(function($query) {
                                    $query->where('donations.recurrence_interval', 'DIARIA')
                                          ->where('donations.end_recurrence', '>', now())
                                          ->where('donations.payment_form', 'PIX');
                                }) 
                                ->select('users.name as nome', 
                                        'users.cpf as cpf',
                                        'users.email as email', 
                                        'donations.recurrence_interval as interval', 
                                        'donations.notification_type as notification_type',
                                        'donations.payment_form as payment_method',
                                        'fixed_value as valor')
                                ->get();
*/