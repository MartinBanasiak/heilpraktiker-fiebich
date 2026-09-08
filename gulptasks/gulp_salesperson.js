
var shopconfig = require('./../config.json');
var config = shopconfig.salesperson;
var bowerdir = config.bower.path,
    builddir = config.layout.path + config.layout.code + "/" + config.layout.build;

var gulp = require('gulp'),
    concat = require ('gulp-concat');

gulp.task('concat-js-components',function () {
    return gulp.src([
        bowerdir + '/jquery/jquery.min.js',
        builddir + '/js/bootstrap.js',
        bowerdir + '/jquery-hoverIntent/jquery.hoverIntent.js',
        bowerdir + '/seiyria-bootstrap-slider/dist/bootstrap-slider.min.js',
        bowerdir + '/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js',
        bowerdir + '/owl.carousel/dist/owl.carousel.min.js',
        bowerdir + '/magicscroll/magicscroll.js',
        bowerdir + '/magiczoomplus/magiczoomplus.js',
        bowerdir + '/password-strength-meter/dist/password.min.js',
        bowerdir + '/isotope/dist/isotope.pkgd.js',
        './dc/common/common.js',
        './module/dcshop/common/dc_functions.js'
    ])
        .pipe(concat('components.js'))
        .pipe(gulp.dest(builddir + '/js/'));
});

gulp.task('concat-css-components',function () {
    return gulp.src([
        bowerdir + '/normalize-css/normalize.css',
        bowerdir + '/font-awesome/css/font-awesome.min.css',
        bowerdir + '/material-design-icons/iconfont/material-icons.css',
        builddir + '/css/bootstrap.css',
        bowerdir + '/seiyria-bootstrap-slider/dist/css/bootstrap-slider.min.css',
        bowerdir + '/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css',
        bowerdir + '/animate.css/animate.min.css',
        bowerdir + '/owl.carousel/dist/assets/owl.carousel.min.css',
        bowerdir + '/owl.carousel/dist/assets/owl.theme.default.min.css',
        bowerdir + '/password-strength-meter/dist/password.min.css',
        bowerdir + '/magicscroll/magicscroll.css',
        bowerdir + '/magiczoomplus/magiczoomplus.css'
    ])
        .pipe(concat('components.css')).on('error', onError)
        .pipe(gulp.dest(builddir + '/css/'));
});

function onError(err) {
    console.log(err);
    this.emit('end');
}