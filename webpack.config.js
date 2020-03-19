var Encore = require('@symfony/webpack-encore');

if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .addStyleEntry('lib', './assets/scss/lib.scss')
    .addStyleEntry('app', './assets/scss/app.scss')
    .addEntry('request', './assets/jsx/request.jsx')
    .addEntry('request-vulnerable', './assets/jsx/request-vulnerable.jsx')
    .addEntry('helper', './assets/jsx/helper.jsx')
    .addEntry('captcha', './assets/js/captcha.js')
    .splitEntryChunks()
    .enableSingleRuntimeChunk()

    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = 3;
    })
    .enableSassLoader()
    .enablePreactPreset()
;

module.exports = Encore.getWebpackConfig();
