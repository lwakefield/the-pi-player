<?php

namespace App;

use FastValidate\BaseModel;

class Playlist extends BaseModel
{

    protected $fillable = ['session_id', 'search_term', 'songs'];

    public function songs()
    {
        return $this->hasMany('App\Song');
    }

}
