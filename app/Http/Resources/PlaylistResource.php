<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class PlaylistResource extends Resource
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
            'name' => $this->when(!is_null($this->name), $this->name),
            'songs' => SongResource::collection($this->whenLoaded('songs')),
            'user' => new UserResource($this->whenLoaded('user')),
          
        ];
    }
}
