<?php

namespace App\Http\Middleware;

use App\Exceptions\MazeException;
use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Exception;

class JwtMiddlewareNegociador
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try
        {
            $user = JWTAuth::parseToken()->authenticate();

            if(!$type = $user->nivel){
                throw new MazeException("Usuário não autorizado para este conteúdo");
            }
            if($type != 'NEGOCIADOR' && $type != 'MASTER')
            {
                throw new MazeException("Usuário não autorizado para este conteúdo");
            }
        }
        catch (MazeException $e){
            throw $e;
        }
        catch (Exception $e)
        {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException)
            {
                throw new MazeException('Token é inválido');
            }
            else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException)
            {
                throw new MazeException('Token esta expirado');
            }
            else
            {
                throw new MazeException('Token de autorização não encontrado');
            }
        }
        return $next($request);
    }
}
