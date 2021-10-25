<?php

namespace App\Http\Controllers;

use App\Donation;
use App\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\MazeException;
use App\MazeHelper;
use Exception;
use Illuminate\Support\Facades\Log;


class TransactionController extends Controller
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
            $transaction = Transaction::all();

            return response()->json($transaction, 200);
        }
        catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível listar as transações', 500);
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
            $validator = Validator::make($request->all(), Transaction::$rules_post, MazeHelper::get_mensagens_validacao());

            if($validator->fails())
            {
                throw new MazeException($validator->errors()->first(), 400);
            }

            $transaction = new Transaction();
            $transaction->fill($request->all());


            if ($payment_voucher = $request->file('payment_voucher')) {
                $transaction->payment_voucher = MazeHelper::salva_arquivo_aws('payment_voucher', $payment_voucher, time() . '_' . $payment_voucher->getClientOriginalName());
            }
            else {
                throw new MazeException('Não foi possível encontrar a imagem!', 404);
            }

            $transaction->save();

            return response()->json($transaction, 201);
        }
        catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível cadastrar a transação', 500);
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
            if(!$transaction = Transaction::find($id))
            {
                throw new MazeException('Transação não encontrada.', 404);
            }

            return response()->json($transaction, 200);
        }
        catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível listar as transações', 500);
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

            $validator = Validator::make($request->all(), Transaction::$rules_update, MazeHelper::get_mensagens_validacao());

            if($validator->fails())
            {
                throw new MazeException($validator->errors()->first(), 400);
            }

            if(!$transaction = Transaction::find($id))
            {
                throw new MazeException('Transação não encontrada.', 404);
            }

            $transaction->fill($request->all());

            if(!$request->file('payment_voucher') == null){
                if ($payment_voucher = $request->file('payment_voucher')) {
                    $transaction->payment_voucher = MazeHelper::salva_arquivo_aws('payment_voucher', $payment_voucher, time() . '_' . $payment_voucher->getClientOriginalName());
                }
            }else{
                $transaction->payment_voucher = $transaction->payment_voucher;
            }

            $transaction->save();

            return response()->json($transaction, 200);
        }
        catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível atualizar a transação', 500);
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
            if(!$transaction = Transaction::find($id))
            {
                throw new MazeException('Transação não encontrada.', 404);
            }

            $transaction = Transaction::destroy($id);

            return response()->json($transaction, 200);
        }
        catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível deletar a transação', 500);
        }
    }
     /**
     * Pegar os dados de uma transação de acordo com o id da doação
     */
    public function getTransactionByDonationId($id){
        try{
            if(!$transaction = Transaction::where('donations_id', $id)->get())
            {
                throw new MazeException('Transação não encontrada.', 404);
            }
            return response()->json($transaction, 200);

        } catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível localizar a transação', 500);
        }
    }
}
