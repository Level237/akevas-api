<?php

namespace App\Http\Controllers\Product\Payment;

use Illuminate\Http\Request;
use App\Jobs\PaymentProcessingJob;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PaymentProductProcessingController extends Controller
{
    public function handlePaymentProduct(Request $request){
        $userId=Auth::guard('api')->user()->id;
        Log::info('PaymentProductProcessingController: handlePaymentProduct', [
            'request' => $request->all(),
            'userId' => $userId
        ]);
        //PaymentProcessingJob::dispatch($request,$userId);

        return response()->json([
            'message' => 'Payment processing job dispatched',
            'data' => $request->all()
        ]);

    }
}
