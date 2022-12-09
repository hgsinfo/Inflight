/* eslint-disable no-undef */

const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

module.exports = {
  entry: {
    elementor: path.join(__dirname, 'src', 'entries', 'elementor.ts'),
    gutenberg: path.join(__dirname, 'src', 'entries', 'gutenberg.ts'),
    leadin: path.join(__dirname, 'src', 'entries', 'app.ts'),
    menu: path.join(__dirname, 'src', 'entries', 'menu.ts'),
    feedback: path.join(__dirname, 'src', 'entries', 'feedback.ts'),
    reviewBanner: path.join(__dirname, 'src', 'entries', 'reviewBanner.ts'),
  },
  output: {
    path: path.join(__dirname, 'dist'),
    filename: '[name].js',
    libraryTarget: 'window',
    library: ['wp', '[name]'],
  },
  mode: process.env.NODE_ENV || 'development',
  externals: [
    {
      jquery: 'jQuery',
      lodash: 'lodash',
      react: 'React',
      'react-dom': 'ReactDOM',
    },
    function wp(context, request, callback) {
      if (/^@wordpress\//.test(request)) {
        const arr = request.split('/');
        arr[1] = arr[1].replace(/-[a-z]/g, x => x[1].toUpperCase());
        arr[0] = 'wp';
        return callback(null, `var ${arr.join('.')}`);
      }
      return callback();
    },
  ],
  resolve: {
    modules: [path.resolve(__dirname, 'src'), 'node_modules'],
    extensions: ['.ts', '.tsx', '.js', '.json'],
  },
  devServer: { static: { directory: path.join(__dirname, 'src') } },
  module: {
    rules: [
      {
        test: /\.(t|j)sx?$/,
        use: [
          { loader: 'babel-loader' },
          {
            loader: '@linaria/webpack-loader',
            options: {
              sourceMap: process.env.NODE_ENV !== 'production',
            },
          },
        ],
      },
      {
        test: /\.css$/i,
        use: [MiniCssExtractPlugin.loader, 'css-loader'],
      },
      {
        test: /\.(jpg|jpeg|png|gif|mp3|svg)$/,
        use: ['file-loader'],
      },
    ],
  },
  plugins: [new MiniCssExtractPlugin({ filename: '[name].css' })],
};
