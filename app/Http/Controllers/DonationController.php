<?php

namespace App\Http\Controllers;

use App\Donation;
use Illuminate\Http\Request;
use App\Exceptions\MazeException;
use App\Jobs\Recurrency as JobsRecurrency;
use App\Jobs\ThanksEmail;
use App\Mail\Recurrency;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\MazeHelper;
use App\PaymentGateways\MazepagGateway;
use App\Transaction;
use App\User;
use App\WhatsApp\WhatsApp;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class DonationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try
        {
            $donation = Donation::with('users')->withTrashed()->get();

            return response()->json($donation, 200);
        }
        catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível listar as doações', 500);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexFiltered($datainicio = 0, $datafim = 0)
    {
        try
        {
            if ($datainicio == 0) $datainicio = '1921-01-01';
            if ($datafim == 0) $datafim = '9999-01-01';

            $donation = Donation::with('users')->whereBetween('donations.created_at', [$datainicio, $datafim . ' 23:59:59'])
                                ->get();

            return response()->json($donation, 200);
        }
        catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível listar as doações', 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $unique = false)
    {
        try
        {
            $validator = Validator::make($request->all(), Donation::$rules_post, MazeHelper::get_mensagens_validacao());

            if($validator->fails())
            {
                throw new MazeException($validator->errors()->first(), 400);
            }

            $user = Auth::user();

            $donation = new Donation();
            $donation->fill($request->all());

            if($user->nivel == 'PADRINHO')
            {
                $donation['users_id'] = $user->id;
            }

            $donation->save();
            $content['nome_usuario'] = $user->name;

            if($donation->recurrence_interval == 'UNICA' && $unique == true){
                $recurrencies = [
                    "nome" => $user->name,
                    "documento" => $user->document,
                    "email" => $user->email,
                    "phone_number" => $user->phone_number,
                    "donations_id" => $donation->id,
                    "valor" => $donation->fixed_value
                ];

                $content['pixes'][0] = $recurrencies;

                $mazepag = new MazepagGateway();
                $res = $mazepag->pix_array($content);  //Retorna os objetos de recurrencies, adicionando os campos pix e txid em cada objeto. Pix: Código Pix copia e cola

                Transaction::create([
                    'value' => $res[0]->valor,
                    'donations_id' => $res[0]->donations_id,
                    'status' => 'PENDING',
                    'txid' => $res[0]->txid
                ]);

                $body['name'] = $res[0]->nome;
                $body['value'] = $res[0]->valor;
                $body['pix'] = $res[0]->pix;
                $body['thanks'] = 'Sua doação ajuda '. random_int(50,500).' pessoas';

                // Mail::to($res[0]->email)->send(new Recurrency($body));
                JobsRecurrency::dispatch($res[0], $body)->delay(now());
                return response()->json(['donation' => $donation, 'pix' => $res[0]->pix], 201);
            }

            ThanksEmail::dispatch($user, $content)->delay(now());

            return response()->json($donation, 201);


        }
        catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível cadastrar a doação', 500);
        }
    }

    public function storeToInvited(Request $request, $unique = false)
    {
        try
        {
            $validator = Validator::make($request->all(), Donation::$rules_post, MazeHelper::get_mensagens_validacao());

            if($validator->fails())
            {
                throw new MazeException($validator->errors()->first(), 400);
            }

            $user = Auth::user();

            $donation = new Donation();
            $donation->fill($request->all());

            //vai ser passado o id do usuário que eu vou criar a doação Ex:440
            $id = isset($request->users_id) ? $request->users_id : $user->id;

            //verifica se retornou algo, neste caso se retornar 0 o usuário não foi encontrado para esse doador
            if(count($userInvited = User::where('id', $id)->get()) == 0)
            {
                throw new MazeException('Usuário não encontrado.', 404);
            }

            foreach($userInvited as $userInv){
                //se o users_id do doardor convidado for diferente ao id do usuário autenticado
                if($userInv->users_id != $user->id){
                    throw new MazeException('Usuário não encontrado.', 404);
                }else{
                    $donation->save();
                    $content['nome_usuario'] = $userInv->name;

                    if($donation->recurrence_interval == 'UNICA' && $unique == true){
                        $recurrencies = [
                            "nome" => $userInv->name,
                            "documento" => $userInv->document,
                            "email" => $userInv->email,
                            "phone_number" => $userInv->phone_number,
                            "donations_id" => $donation->id,
                            "valor" => $donation->fixed_value
                        ];

                        $content['pixes'][0] = $recurrencies;

                        $mazepag = new MazepagGateway();
                        $res = $mazepag->pix_array($content);  //Retorna os objetos de recurrencies, adicionando os campos pix e txid em cada objeto. Pix: Código Pix copia e cola

                        Transaction::create([
                            'value' => $res[0]->valor,
                            'donations_id' => $res[0]->donations_id,
                            'status' => 'PENDING',
                            'txid' => $res[0]->txid,
                            'pix' => $res[0]->pix
                        ]);

                        $body['name'] = $res[0]->nome;
                        $body['value'] = $res[0]->valor;
                        $body['pix'] = $res[0]->pix;
                        $body['thanks'] = 'Sua doação ajuda '. random_int(50,500).' pessoas';

                        JobsRecurrency::dispatch($res[0], $body)->delay(now());

                        return response()->json(['donation' => $donation, 'pix' => $res[0]->pix], 201);
                    }

                    ThanksEmail::dispatch($userInv, $content)->delay(now());
                    return response()->json($donation, 201);
                }
            }
        }
        catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível cadastrar a doação', 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try
        {
            if(!$donation = Donation::find($id))
            {
                throw new MazeException('Doação não encontrada.', 404);
            }

            return response()->json($donation, 200);
        }
        catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível listar as doações', 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {

            $validator = Validator::make($request->all(), Donation::$rules_update, MazeHelper::get_mensagens_validacao());

            if($validator->fails())
            {
                throw new MazeException($validator->errors()->first(), 400);
            }

            if(!$donation = Donation::find($id))
            {
                throw new MazeException('Doação não encontrada.', 404);
            }

            $user = Auth::user();

            $donation->fill($request->all());

            if($user->nivel == 'PADRINHO')
            {
                $donation['users_id'] = $user->id;
            }

            $donation->save();

            return response()->json($donation, 200);
        }
        catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível atualizar a doação', 500);
        }
    }

    public function updateDonationUnique(Request $request, $id, $unique = false)
    {
        try {

            $validator = Validator::make($request->all(), Donation::$rules_update, MazeHelper::get_mensagens_validacao());

            if($validator->fails())
            {
                throw new MazeException($validator->errors()->first(), 400);
            }

            if(!$donation = Donation::find($id))
            {
                throw new MazeException('Doação não encontrada.', 404);
            }

            $user = Auth::user();

            $donation->fill($request->all());

            if($user->nivel == 'PADRINHO')
            {
                $donation['users_id'] = $user->id;
            }

            $donation->save();

            return response()->json($donation, 200);
        }
        catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível atualizar a doação', 500);
        }
    }

    public function UpdateToInvited(Request $request, $id, $unique = false)
    {
        try
        {
            $validator = Validator::make($request->all(), Donation::$rules_post, MazeHelper::get_mensagens_validacao());

            if($validator->fails())
            {
                throw new MazeException($validator->errors()->first(), 400);
            }

            $user = Auth::user();

            if(!$donation = Donation::find($id))
            {
                throw new MazeException('Doação não encontrada.', 404);
            }

            $donation->fill($request->all());

            //Pega o id do usuário que eu vou atualizar a doação Ex:440
            $id = isset($donation->users_id) ? $donation->users_id : $user->id;

            //verifica se retornou algo, neste caso se retornar 0 o usuário não foi encontrado para esse doador
            if(count($userInvited = User::where('id', $id)->get()) == 0)
            {
                throw new MazeException('Usuário não encontrado.', 404);
            }

            //se o users_id do doador convidado for diferente ao id do usuário autenticado
            if($userInvited[0]->users_id != $user->id){
                throw new MazeException('Usuário não encontrado.', 404);
            }else{
                $donation->save();
                $content['nome_usuario'] = $userInvited[0]->name;

                if($donation->recurrence_interval == 'UNICA' && $unique == true){
                    $recurrencies = [
                        "nome" => $userInvited[0]->name,
                        "documento" => $userInvited[0]->document,
                        "email" => $userInvited[0]->email,
                        "phone_number" => $userInvited[0]->phone_number,
                        "donations_id" => $donation->id,
                        "valor" => $donation->fixed_value
                    ];

                    $content['pixes'][0] = $recurrencies;

                    $mazepag = new MazepagGateway();
                    $res = $mazepag->pix_array($content);  //Retorna os objetos de recurrencies, adicionando os campos pix e txid em cada objeto. Pix: Código Pix copia e cola

                    Transaction::where('donations_id', $donation->id)->update([
                        'value' => $res[0]->valor,
                        'donations_id' => $res[0]->donations_id,
                        'status' => 'PENDING',
                        'txid' => $res[0]->txid
                    ]);

                    $body['name'] = $res[0]->nome;
                    $body['value'] = $res[0]->valor;
                    $body['pix'] = $res[0]->pix;
                    $body['thanks'] = 'Sua doação ajuda '. random_int(50,500).' pessoas';

                    JobsRecurrency::dispatch($res[0], $body)->delay(now());

                    return response()->json(['donation' => $donation, 'pix' => $res[0]->pix], 201);
                }

                ThanksEmail::dispatch($userInvited[0], $content)->delay(now());
                return response()->json($donation, 201);
            }

        }
        catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível cadastrar a doação', 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try
        {
            if(!$donation = Donation::find($id))
            {
                throw new MazeException('Doação não encontrada.', 404);
            }

            $donation->status = 'CANCELED';
            $donation->save();
            $donation = Donation::destroy($id);

            return response()->json($donation, 200);
        }
        catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível deletar a doação', 500);
        }
    }

     /**
     * Pegar os dados de uma doação de acordo com o id do usuário
     */
    public function getDonationByUserId($id){
        try{
            if(!$donation = Donation::withTrashed()->where('users_id', $id)->get())
            {
                throw new MazeException('Doação não encontrada.', 404);
            }
            return response()->json($donation, 200);

        } catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível localizar a doação', 500);
        }
    }
}
