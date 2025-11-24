const defaultConfig = require('@wordpress/scripts/config/webpack.config');

module.exports = {
    ...defaultConfig,
    entry: {
        'bloc-marche-public': './src/bloc-marche-public/index.js',
        'bloc-actes-officiels': './src/bloc-actes-officiels/index.js',
        'bloc-trombinoscope': './src/bloc-trombinoscope/index.js',
    }
};