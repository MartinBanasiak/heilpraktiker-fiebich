
var shopconfig = require('./../config.json');
var config = shopconfig.b2c;
var bowerdir = config.bower.path,
    builddir = config.layout.path + config.layout.code + "/" + config.layout.build;

var gulp = require('gulp'),
    concat = require ('gulp-concat');

gulp.task('concat-js-components',function () {
    return gulp.src([
        bowerdir + '/jquery/jquery.min.js',
        builddir + '/js/bootstrap.js',
        bowerdir + '/jquery-hoverIntent/jquery.hoverIntent.js',
        bowerdir + '/owl.carousel/dist/owl.carousel.min.js',
        bowerdir + '/isotope/dist/isotope.pkgd.js',
        bowerdir + '/jquery.pin/jquery.pin.mod.js',
        bowerdir + '/ekko-lightbox/dist/ekko-lightbox.js',
        './dc/common/common.js'
    ])
        .pipe(concat('components.js'))
        .pipe(gulp.dest(builddir + '/js/'));
});

gulp.task('concat-css-components',function () {
    return gulp.src([
        bowerdir + '/normalize-css/normalize.css',
        bowerdir + '/font-awesome/css/font-awesome.min.css',
        bowerdir + '/line-awesome/css/line-awesome-font-awesome.min.css',
        builddir + '/css/bootstrap.css',
        bowerdir + '/animate.css/animate.min.css',
        bowerdir + '/owl.carousel/dist/assets/owl.carousel.min.css',
        bowerdir + '/owl.carousel/dist/assets/owl.theme.default.min.css',
        bowerdir + '/ekko-lightbox/dist/ekko-lightbox.css'
    ])
        .pipe(concat('components.css')).on('error', onError)
        .pipe(gulp.dest(builddir + '/css/'));
});


function onError(err) {
    console.log(err);
    this.emit('end');
}