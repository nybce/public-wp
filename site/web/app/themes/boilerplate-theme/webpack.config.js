// We are using node's native package 'path'
// https://nodejs.org/api/path.html
const path = require("path");

const webpack = require("webpack"); // reference to webpack Object
// using the newer beta version for >= Webpack 4
// the current version is only good for <= Webpack 3

// Constant with our paths
const paths = {
  DIST: path.resolve(__dirname, "dist"),
  SRC: path.resolve(__dirname, "src"),
};
const ExtractTextPlugin = require("extract-text-webpack-plugin");
const PrettierPlugin = require("prettier-webpack-plugin");
const extractSass = new ExtractTextPlugin({
  filename: "style.min.css",
  allChunks: true,

});
// Webpack configuration
module.exports = {
  entry: [path.join(paths.SRC, "index.js")],
  output: {
    path: paths.DIST,
    filename: "main.bundle.js",
  },
  watch: true,
  // Adding jQuery as external library
  externals: {
    jquery: "jQuery",
  },
  // Tell webpack to use html plugin -> ADDED IN THIS STEP
  // index.html is used as a template in which it'll inject bundled app.
  plugins: [
    new webpack.ProvidePlugin({
      $: "jquery",
      jQuery: "jquery",
      Popper: "popper.js",
    }),
    extractSass,
    new PrettierPlugin({
      printWidth: 80,
      tabWidth: 2,
      useTabs: false,
      semi: true,
      singleQuote: true,
      extensions: [ ".js", ".scss" ]
    }),
  ],
  // Loaders configuration -> ADDED IN THIS STEP
  // We are telling webpack to use "babel-loader" for .js and .jsx files
  module: {
    rules: [
      {
        test: /\.(js|jsx)$/,
        exclude: /node_modules/,
        use: ["babel-loader"],
      },
      {
        test: /\.scss$/,
        use: extractSass.extract({
          use: [
            {
              loader: "css-loader",
              options: {
                minimize: true,
              },
            },
            {
              loader: "sass-loader",
            },
          ],
          // use style-loader in development
          fallback: "style-loader",
        }),
      },
      {
        test: /\.css$/,
        loader: "style-loader",
      },
      {
        test: /\.css$/,
        loader: "css-loader",
        options: {
          minimize: true,
        },
      },
    ],
  },
  // Enable importing JS files without specifying their's extenstion -> ADDED IN THIS STEP
  //
  // So we can write:
  // import MyComponent from './my-component';
  //
  // Instead of:
  // import MyComponent from './my-component.jsx';
  resolve: {
    extensions: [".js", ".jsx", ".scss"],
  },
};
