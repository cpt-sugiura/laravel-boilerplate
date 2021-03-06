// eslint-disable-next-line no-undef,@typescript-eslint/no-var-requires
const mix = require('laravel-mix');
// eslint-disable-next-line no-undef,@typescript-eslint/no-var-requires
const path = require('path');
// eslint-disable-next-line no-undef,@typescript-eslint/no-var-requires
// const BundleAnalyzerPlugin = require('webpack-bundle-analyzer').BundleAnalyzerPlugin;
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
  resolve: {alias: {'@': path.resolve(__dirname, 'resources/js')}},
  plugins: [
    // new BundleAnalyzerPlugin(), // 出力ファイルが巨大になった時にどこを削るかの目安に使うプラグイン
  ],
}).setPublicPath('')
    .ts('resources/js/admin/app.tsx', 'storage/app/assets/admin/js')
    .sass('resources/sass/admin/app.scss', 'storage/app/assets/admin/css')
    .ts('resources/js/admin/_WhenNotLogin/app.tsx', 'public/assets/admin/js/login.js')
    .sass('resources/sass/admin/login.scss', 'public/assets/admin/css/login.css')
    .ts('resources/js/member/register/app.tsx', 'public/assets/member/js/register.js')
    .sass('resources/sass/member/register.scss', 'public/assets/member/css/register.css')
    .ts('resources/js/member/passwordReset/app.tsx', 'public/assets/member/js/password_reset.js');
