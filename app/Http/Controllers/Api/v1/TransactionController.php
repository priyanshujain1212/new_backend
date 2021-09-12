<?php
/**
 * Created by PhpStorm.
 * User: dipok
 * Date: 19/4/20
 * Time: 5:59 PM
 */

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\MyTransactionResource;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        $i            = 1;
        $transactions = Transaction::orWhere('source_balance_id', auth()->user()->balance_id)->orWhere([ 'destination_balance_id' => auth()->user()->balance_id ])->orderBy('id', 'DESC')->get();
        $transactions->map(function( $query ) use ( &$i ) {
            $query['increment_id'] = $i;
            $query['type']         = trans('transaction_types.' . $query->type);
            $query['date']         = food_date_format_with_day($query->created_at);
            $query['amount']       = transactionCurrencyFormat($query);
            $i++;
        });
        return response()->json([
            'status' => 200,
            'data'   => MyTransactionResource::collection($transactions),
        ], 200);
    }

}
