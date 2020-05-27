<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */


    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'amount' => $this->amount,
            'type' => $this->type,
            'status' => $this->status,
            'sender-account' => $this->sender_accound_id,
            'recipient-account' => $this->recipient_account_id,
            'sender-id' => $this->sender_user_id,
            'recipient-id' => $this->recipient_user_id,
            'created-at' => $this->created_at->diffForHumans()
        ];
    }
}
