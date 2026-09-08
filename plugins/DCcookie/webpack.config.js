const path = require('path');

module.exports = {
    mode: 'production',
    entry: {
        app: ['./index.js']
    },
    output: {
        filename: 'main.min.js',
        path: path.resolve(__dirname, 'dist'),
    },
    // optimization: {
    //     minimize: false
    // },
    module: {
        rules: [
            {
                test: /\.js$/,
                exclude: /node_modules/
            }
        ]
    }
};
