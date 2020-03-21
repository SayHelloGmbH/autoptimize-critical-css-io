import gulp from 'gulp';
import livereload from 'gulp-livereload';

const config = {
	name: 'Autoptimize critical-css.io',
	key: 'hello-aoccss',
	assetsDir: 'assets/',
	gulpDir: './.build/gulp/',
	assetsBuild: '.build/assets/',
	errorLog: function (error) {
		console.log('\x1b[31m%s\x1b[0m', error);
		if (this.emit) {
			this.emit('end');
		}
	},
	reload: [
		'*.php',
		'src/**/*.{php,html}'
	]
};

import {task as taskStyles} from './.build/gulp/task-styles';
import {task as taskScripts} from './.build/gulp/task-scripts';
import {task as taskReload} from './.build/gulp/task-reload';

export const styles = () => taskStyles(config);
export const scripts = () => taskScripts(config);
export const reload = () => taskReload(config);
export const watch = () => {

	const settings = {usePolling: true, interval: 100};

	livereload.listen();

	gulp.watch(config.assetsBuild + 'styles/**/*.css', settings, gulp.series(styles));
	gulp.watch(config.assetsBuild + 'scripts/**/*.js', settings, gulp.series(scripts));
	gulp.watch(config.reload).on('change', livereload.changed);
};


export const taskDefault = gulp.series(gulp.parallel(styles, scripts, reload), watch);
export default taskDefault;
