var gulp      = require('gulp');
const jshint  = require('gulp-jshint');
const stylish = require('jshint-stylish');
var gulpCopy  = require('gulp-copy');
var replace   = require('gulp-replace');
var $         = require('gulp-load-plugins')();
refresh       = require('gulp-refresh');

// provide a paht to node modules 
var sassPaths = [
  'node_modules'
];

//get materialize min libary
gulp.task('libsrc', function() {
return gulp.src([
  'node_modules/materialize-css/dist/js/materialize.min.js',
  ])
  .pipe(gulpCopy('js/vendor',{prefix: 4}));
});

// rename the autocomplete function as it conflicts with jqueryUI and then move to source control folder js/lib
// (Core and too many libraries rely on jquery autocomplete, replacing it would likely cause too many issues)
gulp.task('rename', function(){
  gulp.src(['js/vendor/materialize.min.js'])
    .pipe(replace('fn.autocomplete', 'fn.autocomplete_materialize'))
    .pipe(gulp.dest('js/lib'));
});

// Grab all the other plugin js and add to project
gulp.task('copy', function() {
return gulp.src([
  'node_modules/materialize-clockpicker/dist/js/materialize.clockpicker.js',
  ])
  .pipe(gulpCopy('js/lib',{prefix: 4}));
});

gulp.task('sass', function() {
  return gulp.src(['scss/material_admin.scss'])
    .pipe($.sass({
      sourceComments: 'map',
      sourceMap: 'sass',
      includePaths: sassPaths,
      outputStyle: 'nested'
    })
    .on('error', $.sass.logError))
    .pipe($.autoprefixer({
      browsers: ['last 2 versions', 'ie >= 9']
    }))
    .pipe(gulp.dest('css'))
    .pipe(refresh());
});

gulp.task('lint', function() {
  return gulp.src(['./js/*.js', '!./js/vendor.all.js'])
    .pipe(jshint())
    .pipe(jshint.reporter('jshint-stylish'));
});

gulp.task('default', ['sass'], function() {
  refresh.listen()
  gulp.watch(['scss/**/*.scss'], ['sass']);
});
