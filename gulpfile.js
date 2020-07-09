
// Requirements
var gulp = require('gulp');
var plumber = require('gulp-plumber');
var sass = require('gulp-sass');
var watch = require('gulp-watch');
var rename = require('gulp-rename');
var cleanCSS = require('gulp-clean-css');
var gulpSequence = require('gulp-sequence');
var runSequence = require('run-sequence');

// Default task
gulp.task('default', function(callback) {
	runSequence('sass',
				'minify-css',
				callback);
});

// Watch task
gulp.task('watch', function () {
    gulp.watch('./sass/**/*.scss', ['styles']);
});

// SASS
gulp.task('sass', function () {
    var stream = gulp.src('./sass/style.scss')
        .pipe(plumber({
            errorHandler: function (err) {
                console.log(err);
                this.emit('end');
            }
        }))
        .pipe(sass())
        .pipe(gulp.dest('./css'));
    return stream;
});

// CSS minification
gulp.task('minify-css', function() {
  return gulp.src('./css/style.css')
    .pipe(cleanCSS({compatibility: '*'}))
    .pipe(plumber({
            errorHandler: function (err) {
                console.log(err);
                this.emit('end');
            }
        }))
    .pipe(rename({suffix: '.min'}))
    .pipe(gulp.dest('./css/'));
});

// Run style sequence
gulp.task('styles', function(callback){
	gulpSequence('sass', 'minify-css')(callback);
});
