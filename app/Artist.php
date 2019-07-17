<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Artist extends Model
{
    protected $fillable = [
        'name', 'type'
    ];

    public function songs(){
        return $this->belongsToMany(Song::class);
    }
    
}
