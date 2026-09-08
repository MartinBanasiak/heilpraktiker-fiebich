
var config = "";
var shopconfig = require('./config.json');
var shoptype = shopconfig.shoptype;

switch (shoptype) {
    case 2:
        config = shopconfig.b2b;
        require('./gulptasks/gulp_b2b.js');
        break;
    case 3:
        config = shopconfig.catalog;
        require('./gulptasks/gulp_catalog.js');
        break;f
    case 4:
        config = shopconfig.login;
        require('./gulptasks/gulp_login.js');
        break;
    case 5:
        config = shopconfig.salesperson;
        require('./gulptasks/gulp_salesperson.js');
        break;
    default:
        config = shopconfig.b2c;
        require('./gulptasks/gulp_b2c.js');
}

var bowerdir = config.bower.path,
    srcdir = config.layout.path + config.layout.code + "/" + config.layout.src,
    builddir = config.layout.path + config.layout.code + "/" + config.layout.build,
    distdir = config.layout.path + config.layout.code + "/" + config.layout.dist,
    fontName = config.iconfont.name,
    faviconTitle = config.favicon.title,
    faviconBackground = config.favicon.background,
    faviconColor = config.favicon.color,
    faviconMasterpicture = config.layout.path + config.layout.code + "/" + config.layout.src + "/" + config.favicon.masterpicture_path + config.favicon.masterpicture_filename,
    faviconDataFile = config.layout.path + config.layout.code + "/" + config.layout.dist + "/" + config.favicon.masterpicture_path + config.favicon.data_file,
    autoupload = shopconfig.autoupload,
    startLivereload = shopconfig.livereload;

var gulp = require('gulp'),
    changed = require('gulp-changed'),
    concat = require ('gulp-concat'),
    uglify = require ('gulp-uglify'),
    rename = require('gulp-rename'),
    imageMin = require ('gulp-imageMin'),
    clean = require('gulp-clean'),
    less = require ('gulp-less'),
    minifyCSS = require ('gulp-clean-css'),
    iconfont = require ('gulp-iconfont'),
    iconfontCss = require('gulp-iconfont-css'),
    realFavicon = require ('gulp-real-favicon'),
    fs = require('fs'),
    autoprefixer = require ('gulp-autoprefixer'),
    runSequence = require('run-sequence'),
    sourcemaps = require('gulp-sourcemaps'),
    streamqueue = require('streamqueue'),
    ftp = require('vinyl-ftp'),
    gutil = require('gulp-util'),
    insert = require('gulp-insert'),
    livereload = require('gulp-livereload');

var conn = ftp.create( {
    host:     autoupload.host,
    user:     autoupload.user,
    password: autoupload.password,
    port: autoupload.port,
    parallel: 10,
    log:      gutil.log
});

gulp.task('generate-iconfont', function(){
    gulp.src([srcdir + '/icons_svg/*.svg'])
        .pipe(iconfontCss({
            fontName: fontName,
            path: srcdir + '/less/template/_icons.less',
            targetPath:  '../../../src/less/app/icons.less',
            fontPath: builddir + '/fonts/icons/'
        }))
        .pipe(iconfont({
            fontName: fontName
        }))
        .pipe(gulp.dest(builddir + '/fonts/icons/'));
});

gulp.task('clean-dist',function () {
    return gulp.src([
        distdir + "/css/components.css",
        distdir + "/css/"+config.layout.cssfile+".css.map"
    ], {read: false})
        .pipe(clean());
});

