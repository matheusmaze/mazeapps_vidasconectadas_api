<?php

namespace App\Http\Controllers;

use App\Buy;
use App\Donation;
use App\Institution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\User;
use App\Transaction;
use Illuminate\Support\Facades\DB;
use App\Exceptions\MazeException;
use Carbon\Carbon;
use Exception;

class AdmController extends Controller
{
    public function cards_geral() {
        try {
            $response = array();

            $response['users'] = User::where('nivel', 3)->count();

            $response['donations'] = Donation::count();

            $response['institutions'] = Institution::count();

            $response['buys'] = Buy::count();

            return response()->json($response,200);

        } catch (\Exception $ex) {
            Log::error($ex);
            return response()->json(
                [
                    "error" => [
                        "Falha ao obter dados de dashboard"
                    ]
                ],
                400
            );
        }
    }

    public function reportPerMonth($month){
        try{
            $report = Transaction::whereMonth('created_at', $month)
                ->select(DB::raw("sum(value) as valor_total, created_at as data"))
                ->groupBy(DB::raw('day(created_at)'))
                ->get();

            return response()->json($report, 200);

        }catch (MazeException $e)
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
