const mix = require('laravel-mix');

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
mix.webpackConfig({
  plugins: []
}).js('resources/js/home.js', 'public/js')
  .sass('resources/sass/home.scss', 'public/css')
  .js('resources/js/blog.js', 'public/js')
  .sass('resources/sass/blog.scss', 'public/css').version();
