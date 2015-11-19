<?php

use FastMigrate\FastMigrator;

class Base extends FastMigrator
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $I = $this;

        $I->wantATable('playlists')
            ->withStrings('session_id', 'search_term');
        $I->wantATable('songs')
            ->withStrings('song_id', 'artist_id', 'title', 'artist_name', 'video_id');
        $I->want('playlists')
            ->toHaveMany('songs');
        $I->amReadyForMigration();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('playlists');
        Schema::dropIfExists('songs');
    }
}
