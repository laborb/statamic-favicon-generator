const mix = require('laravel-mix');

mix.js('resources/js/cp.js', 'dist/js/favicon-generator.js').vue({ version: 3 });

mix.webpackConfig({
	externals: {
		vue: 'Vue',
	},
});