gulp.task('generate-favicon', function(done) {
    realFavicon.generateFavicon({
        masterPicture: faviconMasterpicture,
        dest: distdir + '/favicons/',
        iconsPath: '/',
        design: {
            ios: {
                pictureAspect: 'noChange',
                assets: {
                    ios6AndPriorIcons: false,
                    ios7AndLaterIcons: false,
                    precomposedIcons: false,
                    declareOnlyDefaultIcon: true
                }
            },
            desktopBrowser: {},
            windows: {
                pictureAspect: 'noChange',
                backgroundColor: faviconBackground,
                onConflict: 'override',
                assets: {
                    windows80Ie10Tile: false,
                    windows10Ie11EdgeTiles: {
                        small: false,
                        medium: true,
                        big: false,
                        rectangle: false
                    }
                }
            },
            androidChrome: {
                pictureAspect: 'noChange',
                themeColor: faviconBackground,
                manifest: {
                    name: faviconTitle,
                    display: 'standalone',
                    orientation: 'notSet',
                    onConflict: 'override',
                    declared: true
                },
                assets: {
                    legacyIcon: false,
                    lowResolutionIcons: false
                }
            },
            safariPinnedTab: {
                pictureAspect: 'blackAndWhite',
                threshold: 82.8125,
                themeColor: faviconColor
            }
        },
        settings: {
            scalingAlgorithm: 'Mitchell',
            errorOnImageTooSmall: false
        },
        markupFile: distdir + "/favicons/faviconData.json"
    }, function() {
        done();
    });
});

gulp.task('check-for-favicon-update', function(done) {
    var currentVersion = JSON.parse(fs.readFileSync(distdir + "/favicons/faviconData.json")).version;
    realFavicon.checkForUpdates(currentVersion, function(err) {
        if (err) {
            throw err;
        }
    });
});

gulp.task('minify-images', function() {
    var imgSrc = srcdir + '/images/**/*',
        imgDst = distdir + '/images/';

    gulp.src(imgSrc)
        .pipe(changed(imgSrc))
        .pipe(imageMin()).on('error', onError)
        .pipe(gulp.dest(imgDst));
});

gulp.task('uglify-js', function () {
    return gulp.src(distdir + '/js/*.js')
        .pipe(uglify()).on('error', onError)
        .pipe(gulp.dest(distdir + '/js/'));
});

gulp.task('minify-css', function() {
    return gulp.src(distdir + '/css/*.css')
        .pipe(minifyCSS({compatibility: 'ie8'})).on('error', onError)
        .pipe(gulp.dest(distdir + '/css/'));
});

gulp.task('copy', function() {
    gulp.src(srcdir + '/fonts/**/*')
        .pipe(gulp.dest(distdir + '/fonts/'));
    gulp.src(srcdir + '/images/**/*')
        .pipe(gulp.dest(distdir + '/images/'));
    gulp.src(builddir + '/favicons/**/*')
        .pipe(gulp.dest(distdir + '/favicons/'));
});

gulp.task('styles_fck', function() {
    gulp.src(srcdir + "/less/app/fck.less")
        .pipe(less()).on('error', onError)
        .pipe(minifyCSS({compatibility: 'ie8'})).on('error', onError)
        .pipe(gulp.dest(distdir + "/css/"));
});

gulp.task('bootstrap-less',function () {
    gulp.src(bowerdir + "/bootstrap/less/bootstrap_modified.less")
        .pipe(less()).on('error', onError)
        .pipe(minifyCSS({compatibility: 'ie8'})).on('error', onError)
        .pipe(rename('bootstrap.css')).on('error', onError)
        .pipe(gulp.dest(builddir + "/css/"));
});

gulp.task('bootstrap-js',function () {
    gulp.src([
        bowerdir + '/bootstrap/js/affix.js',
        bowerdir + '/bootstrap/js/alert.js',
        bowerdir + '/bootstrap/js/button.js',
        //bowerdir + '/bootstrap/js/carousel.js',
        //bowerdir + '/bootstrap/js/collapse.js',
        //bowerdir + '/bootstrap/js/dropdown.js',
        bowerdir + '/bootstrap/js/modal.js',
        //bowerdir + '/bootstrap/js/popover.js',
        //bowerdir + '/bootstrap/js/scrollspy.js',
        //bowerdir + '/bootstrap/js/tab.js',
        bowerdir + '/bootstrap/js/tooltip.js',
        bowerdir + '/bootstrap/js/transition.js',
        bowerdir + '/jsvat/dist/jsvat.js'
    ])
        .pipe(insert.append(';'))
        .pipe(concat('bootstrap.js'))
        .pipe(gulp.dest(builddir + '/js/'));
});

