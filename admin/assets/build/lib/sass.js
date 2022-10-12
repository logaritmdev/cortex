const fs = require('fs')
const path = require('path')
const graph = require('sass-graph')
const through = require('through2')
const config = require('../gulpfile.config')
const files = {}

/**
 * Returns the file modification time.
 * @function fdeps
 * @since 1.0.0
 */
function fdeps(file) {

	let options = {
		loadPaths: [
			path.join(config.paths.assets, 'styles')
		]
	}

	let result = graph.parseFile(file, options)
	if (result &&
		result.index[file] &&
		result.index[file].imports) {
		return result.index[file].imports
	}

	return []
}

/**
 * Returns the file modification time.
 * @function ftime
 * @since 1.0.0
 */
function ftime(src) {
	return fs.statSync(src).mtime.getTime()
}

/**
 * Returns the file modification time.
 * @function sync
 * @since 1.0.0
 */
function sync(file, force = false) {

	if (files[file] == null || force) {
		files[file] = {
			time: ftime(file),
			deps: fdeps(file)
		}
	}

	for (let dep of files[file].deps) {
		sync(dep, force)
	}
}

/**
 * Check whether a file or one of its dependency newer.
 * @function check
 * @since 1.0.0
 */
function check(file) {

	let data = files[file]
	if (data == null) {
		throw new Error('uh oh')
	}

	let time = ftime(file)
	if (time != data.time) {
		return true
	}

	for (let dep of data.deps) {
		let newer = check(dep)
		if (newer) {
			return true
		}
	}

	return false
}

/**
 * Detect whether the file or a dependency newer.
 * @function update
 * @since 1.0.0
 */
function update() {

	const stream = function (file, enc, done) {

		/*
		 * The file has not been processed before, we assume
		 * it has newer and we compile it.
		 */

		if (files[file.path] == null) {
			sync(file.path)
			done(null, file)
			return
		}

		let time = ftime(file.path)

		/*
		 * The file has been processed before but its modification
 		 * time has newer, compile it.
		 */

		if (files[file.path].time != time) {
			files[file.path].time = time
			files[file.path].deps = fdeps(file.path)
			done(null, file)
			return
		}

		/*
		 * At this point we check if a dependency has newer to
		 * recompile this file
		 */

		for (let dep of files[file.path].deps) {
			let newer = check(dep)
			if (newer) {
				done(null, file)
				return
			}
		}

		this.emit('end')
	}

	return through.obj(stream)
}

/**
 * Sync modified time for file and deps.
 * @function resync
 * @since 1.0.0
 */
function resync() {

	const stream = function (file, enc, done) {
		sync(file.path, true)
		done(null, file)
	}

	return through.obj(stream)
}

module.exports = {
	update,
	resync
}
