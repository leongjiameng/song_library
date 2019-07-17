<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Song extends Model
{
    protected $fillable = [
        'title', 'genre','year','length'
    ];


    public function artists(){
        return $this->belongsToMany(Artist::class);
    }

    public function playlists(){
        return $this->belongsToMany(Playlist::class);
    }



}
