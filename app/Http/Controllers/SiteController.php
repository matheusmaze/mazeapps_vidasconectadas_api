<?php

namespace App\Http\Controllers;

use App\Buy;
use App\Exceptions\MazeException;
use App\Log;
use App\Transaction;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SiteController extends Controller
{
    public function reportPerYear($year = '', $month = ''){
        try{
            $report = Array();

            $date = Carbon::create($year, $month, null);
            $now = $date->format('Y-m-t');
            $yearAgo = $date->addDays(-365)->format('Y-m-1');

            $report['doacoes'] = Transaction::where('status', 'PAID')
                ->whereBetween('payment_date', [$yearAgo, $now])
                ->select(DB::raw("sum(value) as valor_total, month(payment_date) as mes, year(payment_date) as ano"))
                ->groupBy("mes", "ano")
                ->orderBy("ano", "asc")
                ->orderBy("mes", "asc")
                ->get();


            $report['compras'] = Buy::where('status', 'Aprovada')
                ->whereBetween('created_at', [$yearAgo, $now])
                ->select(DB::raw("sum(value) as valor_total, month(created_at) as mes, year(created_at) as ano"))
                ->groupBy("mes", "ano")
                ->orderBy("ano", "asc")
                ->orderBy("mes", "asc")
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
