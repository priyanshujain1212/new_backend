<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class MyTransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            "id"         => $this['increment_id'],
            "type"       => $this['type'],
            "date"       => $this['date'],
            "amount"     => $this['amount'],
        ];
    }
}
