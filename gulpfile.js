'use strict';

var gulp = require('gulp');
var uglify = require('gulp-uglify');
var rename = require('gulp-rename');
var all = require('gulp-all');
var del = require("del");
var cleanCss = require("gulp-clean-css");
var concat = require('gulp-concat');
var sourcemaps = require('gulp-sourcemaps');
var bower = require("gulp-bower");
var sass = require("gulp-sass");
var watch = require('gulp-watch');
var batch = require('gulp-batch');
var browserSync = require('browser-sync');

var path = {
    bower_src: "bower_components/",
    lib: "dist/",
    font_lib: "dist/fonts/",
    js_lib: "dist/js/",
    css_lib: "dist/css/",
    js_lib_pre: "dist/js/pre/",
    css_lib_pre: "dist/css/pre/"
};

var config = {
    jquery_src: [
        path.bower_src + "jquery/dist/jquery.js",
        path.bower_src + "jquery-stupid-table/stupidtable.js"
    ],
    jquery_bundle: "_jquery-bundle.js",
    semantic_js_src: [
        path.bower_src + "semantic-ui/dist/semantic.js"
    ],
    semantic_js_bundle: "_semantic-bundle.js",
    semantic_css_src: [
        path.bower_src + "semantic-ui/dist/semantic.css"
    ],
    semantic_css_bundle: "_semantic-bundle.css",
    semantic_theme_folder_src: path.bower_src + "semantic-ui/dist/themes/**/*",
    semantic_theme_folder_dest: "css/themes",
    before_bundling_jobs: ["bower-restore"],

    framework_sass_src: [
        "Src/Content/sass/*.sass"
    ],
    framework_css_watch: "Src/Content/sass/**/*.sass",
    framework_css_bundle: "php-frame-bundle.css",

    framework_js_src: [
        "Src/Content/js/**/*.js"
    ],
    framework_js_watch: "Src/Content/sass/**/*.js",
    framework_js_bundle: "php-frame-bundle.js",
    font_src_folder: "Src/Content/fonts/**/*"
};
var collections = {
    all_css_files: [
        path.css_lib_pre + config.semantic_css_bundle,
        path.css_lib_pre + config.framework_css_bundle
    ],

    all_js_files: [
        path.js_lib_pre  + config.jquery_bundle,
        path.js_lib_pre + config.semantic_js_bundle
        //"php-frame-bundle.js"
    ]
};


// clean directory
gulp.task("clean-scripts", function () {
    del([path.lib]);
});

//restore bower packages
gulp.task("bower-restore", function () {
    return bower();
});

//Create a jquery bundled file
gulp.task("jquery-bundle", config.before_bundling_jobs, function () {
    return gulp.src(config.jquery_src)
        .pipe(concat(config.jquery_bundle))
        .pipe(gulp.dest(path.js_lib_pre));
});

//Create a semantic bundled file
gulp.task("semantic-bundle", config.before_bundling_jobs, function () {
    return all(gulp.src(config.semantic_js_src)
        .pipe(concat(config.semantic_js_bundle))
        .pipe(gulp.dest(path.js_lib_pre)),
        gulp.src(config.semantic_css_src)
            .pipe(concat(config.semantic_css_bundle))
            .pipe(gulp.dest(path.css_lib_pre)),
        gulp.src(config.semantic_theme_folder_src)
            .pipe(gulp.dest(path.lib + config.semantic_theme_folder_dest)))
});

// create bundles
gulp.task("create-bundles", ["jquery-bundle", "semantic-bundle"], function () {

});

//combine & minify css
gulp.task("combine-minify-css", function () {
    return gulp.src(collections.all_css_files)
        .pipe(concat("styles.css"))
        .pipe(gulp.dest(path.css_lib))
        .pipe(rename("styles.min.css"))
        .pipe(cleanCss())
        .pipe(gulp.dest(path.css_lib))
        .pipe(browserSync.reload({
            stream: true
        }));
});

//combine & minify js
gulp.task("combine-minify-js", function () {
    return gulp.src(collections.all_js_files)
        .pipe(sourcemaps.init())
        .pipe(concat("scripts.js"))
        .pipe(gulp.dest(path.js_lib))
        .pipe(rename("scripts.min.js"))
        .pipe(uglify())
        .pipe(sourcemaps.write("./"))
        .pipe(gulp.dest(path.js_lib))
        .pipe(browserSync.reload({
            stream: true
        }));
});

gulp.task("copy-fonts", function () {
    return gulp.src(config.font_src_folder)
        .pipe(gulp.dest(path.font_lib));
});

//build js
gulp.task("compile-framework-js", function () {
    //do nothing for now
});

//build sass
gulp.task("compile-framework-sass", function () {
    return gulp.src(config.framework_sass_src)
        .pipe(sourcemaps.init())
        .pipe(sass())
        .pipe(gulp.dest(function (file) {
            return file.base;
        }))
        .pipe(concat(config.framework_css_bundle))
        .pipe(gulp.dest(path.css_lib_pre));
});

//build js
gulp.task("compile-js", ["create-bundles"], function () {
    gulp.start('combine-minify-js');
});

//build css
gulp.task("compile-css", ["create-bundles", "compile-framework-sass"], function () {
    gulp.start('combine-minify-css');
});

gulp.task("copy-files", ["copy-fonts"], function () {
});

//build css
gulp.task("framework-css-recompile", ["compile-framework-sass"], function () {
    gulp.start('combine-minify-css');
});

//build css
gulp.task("framework-js-recompile", ["compile-framework-js"], function () {
    gulp.start('combine-minify-js');
});

gulp.task('browser-sync', function () {
    browserSync.init({
        proxy: "http://localhost:8080/php-frame/Examples/Layout/"
    });
});

gulp.task('default', ['compile-css', 'compile-js', 'copy-files']);

gulp.task('watch', ["browser-sync"], function () {
    watch(config.framework_css_watch, function () {
        gulp.start('framework-css-recompile');
    });
    watch(config.framework_js_watch, function () {
        gulp.start('framework-js-recompile');
    });

});

gulp.task('clean', ["clean-scripts"], function () {
});