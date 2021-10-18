const {
    mix
} = require("laravel-mix");
require("laravel-mix-merge-manifest");

if (mix.inProduction()) {
    var publicPath = 'publishable/assets';
} else {
    var publicPath = "../../../public/vendor/techkken/cashier/assets";
}

mix.setPublicPath(publicPath).mergeManifest();
mix.disableNotifications();


mix.sass(__dirname + "/src/Resources/assets/sass/app.scss", "css/techkken-cashier.css")
    .options({
        processCssUrls: false
    });

mix.js(__dirname + "/src/Resources/assets/js/app.js", "js/techkken-cashier.js")
    .copyDirectory(__dirname + '/src/Resources/assets/images', publicPath + '/images')

if (mix.inProduction()) {
    mix.version();
}