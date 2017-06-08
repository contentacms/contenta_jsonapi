'use strict';

var path = require('path'),
    importOnce = require('node-sass-import-once');

var options = {};

// #############################
// Edit these paths and options.
// #############################

options.drupalURL = 'http://127.0.0.1:8888';
// The root paths are used to construct all the other paths in this
// configuration. The "project" root path is where this gulpfile.js is located.
// While Zen distributes this in the theme root folder, you can also put this
// (and the package.json) in your project's root folder and edit the paths
// accordingly.
options.rootPath = {
  project     : __dirname + '/',
  styleGuide  : __dirname + '/styleguide/',
  theme       : __dirname + '/'
};

options.theme = {
  name       : 'materialize_contenta',
  root       : options.rootPath.theme,
  css        : options.rootPath.theme + 'css/',
  sass       : options.rootPath.theme + 'sass/',
  js         : options.rootPath.theme + 'js/'
};

// Define the node-sass configuration. The includePaths is critical!
options.sass = {
  importer: importOnce,
  includePaths: [
    options.theme.css
  ],
  outputStyle: 'expanded'
};

// Define which browsers to add vendor prefixes for.
options.autoprefixer = {
  browsers: [
    '> 1%',
    'ie 9'
  ]
};

// If your files are on a network share, you may want to turn on polling for
// Gulp watch. Since polling is less efficient, we disable polling by default.
// The default interval is 100 ms. If that leads to excessive cpu usage
// try to set a higher value.
options.gulpWatchOptions = {};
// options.gulpWatchOptions = {interval: 1000, mode: 'poll'};
// options.gulpWatchOptions = {interval: 600};

// #############################
// Edit these Materialize URLs.
// #############################
options.materialize = {
  css: 'materialize-v0.98.2.zip',
  src: 'materialize-src-v0.98.2.zip',
  zip: 'materialize-src-v0.98.2.zip',
  url: 'http://materializecss.com/bin/'
}

// ################################
// Load Gulp and tools we will use.
// ################################
var gulp      = require('gulp'),
  $           = require('gulp-load-plugins')(),
  // browserSync = require('browser-sync').create(),
  del         = require('del'),
  // gulp-load-plugins will report "undefined" error unless you load gulp-sass manually.
  sass        = require('gulp-sass'),
  source      = require('vinyl-source-stream'),
  request     = require('request'),
  runSequence = require('run-sequence');

// ################################
// Download Materialize tasks
// ################################
gulp.task('materialize-download', function() {
   return request(options.materialize.url + '/' + options.materialize.zip)
   .pipe(source(options.materialize.zip))
   .pipe(gulp.dest('./'))
});

gulp.task('materialize-unzip', function() {
    return gulp.src(options.materialize.zip)
    .pipe($.decompress({strip: 1}))
    .pipe(gulp.dest('./'))
});

gulp.task('materialize-zip-cleanup', function() {
    return del(options.materialize.zip);
});

gulp.task('materialize-set:src', function() {
   options.materialize.zip = options.materialize.src;
   return 1;
});

gulp.task('materialize-set:css', function() {
   options.materialize.zip = options.materialize.css;
   return 1;
});

gulp.task('materialize-install:css', function() {
    runSequence('materialize-set:css', 'materialize-download', 'materialize-unzip', 'materialize-zip-cleanup', function() {
        console.log('Materialize library installed');
        done();
    });
});

gulp.task('materialize-install:src', function() {
    runSequence('materialize-set:src', 'materialize-download', 'materialize-unzip', 'materialize-zip-cleanup', 'materialize-fix-src', function() {
        console.log('Materialize library installed');
        done();
    });
    // , 'materialize-fix-src'
});

options.materialize.fix = {
  date_old   : "/components/date_picker/_default.date.scss",
  date_new   : "/components/date_picker/_default_date.scss",
  time_old   : "/components/date_picker/_default.time.scss",
  time_new   : "/components/date_picker/_default_time.scss",
  main_file  : 'materialize.scss'
};

gulp.task('materialize-fix-src', function() {
  runSequence('materialize-fix-src:rename', 'materialize-fix-src:replace' , function() {
    // todo: this cause error on full install.
    // , 'materialize-fix-src:del'
    console.log('Materialize library files fixed.');
    return 1;
  });
});

/*gulp.task('materialize-fix-src2', gulp.series('materialize-fix-src:rename', 'materialize-fix-src:replace', function(done) {
  // do more stuff
  gulp.start('materialize-fix-src:del');
  done();
}));*/


gulp.task('materialize-fix-src:rename', function(done) {
  gulp.src(options.theme.sass + options.materialize.fix.date_old)
    .pipe($.rename(options.materialize.fix.date_new))
    .pipe(gulp.dest(options.theme.sass));

  gulp.src(options.theme.sass + options.materialize.fix.time_old)
    .pipe($.rename(options.materialize.fix.time_new))
    .pipe(gulp.dest(options.theme.sass));

  done();
});

gulp.task('materialize-fix-src:replace', function(done) {
  gulp.src(options.theme.sass + options.materialize.fix.main_file)
    .pipe($.replace(/(".*[^.])(\.)(.*[^.]")/g, '$1_$3'))
    .pipe(gulp.dest(options.theme.sass));

  done();
});

gulp.task('materialize-fix-src:del', function(done) {
  del(options.theme.sass + options.materialize.fix.date_old);
  del(options.theme.sass + options.materialize.fix.time_old);
  done()
});

// The default task.
gulp.task('default', ['help']);

gulp.task('help', function() {
  console.log('materialize-install:css - Install compiled Materialize library.');
  console.log('materialize-install:src - Install Materialize SASS source library.');
  console.log('watch - Watch the changes of SASS files and compile to CSS');
  console.log('styles - Compile SASS files to CSS');
  console.log('compile - Compile SASS files to CSS (alias of styles)');
  console.log('styles:production - Compile SASS files to CSS');
  console.log('clean - Clean up CSS files');
});

// ##########
// Build CSS.
// ##########
var sassFiles = [
  options.theme.sass + '**/*.scss',
  // Do not open Sass partials as they will be included as needed.
  '!' + options.theme.sass + '**/_*.scss'
];

var sassFilesWatch = [
  options.theme.sass + '**/*.scss'
];

// Alias of styles.
gulp.task('compile', ['styles']);

gulp.task('styles', function () {
  return gulp.src(sassFiles)
    .pipe($.sourcemaps.init())
    .pipe(sass(options.sass).on('error', sass.logError))
    .pipe($.autoprefixer(options.autoprefixer))
    .pipe($.rename({dirname: ''}))
    .pipe($.size({showFiles: true}))
    .pipe($.sourcemaps.write('./'))
    .pipe(gulp.dest(options.theme.css));
});

gulp.task('styles:production', ['clean:css'], function () {
  return gulp.src(sassFiles)
    .pipe(sass(options.sass).on('error', sass.logError))
    .pipe($.autoprefixer(options.autoprefixer))
    .pipe($.rename({dirname: ''}))
    .pipe($.size({showFiles: true}))
    .pipe(gulp.dest(options.theme.css));
});

// ##############################
// Watch for changes and rebuild.
// ##############################
gulp.task('watch', ['watch:css']);

gulp.task('watch:css', ['clean:css', 'styles'], function () {
  gulp.watch(sassFilesWatch, options.gulpWatchOptions, ['styles']);
});

// ######################
// Clean all directories.
// ######################
gulp.task('clean', ['clean:css']);

// Clean CSS files.
gulp.task('clean:css', function () {
  del([
    options.theme.css + '**/*.css',
    options.theme.css + '**/*.map'
  ], {force: true});
});
