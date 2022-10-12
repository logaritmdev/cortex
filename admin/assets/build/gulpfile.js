const fs = require('fs')
const path = require('path')
const gulp = require('gulp')
const gulpif = require('gulp-if')
const bro = require('gulp-bro')
const clean = require('gulp-clean')
const cssnano = require('gulp-cssnano')
const imagemin = require('gulp-imagemin')
const flatten = require('gulp-flatten')
const webp = require('gulp-webp')
const merge = require('merge-stream')
const babelify = require('babelify')
const browserSync = require('browser-sync').create()
const update = require('./lib/sass').update
const resync = require('./lib/sass').resync

/*
 * Loads external configurations.
 */

const config = require('./gulpfile.config')

/*
 * Sets the sass compiler to dart sass. This is required since this codebase
 * use recent features and wouldn't work otherwise.
 */

const sass = require('gulp-dart-sass')

//------------------------------------------------------------------------------
// Clean Task
//------------------------------------------------------------------------------

gulp.task('clean', function (done) {

	let merged = merge()

	let keys = {}
	let dirs = []

	const append = (dir) => {
		if (keys[dir] == null) {
			keys[dir] = true
			dirs.push(dir)
		}
	}

	append(path.join(config.paths.dist, 'scripts'))
	append(path.join(config.paths.dist, 'styles'))

	dirs.forEach(dir => {

		merged.add(
			gulp.src(dir, { read: false, allowEmpty: true })
				.pipe(clean())
		)

	})

	return merged
})

//------------------------------------------------------------------------------
// Fonts
//------------------------------------------------------------------------------

gulp.task('fonts', function (done) {

	const write = () => {
		return gulp.dest(path.join(config.paths.dist, 'fonts'))
	}

	return gulp.src(path.join(config.paths.assets, 'fonts/**/*'))
		.pipe(flatten())
		.pipe(write())
		.pipe(browserSync.stream())
})

//------------------------------------------------------------------------------
// Images
//------------------------------------------------------------------------------

gulp.task('images', function (done) {

	let merged = merge()

	const build = () => {
		return gulpif(config.features.optimize, imagemin([
			imagemin.svgo({
				plugins: [
					{ removeViewBox: false },
					{ cleanupIDs: false }
				]
			})
		]))
	}

	const convert = () => {
		return webp()
	}

	const write = () => {
		return gulp.dest(path.join(config.paths.dist, 'images'))
	}

	merged.add(
		gulp.src(path.join(config.paths.assets, 'images/**/*'))
			.pipe(build())
			.pipe(convert())
			.pipe(write())
			.pipe(browserSync.stream())
	)

	return merged
})

//------------------------------------------------------------------------------
// Styles
//------------------------------------------------------------------------------

gulp.task('styles', function () {

	const build = () => {

		return sass({

			includePaths: [
				path.join(config.paths.assets, 'styles')
			]

		}).on('error', sass.logError)

	}

	const minify = () => {
		return gulpif(config.features.optimize, cssnano({
			zindex: false,
			reduceIdents: false,
			discardUnused: false
		}))
	}

	const write = (dst) => {
		return gulp.dest(dst)
	}

	let merged = merge()

	config.files.styles.forEach(file => {

		let { src, dst } = file

		merged.add(
			gulp.src(src)
				.pipe(update())
				.pipe(build())
				.pipe(minify())
				.pipe(write(dst))
				.pipe(browserSync.stream())
		)

	})

	return merged

})

gulp.task('styles-resync', function () {

	let merged = merge()

	config.files.styles.forEach(file => {

		let { src, dst } = file

		merged.add(
			gulp.src(src)
				.pipe(resync())
		)

	})

	return merged

})

//------------------------------------------------------------------------------
// Scripts
//------------------------------------------------------------------------------

gulp.task('scripts', function () {

	const build = () => {

		let options = {
			transform: [
				babelify.configure({
					presets: [
						'@babel/preset-env'
					]
				})
			]
		}

		if (config.prod) {
			options.transform.push([
				'uglifyify', {
					global: true
				}
			])
		}

		return bro(options)
	}

	const write = (dst) => {
		return gulp.dest(dst)
	}

	let merged = merge()

	config.files.scripts.forEach(file => {

		let { src, dst } = file

		merged.add(
			gulp.src(src)
				.pipe(build())
				.pipe(write(dst))
		)

	})

	return merged

})

//------------------------------------------------------------------------------
// Build
//------------------------------------------------------------------------------

gulp.task('build', gulp.series('clean', 'styles', 'scripts', 'fonts', 'images', function (done) {
	done()
}))

//------------------------------------------------------------------------------
// Watch
//------------------------------------------------------------------------------

gulp.task('watch', function () {

	browserSync.init({

		open: false,

		files: [
			path.join(config.paths.root, 'resources/**/*.twig'),
			path.join(config.paths.root, 'resources/**/*.php'),
			path.join(config.paths.root, 'app/**/*.php')
		],

		watchOptions: {
			ignoreInitial: true,
			ignored: ['node_modules']
		},

		proxy: config.urls.local,

		snippetOptions: {
			whitelist: ['/wp-admin/admin-ajax.php'],
			blacklist: ['/wp-admin/**']
		}

	})

	gulp.watch([
		path.join(config.paths.root, 'resources/assets/images/**/*'),
	], gulp.series('images', 'reload'))

	gulp.watch([
		path.join(config.paths.root, 'resources/assets/styles/**/*.scss'),
	], gulp.series('styles', 'styles-resync'))

	gulp.watch([
		path.join(config.paths.root, 'resources/assets/scripts/**/*.js'),
	], gulp.series('scripts', 'reload'))

})

//------------------------------------------------------------------------------
// Reload
//------------------------------------------------------------------------------

gulp.task('reload', function (done) {
	browserSync.reload()
	done()
})