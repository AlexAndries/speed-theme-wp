var gulp = require('gulp'),
  $ = require('gulp-load-plugins')({
    pattern: ['gulp-*']
  }),
  gutil = require('gulp-util'),
  cleanCSS = require('gulp-clean-css'),
  sassUnicode = require('gulp-sass-unicode'),
  errorHandler = function(title) {
    'use strict';
    
    return function(err) {
      gutil.log(gutil.colors.red('[' + title + ']'), err.toString());
      this.emit('end');
    };
  };

gulp.task('build-css', function() {
  var sassOptions = {
    outputStyle: 'compressed'
  };
  
  return gulp.src('sass/**/*.scss')
             .pipe($.sass(sassOptions)).on('error', errorHandler('Sass'))
             .pipe(sassUnicode())
             .pipe($.autoprefixer()).on('error', errorHandler('Autoprefixer'))
             .pipe(cleanCSS({compatibility: 'ie10'}))
             .pipe(gulp.dest('css'));
});

gulp.task('watch', ['build-css'], function() {
  gulp.watch('sass/**/*.scss', ['build-css']);
});
