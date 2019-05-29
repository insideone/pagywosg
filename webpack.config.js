var Encore = require('@symfony/webpack-encore');

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .enableSingleRuntimeChunk()
    //.disableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    //.enableSourceMaps(!Encore.isProduction())
    .enableSourceMaps(false)
    .addEntry('main', './src/Frontend/main.js')
    // Enable Vue loader
    .enableVueLoader()
    .enableLessLoader()
;

var config = Encore.getWebpackConfig();

module.exports = config;