gulp.task('watch', function () {
    if(startLivereload){
        livereload.listen();
    }
    gulp.watch(srcdir + '/less/**/*.less',['deploy-css']);
    gulp.watch(srcdir + '/js/**/*.js', ['deploy-js']);
});

gulp.task( 'upload-css', function () {
    if(autoupload.active){
        return gulp.src( distdir + "/css/*", { base: '.', buffer: false } )
            .pipe( conn.dest( autoupload.remotePath ) );
    }
});

gulp.task( 'upload-js', function () {
    if(autoupload.active){
        return gulp.src( distdir + "/js/*", { base: '.', buffer: false } )
            .pipe( conn.dest( autoupload.remotePath ));
    }
});

gulp.task('reloadBrowserCSS',function () {
    if(startLivereload) {
        return gulp.src([
            distdir + '/css/*.css'
        ])
            .pipe(livereload());
    }
});

gulp.task('deploy-css',function () {
    runSequence(
        'styles_dev',
        'upload-css',
        'reloadBrowserCSS'
    )
});

gulp.task('deploy-js',function () {
    runSequence(
        'scripts',
        'upload-js'
    )
});

gulp.task('autoprefixer',function () {
    gulp.src(distdir + '/css/*.css')
        .pipe(autoprefixer({
            browsers: ['last 2 versions'],
            cascade: false
        })).on('error', onError)
        .pipe(gulp.dest(distdir + '/css/'))
});

gulp.task('styles_dev', function() {
    return gulp.src(builddir + '/css/components.css')
        .pipe(gulp.dest(distdir + '/css/')),
        gulp.src(srcdir + "/less/app/"+config.layout.cssfile+".less")
            .pipe(sourcemaps.init())
            .pipe(less())
            .pipe(sourcemaps.write("."))
            .pipe(gulp.dest(distdir + '/css/'))
            .on('error', onError)
            .pipe(concat(config.layout.cssfile+'.css')).on('error', onError)
            .pipe(gulp.dest(distdir + '/css/'));
});

gulp.task('styles_public', function() {
    return streamqueue({ objectMode: true},
        gulp.src(builddir + '/css/components.css').on('error', onError),
        gulp.src(srcdir + "/less/app/"+config.layout.cssfile+".less")
            .pipe(less())
            .pipe(gulp.dest(distdir + '/css/'))
            .on('error', onError)
    )
        .pipe(concat(config.layout.cssfile+'.min.css')).on('error', onError)
        .pipe(minifyCSS({compatibility: 'ie8'})).on('error', onError)
        .pipe(gulp.dest(distdir + '/css/'));
});

gulp.task('scripts',function () {
    return gulp.src([
        builddir + '/js/components.js',
        srcdir + '/js/'+config.layout.jsfile+'.js'
    ])
        .pipe(concat(config.layout.jsfile+'.js'))
        .pipe(gulp.dest(distdir + '/js/'));
});

gulp.task('default', function(callback) {
    runSequence(
        'bootstrap-less',
        'bootstrap-js',
        'concat-css-components',
        'concat-js-components',
        'generate-iconfont',
        'styles_dev',
        'scripts',
        'copy',
        'watch',
        callback);
});

gulp.task('public', function(callback) {
    runSequence(
        'clean-dist',
        'bootstrap-less',
        'bootstrap-js',
        'concat-css-components',
        'concat-js-components',
        'generate-iconfont',
        'styles_public',
        'styles_fck',
        'scripts',
        'uglify-js',
        'copy',
        //'minify-images',
        //'generate-favicon',
        callback);
});

function onError(err) {
    console.log(err);
    this.emit('end');
}