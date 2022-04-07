/* ---
  Docs: https://www.npmjs.com/package/mati-mix/
--- */
const mix = require('mati-mix');

// NPS
mix.js( [ 'assets-src/nps/js/index.jsx' ], 'assets/js/nps.js' );
mix.sass( 'assets-src/nps/scss/style.scss', 'assets/css/nps.css' );

mix.mix.babelConfig({
	"presets": [
		"@babel/preset-env",
		"@babel/preset-react"
	],
});

mix.mix.webpackConfig({
	externals: {
		"@wordpress/i18n": ["wp", "i18n"]
	}
});
