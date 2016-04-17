'use strict';

var gulp = require('gulp');
var uglify = require('gulp-uglify');
var rename = require('gulp-rename');
var all = require('gulp-all');
var del = require("del");
var minifyCSS = require("gulp-minify-css");
var concat = require('gulp-concat');
var sourcemaps = require('gulp-sourcemaps');
var bower = require("gulp-bower");

var path = {
    src: "bower_components/",
    lib: "dist",
    js_lib: "dist/js/",
    css_lib: "dist/css/"
};

var config = {
    jquery_src: [
        path.src + "jquery/dist/jquery.js",
        path.src + "jquery-stupid-table/stupidtable.js"
    ],
    jquery_bundle: "jquery-bundle.js",
    semantic_js_src: [
        path.src + "semantic-ui/dist/semantic.js"
    ],
    semantic_js_bundle: "semantic-bundle.js",
    semantic_css_src: [
        path.src + "semantic-ui/dist/semantic.css"
    ],
    semantic_css_bundle: "semantic-bundle.css",
    before_bundling_jobs: ["clean-scripts", "bower-restore"]
};

// Synchronously delete the output script file(s)
gulp.task("clean-scripts", function () {
    del([path.lib]);
});

//Create a jquery bundled file
gulp.task("jquery-bundle", config.before_bundling_jobs, function () {
    return gulp.src(config.jquery_src)
        .pipe(concat(config.jquery_bundle))
        .pipe(gulp.dest(path.js_lib));
});

//Create a jquery bundled file
gulp.task("semantic-bundle", config.before_bundling_jobs, function () {
    return all(gulp.src(config.semantic_js_src)
        .pipe(concat(config.semantic_js_bundle))
        .pipe(gulp.dest(path.js_lib)),
        gulp.src(config.semantic_css_src)
            .pipe(concat(config.semantic_css_bundle))
            .pipe(gulp.dest(path.css_lib)))
});

// Combine and the vendor files from bower into bundles (output to the Scripts folder)
gulp.task("bundle-scripts", ["jquery-bundle", "semantic-bundle"], function () {

});

//Restore all bower packages
gulp.task("bower-restore", function () {
    return bower();
});

//build lib scripts
gulp.task("compile-js", ["bundle-scripts"], function () {
    return gulp.src(path.js_lib + "*.js")
        .pipe(sourcemaps.init())
        .pipe(concat("scripts.js"))
        .pipe(gulp.dest(path.js_lib))
        .pipe(rename("scripts.min.js"))
        .pipe(uglify())
        .pipe(sourcemaps.write("./"))
        .pipe(gulp.dest(path.js_lib));
});

//build lib scripts
gulp.task("compile-css", ["bundle-scripts"], function () {
    return gulp.src(path.js_lib + "*.css")
        .pipe(concat("styles.css"))
        .pipe(gulp.dest(path.css_lib))
        .pipe(rename("styles.min.css"))
        .pipe(minifyCSS())
        .pipe(gulp.dest(path.css_lib));
});

gulp.task('default', ['compile-js', 'compile-js']);