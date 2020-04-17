<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'email' => $this->email,
            'id' => $this->id,
            'name' => $this->name,
            'roles' => $this->roles->makeHidden(['created_at', 'description', 'id', 'pivot', 'updated_at']),
            'created_at' => (string) $this->created_at,
            'updated_at' => (string) $this->updated_at,
            'account' => $this->account->makeHidden(['created_at', 'updated_at']),
        ];
    }
}
