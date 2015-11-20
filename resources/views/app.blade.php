<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width">
        <title>The Pi Player</title>

        <link rel="stylesheet" href="{{ asset('css/app.css') }}" type="text/css">
        <script type="text/javascript" src="{{ asset('js/vue.js') }}"></script>
        <script type="text/javascript" src="{{ asset('js/vue-resource.min.js') }}"></script>
    </head>
    <body>
        <div class="app container">
            <div class="search input-group">
                <input type="text" class="form-control" placeholder="Search for an artist" v-model="search_term" @keyup.enter="new">
                <span class="input-group-btn">
                    <button class="btn btn-primary" type="button" @click="new"> Go </button>
                </span>
            </div>
            <div class="splash" v-show="playlist === undefined"></div>
            <div v-show="playlist !== undefined">
                <div id="player"></div>
            </div>
            <span class="logo" v-show="playlist !== undefined"></span>
        </div>
        <script>
            var app = new Vue({
                el: '.app',
                data: {
                    search_term: '',
                    playlist: undefined,
                    player: undefined
                },
                created: function() {
                    this.$emit('load-player');
                },
                methods: {
                    new: function() {
                        var data = {'search_term': this.search_term};
                        this.$http.post('/api/playlist', data, function (response, status, request) {
                            this.playlist = response;
                            this.$emit('load-playlist');
                        });
                    },
                    next: function() {
                        var url = '/api/playlist/' + this.playlist.id + '/next';
                        this.$http.get(url, function (response, status, request) {
                            this.playlist = response;
                            this.$emit('load-playlist');
                        });
                    }
                },
                events: {
                    'load-playlist': function() {
                        var song = this.playlist.songs[this.playlist.songs.length - 1];
                        this.player.loadVideoById(song.video_id);
                    },
                    'load-player': function() {
                        var tag = document.createElement('script');
                        tag.src = "https://www.youtube.com/player_api";
                        var firstScriptTag = document.getElementsByTagName('script')[0];
                        firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
                    },
                    'loaded-player': function(player) {
                        this.player = player;
                        this.player.addEventListener(YT.PlayerState.ENDED, 'ended');
                    },
                    'video-ended': function() {
                        this.next();
                    }
                }
            });

            function onYouTubePlayerAPIReady() {
                var player = new YT.Player('player', {
                    height: '390',
                    width: '100%',
                    events: {
                        'onStateChange': onPlayerStateChange
                    }
                });
                app.$emit('loaded-player', player);
            }

            function onPlayerStateChange(event) {
                if (event.data == YT.PlayerState.ENDED) {
                    app.$emit('video-ended');
                }
            }
        </script>
    </body>
</html>
