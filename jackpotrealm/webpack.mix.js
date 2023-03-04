const mix = require('laravel-mix');
const tailwindcss = require('tailwindcss')

require('laravel-mix-bundle-analyzer');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/app/js/app.js').vue()
    .js('resources/js/widgets/echoWidget.js', 'public/app/js/echoWidget.js')
    .scripts('resources/js/lib.js', 'public/app/js/lib.js')
    .scripts('resources/js/player/ovenplayer.js', 'public/app/js/player.js')
    .scripts('resources/js/player/DashProvider_HlsProvider.js', 'public/app/js/DashProvider_HlsProvider.js')
    .scripts('resources/js/player/WebRTCProvider.js', 'public/app/js/WebRTCProvider.js')
    .sass('resources/sass/async.scss', 'public/app/css')
    .sass('resources/sass/app.scss', 'public/app/css')
    .options({
        processCssUrls: false,
        postCss: [ tailwindcss('./tailwind.config.js') ],
    })

mix.bundleAnalyzer();
