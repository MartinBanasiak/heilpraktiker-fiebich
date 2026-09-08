const path = require('path');

module.exports = {
    mode: 'production',
    entry: {
        app: ['./index-ie.js']
    },
    output: {
        filename: 'ie.min.js',
        path: path.resolve(__dirname, 'dist'),
    },
    // optimization: {
    //     minimize: false
    // },
    module: {
        rules: [
            {
                test: /\.js$/,
                use: {
                    loader: 'babel-loader',
                    options: {
                        presets: [
                            [
                                "@babel/preset-env",
                                {
                                    "corejs": {"version": 3},
                                    "useBuiltIns": "usage",
                                    "targets": {
                                        "edge": "17",
                                        "firefox": "60",
                                        "chrome": "67",
                                        "safari": "11.1",
                                        "ie": "11"
                                    }
                                }
                            ]
                        ]
                    }
                },
                exclude: /node_modules/
            }
        ]
    }
};
