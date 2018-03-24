var gulp = require('gulp');
var concat = require('gulp-concat');
var minifyCSS = require('gulp-minify-css');
var uglify = require('gulp-uglify');
var sass = require('gulp-sass');
var csso = require('gulp-csso');

gulp.task('js', function(){
	return gulp.src(["src/*.js"])
		.pipe(concat('script.min.js'))
		.pipe(uglify())
		.pipe(gulp.dest('./js'));
});

gulp.task('sass', function () {
	return gulp.src('./src/*.scss')
		.pipe(sass().on('error', sass.logError))
		.pipe(gulp.dest('./src'));
});

gulp.task('css', function(){
	return gulp.src(["src/*.css"])
		.pipe(concat('style.min.css'))
		.pipe(minifyCSS())
		.pipe(csso())
		.pipe(gulp.dest('./css'));
});

gulp.task('default', ['js', 'sass', 'css']);
