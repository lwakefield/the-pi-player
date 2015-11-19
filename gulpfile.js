var elixir = require('laravel-elixir');
require('laravel-elixir-ruby-sass');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Less
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(function(mix) {
    mix.rubySass('app.scss', 'public/css', {
        includePaths: [
            __dirname + '/vendor/bower_components/bootstrap/scss',
            __dirname + '/vendor/bower_components/bootstrap/scss/mixins',
            __dirname + '/vendor/bower_components/fontawesome/scss',
        ]
    });
    mix.copy('vendor/bower_components/fontawesome/fonts/', 'public/fonts');
    mix.copy('resources/assets/imgs', 'public/imgs');
    mix.copy([
        'vendor/bower_components/vue/dist/vue.js',
        'vendor/bower_components/vue-resource/dist/vue-resource.min.js',
        'resources/assets/js',
        ], 'public/js/');
});
