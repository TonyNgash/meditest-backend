<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
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
            'test_name'=>$this->test_name,
            'test_price'=>$this->test_price,
            'test_constituents'=>$this->test_constituents,
            'test_category'=>$this->test_category,
            'test_prerequisites'=>$this->test_prerequisites,
            'test_report_availability'=>$this->test_report_availability,
            'test_desc'=>$this->test_desc,
            'image_path'=>$this->image_path,
            'home'=>$this->home
        ];
    }

}
