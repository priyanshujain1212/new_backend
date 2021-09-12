<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class RequestWithdrawResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'           => $this->id,
            'name'         => $this->user->name,
            'currency_amount' => currencyFormat($this->amount),
            'amount'       => $this->amount,
            'date_db_style' => $this->date,
            'date'         => $this->date->format('d M Y'),
            'status'       => $this->status,
            'status_label' => $this->status_label,
            'created_at'   => $this->created_at->format('d M Y'),
            'updated_at'   => $this->updated_at->format('d M Y'),
        ];
    }
}
