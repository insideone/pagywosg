const
    Encore = require('@symfony/webpack-encore'),
    HtmlWebpackPlugin = require('html-webpack-plugin')
;

Encore
    .setOutputPath('public/build/')
    .enableVersioning(Encore.isProduction())
    .setPublicPath('/build')
    .enableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(false)
    .addEntry('main', './src/Frontend/main.js')
    .enableVueLoader()
    .enableLessLoader()
    .addPlugin(new HtmlWebpackPlugin({
        title: 'Welcome!',
        filename: '../index.html',
        //favicon: '/favicon.png',
        meta: {
            viewport: 'width=940',
        },
    }))
;

module.exports = Encore.getWebpackConfig();
