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
                    <button class="btn btn-primary" type="button" @click="new"> π </button>
                </span>
            </div>
            <div class="splash" v-show="playlist === undefined"></div>
            <div v-show="playlist !== undefined">
                <div id="player"></div>
                <progress class="progress" max="100" v-bind:value="time_percent" @click="seek($event)"> @{{ time_percent }}% </progress>
                <div class="controls text-center">
                    <div class="btn-group">
                        <button type="button" class="btn btn-secondary" @click="prev"><span class="fa fa-fast-backward"></span></button>
                        <button type="button" class="btn btn-secondary" @click="togglePlay">
                            <span class="fa fa-play" v-if="status === 'paused'"></span>
                            <span class="fa fa-pause" v-else></span>
                        </button>
                        <button type="button" class="btn btn-secondary" @click="next"><span class="fa fa-fast-forward"></span></button>
                    </div>
                </div>
            </div>
            <span class="logo" v-show="playlist !== undefined"></span>
        </div>
        <script>
            Vue.config.debug = true;
            var app = new Vue({
                el: '.app',
                data: {
                    search_term: '',
                    playlist: undefined,
                    current_song: -1,
                    player: undefined,
                    status: '',
                    time: -1
                },
                created: function() {
                    this.$emit('load-player');
                },
                computed: {
                    time_percent: function() {
                        return 100 * (this.time / this.duration);
                    }
                },
                methods: {
                    new: function() {
                        var data = {'search_term': this.search_term};
                        this.$http.post('/api/playlist', data, function (response, status, request) {
                            this.playlist = response;
                            this.current_song = 0;
                            this.$emit('reload-playlist');
                        });
                    },
                    next: function() {
                        this.current_song++;
                        if (this.current_song == this.playlist.songs.length) {
                            var url = '/api/playlist/' + this.playlist.id + '/next';
                            this.$http.get(url, function (response, status, request) {
                                this.playlist = response;
                                this.$emit('reload-playlist');
                            });
                        } else {
                            this.$emit('reload-playlist');
                        }
                    },
                    prev: function() {
                        this.current_song--;
                        if (this.current_song < 0) {
                            this.current_song = 0;
                        }
                        this.$emit('reload-playlist');
                    },
                    togglePlay: function() {
                        if (this.player.getPlayerState() == YT.PlayerState.PAUSED) {
                            this.player.playVideo();
                        } else {
                            this.player.pauseVideo();
                        }
                    },
                    seek: function(event) {
                        var percent = event.offsetX / event.target.offsetWidth;
                        var seek_to = percent * this.player.getDuration();
                        this.player.seekTo(seek_to);
                    }
                },
                events: {
                    'reload-playlist': function() {
                        var song = this.playlist.songs[this.current_song];
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
                        var self = this;
                        setInterval(function() {
                            self.time = !!self.player.getCurrentTime ? self.player.getCurrentTime() : -1;
                            self.duration = !!self.player.getDuration ? self.player.getDuration() : -1;
                        }, 100);
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
                    app.status = 'ended';
                } else if (event.data == YT.PlayerState.PLAYING) {
                    app.status = 'playing';
                } else if (event.data == YT.PlayerState.PAUSED) {
                    app.status = 'paused';
                }
            }
        </script>
    </body>
</html>
