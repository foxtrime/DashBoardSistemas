let mix = require('laravel-mix');

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

mix.sass('resources/assets/sass/app.scss',     'public/css')
    .sass('resources/assets/sass/pdf.scss',     'public/css')
    .version();

mix.js('resources/assets/js/app.js', 'public/js')
    .sourceMaps();
   
mix.copyDirectory('vendor/twbs/bootstrap-sass/assets/fonts/bootstrap', 'public/fonts/bootstrap');

mix.copyDirectory('resources/assets/vendor/bootstrap-datetimepicker',   'public/bootstrap-datetimepicker');
mix.copyDirectory('resources/assets/vendor/datatables',                 'public/datatables');
mix.copyDirectory('resources/assets/vendor/pdfmake',                    'public/pdfmake');
mix.copyDirectory('resources/assets/vendor/icheck',                     'public/icheck');

//mix.copyDirectory('node_modules/inputmask', 'public/inputmask');

mix.scripts([
    // Pntify
    'node_modules/gentelella/vendors/pnotify/dist/pnotify.js',
    'node_modules/gentelella/vendors/pnotify/dist/pnotify.buttons.js'
],  'public/js/components.js'); 