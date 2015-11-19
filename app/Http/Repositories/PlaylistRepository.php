<?php

namespace App\Http\Repositories;

use App\Playlist;
use Echonest\Service\Echonest;
use FastModelFactory\FastModelFactory as Factory;
use Madcoda\Youtube;

class PlaylistRepository
{

    public function __construct()
    {
        Echonest::configure(getenv('ECHONEST_API_KEY'));
        $this->youtube = new Youtube(['key' => getenv('YOUTUBE_DATA_API_KEY')]);
    }
    

    public function create($search_term)
    {
        $response = Echonest::query('playlist/dynamic', 'create', [
            'artist' => $search_term,
            'type' => 'artist-radio',
            'results' => 1
        ]);

        $song = head(object_get($response, 'response.songs'));
        $video_id = $this->findVideoId("$song->artist_name - $song->title");
        $data = [
            'session_id' => object_get($response, 'response.session_id'),
            'search_term' => $search_term,
            'songs' => [
                [
                    'song_id' => object_get($song, 'id'),
                    'artist_id' => object_get($song, 'artist_id'),
                    'title' => object_get($song, 'title'),
                    'artist_name' => object_get($song, 'artist_name'),
                    'video_id' => $video_id
                ]
            ]
        ];
        $playlist = Factory::create(Playlist::class, $data);
        return $playlist;
    }

    public function next($id)
    {
        $playlist = Playlist::find($id);
        $response = Echonest::query('playlist/dynamic', 'next', [
            'session_id' => $playlist->session_id,
            'results' => 1
        ]);
        $song = head(object_get($response, 'response.songs'));
        $video_id = $this->findVideoId("$song->artist_name - $song->title");
        $data = [
            'id' => $playlist->id,
            'songs' => [
                [
                    'song_id' => object_get($song, 'id'),
                    'artist_id' => object_get($song, 'artist_id'),
                    'title' => object_get($song, 'title'),
                    'artist_name' => object_get($song, 'artist_name'),
                    'video_id' => $video_id
                ]
            ]
        ];
        $playlist = Factory::update(Playlist::class, $data);
        return $playlist;
    }
    

    private function findVideoId($search_term)
    {
        $result = $this->youtube->searchAdvanced([
            'q' => $search_term,
            'type' => 'video',
            'part' => 'id, snippet',
            'maxResults' => 1,
            'videoCategoryId' => 10
        ]);
        return head($result)->id->videoId;
    }

}

