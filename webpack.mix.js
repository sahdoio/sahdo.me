let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for your application, as well as bundling up your JS files.
 |
 */

 /** 
  * Generic
  */

mix.styles([
    'resources/assets/vendor/font-awesome.min.css',
    'resources/assets/vendor/noscript.css'
], 'public/vendor/vendor.css');

mix.copyDirectory('resources/assets/fonts', 'public/fonts');

mix.scripts([
    'resources/assets/vendor/jquery.min.js',
    'resources/assets/vendor/browser.min.js',
    'resources/assets/vendor/breakpoints.min.js',
    'resources/assets/vendor/util.js'
], 'public/vendor/vendor.js');

/** 
 * Website
 */

mix.styles([
    'resources/assets/website/css/main.css'
], 'public/website/css/app.css');

mix.scripts([
    'resources/assets/website/js/main.js'
], 'public/website/js/app.js');


/** 
 * Admin
 */

/*

Under development

mix.styles([
    'resources/assets/vendor/bootstrap/css/bootstrap.css',
    'resources/assets/admin/css/now-ui-dashboard.css',
    'resources/assets/admin/css/pages/login.css'
], 'public/admin/css/login.css');
*/

mix.scripts('resources/assets/admin/js/pages/login.js', 'public/admin/js/login.js');




