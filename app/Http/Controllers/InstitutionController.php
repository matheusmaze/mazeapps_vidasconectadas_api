<?php

namespace App\Http\Controllers;

use App\Institution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\MazeException;
use App\MazeHelper;
use Exception;
use Illuminate\Support\Facades\Log;

class InstitutionController extends Controller
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
            $institution = Institution::all();

            return response()->json($institution, 200);
        }
        catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível listar as instituições', 500);
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
            $validator = Validator::make($request->all(), Institution::$rules_post, MazeHelper::get_mensagens_validacao());

            if($validator->fails())
            {
                throw new MazeException($validator->errors()->first(), 400);
            }

            $institution = new Institution();
            $institution->fill($request->all());
            $institution->cnpj = (isset($request->cnpj)) ?  MazeHelper::tirarPontos($request->cnpj) : $institution->cnpj;
            $institution->phone_number = (isset($request->phone_number)) ?  MazeHelper::tirarPontos($request->phone_number) : $institution->phone_number;

            if ($logo = $request->file('logo')) {
                $institution->logo = MazeHelper::salva_arquivo_aws('logo', $logo, time() . '_' . $logo->getClientOriginalName());
            }
            else {
                $url = 'https://via.placeholder.com/150';
                $institution->logo = $url;
            }

            $institution->save();

            return response()->json($institution, 201);
        }
        catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível cadastrar a instituição', 500);
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
            if(!$institution = Institution::find($id))
            {
                throw new MazeException('Instituição não encontrada.', 404);
            }

            return response()->json($institution, 200);
        }
        catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível listar as instituições', 500);
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
            $validator = Validator::make($request->all(), Institution::$rules_update, MazeHelper::get_mensagens_validacao());

            if($validator->fails())
            {
                throw new MazeException($validator->errors()->first(), 400);
            }

            if(!$institution = Institution::find($id))
            {
                throw new MazeException('Instituição não encontrada.', 404);
            }

            $institution->fill($request->all());
            $institution->cnpj = (isset($request->cnpj)) ?  MazeHelper::tirarPontos($request->cnpj) : $institution->cnpj;
            $institution->phone_number = (isset($request->phone_number)) ?  MazeHelper::tirarPontos($request->phone_number) : $institution->phone_number;

            if(!$request->file('logo') == null){
                if ($logo = $request->file('logo')) {
                    $institution->logo = MazeHelper::salva_arquivo_aws('logo', $logo, time() . '_' . $logo->getClientOriginalName());
                }
            }else{
                $institution->logo = $institution->logo;
            }

            $institution->save();

            return response()->json($institution, 200);
        }
        catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível atualizar a instituição', 500);
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
            if(!$institution = Institution::find($id))
            {
                throw new MazeException('Instituição não encontrada.', 404);
            }

            $institution = Institution::destroy($id);

            return response()->json($institution, 200);
        }
        catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível deletar a instituição', 500);
        }
    }
}
