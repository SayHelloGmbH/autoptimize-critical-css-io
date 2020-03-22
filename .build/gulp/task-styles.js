import {src, dest} from 'gulp';

import cleanCSS from 'gulp-clean-css';
import filter from 'gulp-filter';
import postCSS from 'gulp-postcss';
import autoprefixer from 'autoprefixer';
import postCSSNested from 'postcss-nested';
import postCSSImport from 'postcss-import';
import rename from 'gulp-rename';
import livereload from 'gulp-livereload';
import sourcemaps from 'gulp-sourcemaps';

export const task = config => {

	return src(config.assetsBuild + 'styles/*.css')
		.pipe(sourcemaps.init())
		.pipe(postCSS([
			autoprefixer(),
			postCSSImport(),
			postCSSNested()
		]))
		.pipe(sourcemaps.write({includeContent: false}))
		.pipe(sourcemaps.init({loadMaps: true}))
		.pipe(dest(config.assetsDir + 'styles/'))
		.pipe(sourcemaps.write('.'))
		.on('error', config.errorLog)
		// minify
		.pipe(cleanCSS())
		.pipe(rename({
			suffix: '.min'
		}))
		.on('error', config.errorLog)
		.pipe(dest(config.assetsDir + 'styles/'))
		//reload
		.pipe(filter('**/*.css'))
		.pipe(livereload());
};
