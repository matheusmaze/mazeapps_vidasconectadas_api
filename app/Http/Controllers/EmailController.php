<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Exceptions\MazeException;
use Exception;
use Illuminate\Support\Facades\Crypt;
use App\Mail\ResponseEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class EmailController extends Controller
{
    public function confirmar_email($id) {
        try{
            $user = User::find(Crypt::decryptString($id));

            if (Carbon::now() > $user->updated_at->addDays(3)) throw new MazeException('Pedido de confirmação expirado.', 400);

            $user->verified_at = Carbon::now();
            $user->save();

            return response()->json('Email confirmado com sucesso.', 200);
        } catch (MazeException $e) {
            log::info($e);
            return response()->json($e->getMessage(), $e->getCode());
        } catch (Exception $e){
            log::info($e);
            return response()->json('Não foi possível confirmar o email.', 500);
        }
    }

    public function enviar_confirmacao_email(Request $request, $id) {
        try{
            $user = User::find($id);

            if ($user->verified_at != null) throw new MazeException('Usuário já possui um email confirmado.', 400);

            $user->email = $request->email;
            DB::beginTransaction();
            $user->save();

            $content['nome'] = $user->name;
            $content['url'] = env('URL').'/confirmar_email/' . Crypt::encryptString($user->id);

            Mail::to($user->email)->send(new ResponseEmail(
                $content,"Bem vindo à Vidas Conectadas!","envia_confirmacao_email"
            ));
            DB::commit();
            return response()->json('Email de confirmação enviado com sucesso.', 200);
        } catch (MazeException $e) {
            log::info($e);
            DB::rollBack();
            return response()->json($e->getMessage(), $e->getCode());
        } catch (Exception $e){
            log::info($e);
            DB::rollBack();
            return response()->json('Não foi possível enviar o email de confirmação.', 500);
        }
    }
}
