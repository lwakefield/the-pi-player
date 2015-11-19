<?php

use App\Http\Repositories\PlaylistRepository as Repo;

class PlaylistTest extends \Codeception\TestCase\Test
{

    public function _before()
    {
        $this->repo = new Repo;
    }

    public function testCreate()
    {
        $result = $this->repo->create('radiohead');
        $this->assertNotEmpty($result);
        $this->assertNotEmpty($result->songs);
    }
    
    public function testNext()
    {
        $playlist = $this->repo->create('radiohead');
        $result = $this->repo->next($playlist->id);
        $this->assertEquals(count($result->songs), 2);
    }
}
