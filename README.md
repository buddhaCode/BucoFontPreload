# BucoFontPreload
Shopware plugin to preload font assets

## Features
This plugin enables preloading for Shopware's standard fonts. You can select which of the Shopware fonts will be loaded. Additionally, you can specify custom fonts as well. With fonts preloading the performance of your page can be improved. The font assets are known to the browser in advance and doesn't need to be resolved through the CSS file. As a result, the browser can load the font assets faster.

For further information refer to the [Google Web Font Optimization page](https://developers.google.com/web/fundamentals/performance/optimizing-content-efficiency/webfont-optimization?hl=en#optimizing_loading_and_rendering) please.

## Technical information
This plugins appends the `frontend_index_header_favicons` template block. So please be sure, that this block isn't replaced in your theme. Also, the template variables `$SHOPWARE_VERSION`, `$SHOPWARE_VERSION_TEXT` and `$SHOPWARE_REVISION` will be set for all frontend controllers.

## Compatibility
* PHP >= 7.0
* Shopware >= 5.2.0
* Compatible with [Performance Improvements](https://store.shopware.com/en/frosh31872894918f/performance-improvements.htm)'s "Remove shopware revision from font path" option

## Installation

### Git Version
* Checkout plugin in `/custom/plugins/BucoFontPreload`
* Install and active plugin with the Plugin Manager

### Install with composer
* Change to your root installation of Shopware
* Run command `composer require buddha-code/buco-font-preload`
* Install and active plugin with `./bin/console sw:plugin:install --activate BucoFontPreload`

## Contributing
Feel free to fork and send pull requests!

## Licence
This project uses the [GPLv3 License](LICENCE).
