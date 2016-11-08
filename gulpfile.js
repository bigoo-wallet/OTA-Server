var gulp           = require("gulp"),
    mainBowerFiles = require("gulp-main-bower-files"),
    uglify         = require("gulp-uglify"),
    minifyCss      = require('gulp-clean-css'),
    clean          = require('gulp-clean'),
    sourcemaps     = require('gulp-sourcemaps'),

    distbasePath   = "www/web",
    assetsbasePath = distbasePath+"/assets",

    srcStatic      = "./frontend_src/static/*",
    srcJs          = "./frontend_src/**/*.js",
    srcCss         = "./frontend_src/**/*.css",

    bowerFile      = "./bower.json",

    tasks          = [
                      "uglify-js-libs", 
                      "uglify-css-src",
                      "uglify-js",
                      "copy-static-file"
                    ];

/**
 * clear libraries
 */
gulp.task("lib-clean", function(){
  return gulp.src(assetsbasePath+"/libs", {read: false})
    .pipe(clean({force: true}));
});

/**
 * clear static file
 */
gulp.task("static-clean", function(){
  return gulp.src(assetsbasePath+"/static", {read: false})
    .pipe(clean({force: true}));
});

/**
 * clear dev css
 */
gulp.task("css-clean", function(){
  return gulp.src(assetsbasePath+"/css", {read: false})
    .pipe(clean({force: true}));
});

/**
 * clear dev js
 */
gulp.task("js-clean", function(){
  return gulp.src(assetsbasePath+"/js", {read: false})
    .pipe(clean({force: true}));
});

/**
 * clear dev source map
 */
gulp.task("map-clean", function(){
  return gulp.src(assetsbasePath+"/maps", {read: false})
    .pipe(clean({force: true}));
});

/**
 * clear dev html
 */
gulp.task("html-clean", function(){
  return gulp.src(distbasePath+"/*.html", {read: false})
    .pipe(clean({force: true}));
});

/**
 * pick up the library files
 */
gulp.task("pickup-bower-files", ["lib-clean"], function(){
  var conf = {
    overrides: {

      "AdminLTE": {
        main: [
          "./bootstrap/css/*.min.*",
          "./bootstrap/js/*.min.*",
          "./bootstrap/fonts/*.*",
          "./dist/css/**/*.min.*",
          "./dist/js/*.min.*",
          "./dist/img/**/*.*",
          "./plugins/**/*.*"
        ]
      },

      "bootstrap-datepicker": {
        main: [
          "./dist/js/bootstrap-datepicker.min.js",
          "./dist/css/*.min.*",
          "./dist/css/*.min.map"

        ]
      },

      "font-awesome": {
        main: [
          "./css/*.min.*",
          "./css/*.map",
          "./fonts/*.*"
        ]
      },

      "html5shiv": {
        main: [
          "./dist/*.min.js"
        ]
      },

      "respond": {
        main: [
          "./dest/*.min.js"
        ]
      }
    }
  };
  return gulp.src("./bower.json")
    .pipe(mainBowerFiles(conf))
    .pipe(gulp.dest(assetsbasePath+"/libs"));
});

/**
 * uglify libraries js
 */
gulp.task("uglify-js-libs", ["pickup-bower-files"], function(){
    
  return gulp.src(assetsbasePath+"/libs/**/*.js")
    .pipe(uglify())
    .pipe(gulp.dest(assetsbasePath+"/libs"));
});

/**
 * uglify libraries js
 */
gulp.task("uglify-js", function(){
    
  return gulp.src(srcJs)
    .pipe(uglify())
    .pipe(gulp.dest(assetsbasePath));
});

/**
 * uglify css file from src
 */
gulp.task("uglify-css-src", ["css-clean", "map-clean"], function(){
  return gulp.src(srcCss)
    .pipe(minifyCss())
    .pipe(gulp.dest(assetsbasePath));
});

/**
 * copy static file
 */
gulp.task("copy-static-file", ["static-clean"], function(){
  return gulp.src(srcStatic)
    .pipe(gulp.dest(assetsbasePath+"/static/"));
});


gulp.task("default", tasks, function() {    
  gulp.watch([srcStatic, srcJs, srcCss, bowerFile], function(e){
    gulp.run(tasks);
  });
});