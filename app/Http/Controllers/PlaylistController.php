<?php

namespace App\Http\Controllers;

use App\Http\Repositories\PlaylistRepository as Repo;
use App\Playlist;
use Input;

class PlaylistController extends Controller
{

    public function __construct()
    {
        $this->repo = new Repo;
    }
    
    public function show($id)
    {
        return Playlist::with('songs')->findOrFail($id);
    }

    public function create()
    {
        $search_term = Input::get('search_term');
        $playlist = $this->repo->create($search_term);
        $playlist->load('songs');
        return $playlist;
    }

    public function next($id)
    {
        $playlist = $this->repo->next($id);
        $playlist->load('songs');
        return $playlist;
    }

}

