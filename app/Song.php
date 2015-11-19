<?php

namespace App;

use FastValidate\BaseModel;

class Song extends BaseModel
{

    protected $fillable = ['song_id', 'artist_id', 'title', 'artist_name', 'video_id'];
    protected $appends = ['full_name'];

    public function playlist()
    {
        return $this->belongsTo('App\Playlist');
    }

    public function getFullNameAttribute()
    {
        return $this->artist_name.' - '.$this->title;
    }

}
