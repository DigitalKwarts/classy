var gulp = require('gulp');
var shell = require('gulp-shell');
var bump = require('gulp-bump');

/**
 * Install Dependencies
 */

gulp.task('install', shell.task([
    'cd framework && composer install',
]));

/**
 * Your Custom Tasks
 */

gulp.task('default', function(){
    // gulp.run();
});