<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PhleboResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        //return parent::toArray($request);
        return [
            'id'=>$this->id,
            'first_name'=>$this->first_name,
            'sirname'=>$this->sirname,
            'last_name'=>$this->last_name,
            'gender'=>$this->gender,
            'email'=>$this->email,
            'phone'=>$this->phone,
            'dob'=>$this->dob
        ];
    }
}
