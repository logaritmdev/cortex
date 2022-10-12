const fs = require('fs')
const path = require('path')
const util = require('gulp-util')

/**
 * The root directory.
 * @const root
 * @since 1.0.0
 */
const root = process.cwd()

/**
 * Wheher the environment is production.
 * @const prod
 * @since 1.0.0
 */
const prod = !!util.env.production

/**
 * Absolutes paths.
 * @const paths
 * @since 1.0.0
 */
const paths = {
	root: path.resolve(root),
	dist: path.resolve(root),
	assets: path.resolve(root, 'assets'),
}

module.exports = {

	prod: prod,

	paths: paths,

	files: {

		styles: [{
			src: path.join(paths.root, 'assets/styles/main.scss'),
			dst: path.join(paths.dist, 'styles')
		}],

		scripts: [{
			src: path.join(paths.root, 'assets/scripts/main.js'),
			dst: path.join(paths.dist, 'scripts')
		}]

	},

	urls: {
		proxy: 'http://localhost:3000',
		local: 'http://EXAMPLE.test'
	},

	features: {
		prefix: prod,
		optimize: prod,
		sourcemap: true
	}
}