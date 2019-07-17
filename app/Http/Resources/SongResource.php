<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class SongResource extends Resource
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
            'id' => $this->when(!is_null($this->id), $this->id),
            'title' => $this->when(!is_null($this->title), $this->title),
            'year' => $this->when(!is_null($this->year), $this->year),
            'length' => $this->when(!is_null($this->length), $this->length),
            'genre' => $this->when(!is_null($this->genre), $this->genre),
            'artists' => ArtistResource::collection($this->whenLoaded('artists')),
        ];
    }
}
