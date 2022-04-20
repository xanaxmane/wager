const mix = require('laravel-mix'), _ = require('lodash');
const WebpackShellPlugin = require('webpack-shell-plugin'),
      TerserPlugin = require('terser-webpack-plugin'),
      CompressionPlugin = require('compression-webpack-plugin'),
      LodashPlugin = require('lodash-webpack-plugin'),
      OptimizeCssAssetsPlugin = require('optimize-css-assets-webpack-plugin');
      //BundleAnalyzerPlugin = require('webpack-bundle-analyzer').BundleAnalyzerPlugin;

const assets = {
    js: [
        'bootstrap.js',
        'app.js',
        'pages/index.js',
        'pages/user.js',
        'pages/help.js',
        'pages/bonus.js',
        'pages/fairness.js',
        'pages/partner.js',
        'pages/wallet.js',

        'pages/dice.js',
        'pages/wheel.js',
        'pages/plinko.js',
        'pages/keno.js',
        'pages/mines.js',
        'pages/coinflip.js',
        'pages/blackjack.js',
        'pages/roulette.js',
        'pages/diamonds.js',
        'pages/hilo.js',
        'pages/limbo.js',
        'pages/crash.js',
        'pages/stairs.js',
        'pages/tower.js',
        'pages/videopoker.js',
        'pages/slide.js',
        'pages/slots.js',

        'admin/app.js',
        'admin/pages/dashboard.js',
        'admin/pages/promo.js',
        'admin/pages/settings.js',
        'admin/pages/notifications.js',
        'admin/pages/users.js',
        'admin/pages/user.js',
        'admin/pages/wallet.js',
        'admin/pages/modules.js',
        'admin/pages/currency.js',
        'admin/pages/aggregators.js',
        'admin/pages/bankdeposits.js'
    ],
    sass: [
        'app.scss',
        'loader.scss',
        'pages/index.scss',
        'pages/user.scss',
        'pages/error.scss',
        'pages/help.scss',
        'pages/bonus.scss',
        'pages/job.scss',
        'pages/fairness.scss',
        'pages/partner.scss',
        'pages/wallet.scss',

        'pages/dice.scss',
        'pages/wheel.scss',
        'pages/plinko.scss',
        'pages/keno.scss',
        'pages/mines.scss',
        'pages/coinflip.scss',
        'pages/blackjack.scss',
        'pages/roulette.scss',
        'pages/diamonds.scss',
        'pages/hilo.scss',
        'pages/limbo.scss',
        'pages/crash.scss',
        'pages/stairs.scss',
        'pages/tower.scss',
        'pages/videopoker.scss',
        'pages/slide.scss',
        'pages/slots.scss',

        'admin/app.scss'
    ],
    copy: [
        'img',
        'webfonts',
        'sounds'
    ]
};

const compile = function(assets, callback, data) {
    _.forEach(assets, function(file) {
        callback(`${data.src}/${file}`, `${data.dist}/${file}`);
    });
};

compile(assets.js, (from, to) => mix.js(from, to).version(), {
    'src': 'resources/js',
    'dist': 'public/js'
});

compile(assets.sass, (from, to) => mix.sass(from, to.replace('.scss', '.css')).version(), {
    'src': 'resources/sass',
    'dist': 'public/css'
});

compile(assets.copy, (from, to) => mix.copy(from, to).version(), {
    'src': 'resources',
    'dist': 'public'
});

mix.copy('resources/js/sw.js', 'public')
   .copy('resources/manifest.json', 'public/manifest.json');

mix.webpackConfig({
    plugins: [
        new WebpackShellPlugin({
            onBuildStart: ['php artisan cache:clear --quiet'],
            onBuildEnd: []
        }),
        //new BundleAnalyzerPlugin(),
        new OptimizeCssAssetsPlugin({
            assetNameRegExp: /\.optimize\.css$/g,
            cssProcessor: require('cssnano'),
            cssProcessorPluginOptions: {
                preset: ['default', { discardComments: { removeAll: true } }],
            },
            canPrint: true
        }),
        new LodashPlugin(),
        /*new TerserPlugin({
            parallel: true,
            cache: false,
            terserOptions: {
                ecma: 8,
                warnings: false
            },
        }),*/
        new CompressionPlugin({
            filename: '[path].gz[query]',
            algorithm: 'gzip',
            test: /\.js$|\.css$|\.html$|\.eot?.+$|\.ttf?.+$|\.woff?.+$|\.svg?.+$/,
            threshold: 10240,
            minRatio: 0.8,
            cache: false
        })
    ]
});
