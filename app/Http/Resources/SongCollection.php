<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class SongCollection extends ResourceCollection
{
     /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'data'=>SongResource::collection($this->collection),
            'meta'=>[
                'time'=>date('U'),
            ],
        ];
    }
}
