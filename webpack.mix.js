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

mix.copy('node_modules/admin-lte/node_modules/select2/dist/js/select2.full.js', 'public/js/select2.js')
    .js('resources/js/app.js', 'public/js')
    .js(`resources/js/index.js`, "public/js")
    .js(`resources/js/invoice-index.js`, "public/js")
    .js(`resources/js/supplier.js`, "public/js")
    .js(`resources/js/invoice-batch.js`, "public/js")
    .js(`resources/js/invoice.js`, "public/js")
    .js(`resources/js/dashboard.js`, "public/js")
    .js(`resources/js/company.js`, "public/js")
    .js(`resources/js/currency.js`, "public/js")
    .js(`resources/js/user.js`, "public/js")
    .js(`resources/js/xero.js`, "public/js")
    .js(`resources/js/creditnotes.js`, "public/js")
    .js(`resources/js/nav.js`, "public/js")
    // .js(`resources/js/summary.js`, "public/js")
    .sass('resources/sass/app.scss', 'public/css')
    .vue()
    .version();
