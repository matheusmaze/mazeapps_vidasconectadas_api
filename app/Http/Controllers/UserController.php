<?php

namespace App\Http\Controllers;

use App\Donation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\MazeException;
use App\MazeHelper;
use App\User;
use App\EmailController;
use App\Jobs\ResetPassword;
use App\Jobs\ResponseEmail as JobsResponseEmail;
use Exception;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResponseEmail;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\Rules\Exists;

class UserController extends Controller
{
    public function registerBySite(Request $request){
        try{
            $req = $request->all();
            $req['document'] = MazeHelper::tirarPontos($request->document);

            $validator = Validator::make($req,
                User::$rules_register, MazeHelper::get_mensagens_validacao());

            if($validator->fails()){
                    return response()->json(['message' => $validator->errors()], 400);
            }

            $data = $request->all();
            $data['document'] = MazeHelper::tirarPontos($request->document);
            $data['phone_number'] = MazeHelper::tirarPontos($request->phone_number);
            $data['users_id'] = null;
            $data['nivel'] = "PADRINHO";
            $data['birthday'] = implode('-', array_reverse(explode('/', $request->birthday)));
            $user = User::create($data);

            if(isset($data['welcome_email'])){
                if($data['welcome_email'] == 1){
                   $this->enviarEmail($user, $data);
                }
            }

            $token = JWTAuth::fromUser($user);

            return response()->json(compact('user','token'),201);
        } catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível realizar o cadastro!', 500);
        }
    }

    public function registerByAdm(Request $request){
        try{
            $idAdm = Auth::user()->id;

            $req = $request->all();
            $req['document'] = MazeHelper::tirarPontos($request->document);

            $validator = Validator::make($req,
                User::$rules_register, MazeHelper::get_mensagens_validacao());


            if($validator->fails()){
                    return response()->json($validator->errors(), 400);
            }

            $data = $request->all();
            $data['document'] = MazeHelper::tirarPontos($request->document);
            $data['phone_number'] = MazeHelper::tirarPontos($request->phone_number);
            $data['users_id'] = $idAdm;
            $data['birthday'] = implode('-', array_reverse(explode('/', $request->birthday)));

            $user = User::create($data);

            if(isset($data['welcome_email'])){
                if($data['welcome_email'] == 1){
                   $this->enviarEmail($user, $data);
                }
            }
            $token = JWTAuth::fromUser($user);

            return response()->json(compact('user'),201);
        } catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível realizar o cadastro!', 500);
        }
    }

    public function registerByPadrinho(Request $request){
        try{
            $idPadrinho = Auth::user()->id;

            $req = $request->all();
            $req['document'] = MazeHelper::tirarPontos($request->document);

            $validator = Validator::make($req,
                User::$rules_register, MazeHelper::get_mensagens_validacao());


            if($validator->fails()){
                    return response()->json($validator->errors(), 400);
            }

            $data = $request->all();
            $data['document'] = MazeHelper::tirarPontos($request->document);
            $data['phone_number'] = MazeHelper::tirarPontos($request->phone_number);
            $data['users_id'] = $idPadrinho;
            $data['nivel'] = 'PADRINHO';
            $data['birthday'] = implode('-', array_reverse(explode('/', $request->birthday)));

            $user = User::create($data);

            if(isset($data['welcome_email'])){
                if($data['welcome_email'] == 1){
                   $this->enviarEmail($user, $data);
                }
            }

            $token = JWTAuth::fromUser($user);

            return response()->json(compact('user'),201);
        } catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível realizar o cadastro!', 500);
        }
    }

    public function authenticate(Request $request)
    {
        try
        {
            $validator = Validator::make($request->all(), [
                'document' => 'required|string|max:20',
                'password' => 'required|string|min:6',
            ], MazeHelper::get_mensagens_validacao());

            if($validator->fails()){
                throw new MazeException($validator->errors()->first(), 400);
            }

            $credentials = $request->only('document', 'password');
            $credentials['document'] = MazeHelper::tirarPontos($credentials['document']);

            if (! $token = JWTAuth::attempt($credentials))
            {
                throw new MazeException('Credenciais incorretas!', 400);
            }
        }
        catch (JWTException $e)
        {
            Log::error($e);
            throw new MazeException('Erro ao criar token!', 500);
        }
        return response()->json(compact('token'), 200);
    }

    public function reset_password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|max:255',
        ]);

        if($validator->fails()){
            throw new MazeException($validator->errors()->first(), 400);
        }

        $user = User::where('email', $request->all('email'))->first();

        if(empty($user)){
            throw new MazeException("Usuário não encontrado!", 400);
        }

        $new_password = mt_rand(100000, 999999);

        $user->password = $new_password;
        $user->save();

        $content['new_password'] = $new_password;
        $content['name'] = $user->name;

        ResetPassword::dispatch($user,$content)->delay(now());

        $token = JWTAuth::fromUser($user);

        return response()->json(['token' => $token], 201);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try
        {
            $user = User::all();

            return response()->json($user, 200);
        }
        catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível listar os usuário', 500);
        }
    }

    public function getUserByUserId($id){
        try{
            if(!$user = User::where('users_id', $id)->get())
            {
                throw new MazeException('Usuário não encontrado.', 404);
            }
            return response()->json($user, 200);

        } catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível localizar o usuário', 500);
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
    /*
    public function store(Request $request)
    {
        try
        {
            $validator = Validator::make($request->all(), User::$rules_post, MazeHelper::get_mensagens_validacao());

            if($validator->fails())
            {
                throw new MazeException($validator->errors()->first(), 400);
            }

            $user = new User();
            $user->fill($request->all());
            $user->save();

            return response()->json($user, 201);
        }
        catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível cadastrar o usuário', 500);
        }
    }
    */

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
            if(!$user = User::find($id))
            {
                throw new MazeException('Usuário não encontrado.', 404);
            }

            return response()->json($user, 200);
        }
        catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível listar os usuários', 500);
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
    public function update(Request $request, $id){
        try{
            $validator = Validator::make($request->all(), User::$rules_update, MazeHelper::get_mensagens_validacao());

            if($validator->fails())
            {
                throw new MazeException($validator->errors()->first(), 400);
            }

            $user = User::find($id);
            $user->fill($request->except(['password', 'document', 'email']));

            if(isset($request->password)) $user->password = Hash::make($request->get('password'));
            $user->save();

            $token = JWTAuth::fromUser($user);

            return response()->json(compact('user','token'),201);
        } catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível atualizar o usuário', 500);
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
            if(!$user = User::find($id))
            {
                throw new MazeException('Usuário não encontrado.', 404);
            }

            $user = User::destroy($id);

            return response()->json($user, 200);
        }
        catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível deletar o usuário', 500);
        }
    }

    public function getUserByNivel($nivel){
        try{
            if(!$user = User::where('nivel',$nivel)->get()){
                throw new MazeException('Usuário não encontrado.', 404);
            }
            return response()->json($user, 200);

        }  catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível deletar o usuário', 500);
        }
    }

    public function enviarEmail($user, $data){
        $content['nome_usuario'] = $user->name;
        $content['document'] = $user->document;
        $content['password'] = $data['password'];
        $content['url'] = 'http://'.env('URL','127.0.0.1:8000').'/confirmar_email/' . Crypt::encryptString($user->id);

        JobsResponseEmail::dispatch($user, $content)->delay(now());
    }

    public function cards_users() {
        try {
            $id = Auth::user()->id;

            $response = array();

            $response['donators'] = User::where('nivel', 3)->where('users_id', $id)->count();

            $response['donations'] = Donation::where('users_id', $id)->count();

            return response()->json($response,200);

        } catch (\Exception $ex) {
            Log::error($ex);
            return response()->json(
                [
                    "error" => [
                        "Falha ao obter dados do usuário"
                    ]
                ],
                400
            );
        }
    }
}
