<?php

namespace App\Http\Controllers\Api\v1;

use App\Enums\RequestWithdrawStatus;
use App\Http\Controllers\BackendController;
use App\Http\Requests\RequestWithdrawRequest;
use App\Http\Resources\v1\RequestWithdrawResource;
use App\Models\RequestWithdraw;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RequestWithdrawController extends BackendController
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        $queryArray = [];
        if (auth()->user()->myrole != 1) {
            $queryArray['user_id'] = auth()->id();
        }

        $requestWithdraws = RequestWithdraw::where($queryArray)->latest()->get();
        return response()->json([
            'status' => 200,
            'data'   => RequestWithdrawResource::collection($requestWithdraws),
        ], 200);
    }

    public function store(Request $request)
    {
        $rules     = new RequestWithdrawRequest;
        $validator = Validator::make($request->all(), $rules->rules());

        $validator->after(function ($validator) use ($rules) {
            $rules->checkBalanceAmount(0, $validator);
        });

        if ($validator->fails()) {
            return response()->json([
                'status'  => 422,
                'message' => $validator->errors(),
            ], 422);
        }

        $requestWithdraw          = new RequestWithdraw;
        $requestWithdraw->user_id = auth()->id();
        $requestWithdraw->amount  = $request->amount;
        $requestWithdraw->status  = RequestWithdrawStatus::PENDING;
        $requestWithdraw->date    = $request->date;
        $requestWithdraw->save();

        return response()->json([
            'status'  => 200,
            'message' => 'The data created successfully.',
            'data'    => new RequestWithdrawResource($requestWithdraw),
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $queryArray = [];
        if (auth()->user()->myrole != 1) {
            $queryArray['user_id'] = auth()->id();
        }

        $requestWithdraw = RequestWithdraw::find($id);
        if (!blank($requestWithdraw)) {
            return response()->json([
                'status' => 200,
                'data'   => new RequestWithdrawResource($requestWithdraw),
            ], 200);
        } else {
            return response()->json([
                'status'  => 401,
                'message' => 'The request withdraw not found.',
            ], 401);
        }
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
        $rules     = new RequestWithdrawRequest;
        $validator = Validator::make($request->all(), $rules->rules());

        $validator->after(function ($validator) use ($rules, $id) {
            $rules->checkBalanceAmount($id, $validator);
        });

        if ($validator->fails()) {
            return response()->json([
                'status'  => 422,
                'message' => $validator->errors(),
            ], 422);
        }

        $requestWithdraw         = RequestWithdraw::where('status', RequestWithdrawStatus::PENDING)->find($id);
        $requestWithdraw->amount = $request->amount;
        $requestWithdraw->date   = $request->date;
        if (auth()->user()->myrole == 1) {
            $requestWithdraw->status = $request->status;
        }
        $requestWithdraw->save();

        return response()->json([
            'status'  => 200,
            'message' => 'The data updated successfully.',
            'data'    => new RequestWithdrawResource($requestWithdraw),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        $requestWithdraw = RequestWithdraw::where('status', RequestWithdrawStatus::PENDING)->find($id);
        if (!blank($requestWithdraw)) {
            $requestWithdraw->delete();
            return response()->json([
                'status'  => 200,
                'message' => 'The data deleted successfully',
            ], 200);
        }
        return response()->json([
            'status'  => 401,
            'message' => 'You can\'t delete this data',
        ], 401);
    }
}
