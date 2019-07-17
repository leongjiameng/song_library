<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Playlist extends Model
{
    protected $fillable = [
        'name'
    ];

    public function songs(){
        return $this->belongsToMany(Song::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }


}
