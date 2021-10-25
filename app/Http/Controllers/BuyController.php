<?php

namespace App\Http\Controllers;

use App\Buy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\MazeException;
use App\Institution;
use App\Jobs\BuyNotification;
use App\MazeHelper;
use App\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BuyController extends Controller
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
            $buy = Buy::all();

            return response()->json($buy, 200);
        }
        catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível listar as compras', 500);
        }
    }

    public function BuyFiltered($status){
        try{
            $buy = Buy::where('status', $status)->get();

            return response()->json($buy, 200);
        } catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível listar as compras', 500);
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
    public function store(Request $request)
    {
        try
        {
            $user = Auth::user();
            $validator = Validator::make($request->all(), Buy::$rules_post, MazeHelper::get_mensagens_validacao());

            if($validator->fails())
            {
                throw new MazeException($validator->errors()->first(), 400);
            }

            $buy = new Buy();
            $buy->fill($request->all());

            $enviaEmailNotificacao = isset($request->emailNotificacao) ? $request->emailNotificacao : false;

            if ($purchase_voucher = $request->file('purchase_voucher')) {
                $buy->purchase_voucher = MazeHelper::salva_arquivo_aws('purchase_voucher', $purchase_voucher, time() . '_' . $purchase_voucher->getClientOriginalName());
            }
            else {
                throw new MazeException('Não foi possível encontrar a imagem!', 404);
            }

            $buy->save();

            $adms = User::where('nivel', 'MASTER')->get();
            $userEnviarEmail = User::where('id', $buy->users_id)->get();
            $institution = Institution::where('id', $buy->institutions_id)->get();

            $content['title'] = 'Nova compra';
            $content['name'] = $user->name;
            $content['institution'] = $institution[0]->name;

            $user = Auth::user();

            if($user->nivel == 'NEGOCIADOR'){
                foreach($adms as $adm){
                    BuyNotification::dispatch($adm, $content)->delay(now());
                }
            }
            if($user->nivel == 'MASTER' && $enviaEmailNotificacao == true){
                foreach($userEnviarEmail as $userEnviar){
                    BuyNotification::dispatch($userEnviar, $content)->delay(now());
                }
            }

            return response()->json($buy, 201);
        }
        catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível cadastrar a compra', 500);
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
            if(!$buy = Buy::find($id))
            {
                throw new MazeException('Compra não encontrada.', 404);
            }

            return response()->json($buy, 200);
        }
        catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível listar as compras', 500);
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

            $user = Auth::user();

            $validator = Validator::make($request->all(), Buy::$rules_update, MazeHelper::get_mensagens_validacao());

            if($validator->fails())
            {
                throw new MazeException($validator->errors()->first(), 400);
            }

            if(!$buy = Buy::find($id))
            {
                throw new MazeException('Compra não encontrada.', 404);
            }

            //Padrinho não pode editar compra
            if($user->nivel == 'PADRINHO'){
                throw new MazeException('Não é possível excluir uma compra.', 400);
            }
            //Não pode editar compra reprovada se for negociador
            else if($buy->status == 'Reprovada' && $user->nivel == 'NEGOCIADOR'){
                throw new MazeException('Não é possível editar uma compra reprovada.', 400);
            }
            //Não pode editar compra que é de outro comprador
            else if($buy->users_id != $user->id && $user->nivel == 'NEGOCIADOR'){
                throw new MazeException('Não é possível editar uma compra de outro usuário.', 400);
            }
            else{
                $buy->fill($request->all());
                //Comprador não pode reprovar uma compra(passando status como Reprovada)
                if($request->status == 'Reprovada' && $user->nivel == 'NEGOCIADOR'){
                    throw new MazeException('Não é possível Aprovar / Reprovar uma compra.', 400);
                }

                if(!$request->file('purchase_voucher') == null){
                    if ($purchase_voucher = $request->file('purchase_voucher')) {
                        $buy->purchase_voucher = MazeHelper::salva_arquivo_aws('purchase_voucher', $purchase_voucher, time() . '_' . $purchase_voucher->getClientOriginalName());
                    }
                }else{
                    $buy->purchase_voucher = $buy->purchase_voucher;
                }

                $buy->save();

                $adms = User::where('nivel', 'MASTER')->get();
                $userEnviarEmail = User::where('id', $buy->users_id)->get();
                $institution = Institution::where('id', $buy->institutions_id)->get();

                $content['title'] = 'Atualização de compra';
                $content['name'] = $user->name;
                $content['institution'] = $institution[0]->name;

                if($user->nivel == 'NEGOCIADOR'){
                    foreach($adms as $adm){
                        BuyNotification::dispatch($adm, $content)->delay(now());
                    }
                }

                if($user->nivel == 'MASTER'){
                    foreach($userEnviarEmail as $userEnviar){
                        BuyNotification::dispatch($userEnviar, $content)->delay(now());
                    }
                }
            }

            return response()->json($buy, 200);
        }
        catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível atualizar a compra', 500);
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
            $user = Auth::user();

            if(!$buy = Buy::find($id))
            {
                throw new MazeException('Compra não encontrada.', 404);
            }

            //O negociador não pode reprovar uma compra
            if($user->nivel == 'NEGOCIADOR' && $buy->users_id != $user->id){
                throw new MazeException('Não é possível excluir uma compra.', 400);
            }

            $buy = Buy::destroy($id);

            return response()->json($buy, 200);
        }
        catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível deletar a compra', 500);
        }
    }

    /**
     * Pegar os dados de uma compra de acordo com o id do usuário
     */

    public function getBuyByUserId($id){
        try{
            if(!$buy = Buy::where('users_id', $id)->get())
            {
                throw new MazeException('Compra não encontrada.', 404);
            }
            return response()->json($buy, 200);

        } catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível localizar a compra', 500);
        }
    }

     /**
     * Pegar os dados de uma compra de acordo com o id da instituição
     */
    public function getBuyByInstituitionId($id){
        try{
            if(!$buy = Buy::where('institutions_id', $id)->get())
            {
                throw new MazeException('Compra não encontrada.', 404);
            }
            return response()->json($buy, 200);

        } catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível localizar a compra', 500);
        }
    }
}
