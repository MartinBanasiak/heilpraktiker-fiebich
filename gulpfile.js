/*
 * Frontend-Build
 *
 * Ueberarbeitet am 2026-09-09. Der alte Build (gulp 3, Plugins von 2016) liess
 * sich auf aktuellem Node nicht mehr starten und hatte ausserdem einen
 * Syntaxfehler ("break;f"). Aufgabenname und Ein-/Ausgaben sind unveraendert,
 * nur die Werkzeuge darunter sind aktuell:
 *
 *   gulp public   -> dist/css/style.min.css, dist/css/fck.css, dist/js/*
 *   gulp          -> dasselbe unminifiziert, danach watch
 *
 * Nicht uebernommen wurden die Tasks generate-iconfont, generate-favicon,
 * minify-images und die FTP-Upload-Tasks. Sie brauchen Zugangsdaten
 * beziehungsweise Werkzeuge, die im Repository fehlen; ihre Ergebnisse liegen
 * als fertige Dateien in dist/ und aendern sich im Alltag nicht. Bei Bedarf
 * lassen sie sich aus der Git-Historie zurueckholen.
 */

const gulp = require('gulp');
const concat = require('gulp-concat');
const less = require('gulp-less');
const cleanCSS = require('gulp-clean-css');
const postcss = require('gulp-postcss');
const autoprefixer = require('autoprefixer');
const rename = require('gulp-rename');
const sourcemaps = require('gulp-sourcemaps');
const terser = require('gulp-terser');

// Less 4 rechnet "/" ausserhalb von Klammern nicht mehr aus. Der urspruengliche
// Build (Less 2) hat das getan, die Quellen verlassen sich darauf - ohne diese
// Option landet woertlich "padding:10px/2" im CSS statt "padding:5px".
const LESS_OPTIONS = { math: 'always' };

const shopconfig = require('./config.json');

const CONFIGS = {
    1: 'b2c',
    2: 'b2b',
    3: 'catalog',
    4: 'login',
    5: 'salesperson',
};

const config = shopconfig[CONFIGS[shopconfig.shoptype] || 'b2c'];

const bowerdir = config.bower.path;
const layoutdir = config.layout.path + config.layout.code;
const srcdir = layoutdir + '/' + config.layout.src;
const builddir = layoutdir + '/' + config.layout.build;
const distdir = layoutdir + '/' + config.layout.dist;
const cssfile = config.layout.cssfile;
const jsfile = config.layout.jsfile;

// Reihenfolge ist relevant - normalize zuerst, Bootstrap vor den Komponenten,
// die es ueberschreiben.
const CSS_COMPONENTS = [
    bowerdir + '/normalize-css/normalize.css',
    bowerdir + '/font-awesome/css/font-awesome.min.css',
    bowerdir + '/line-awesome/css/line-awesome-font-awesome.min.css',
    builddir + '/css/bootstrap.css',
    bowerdir + '/animate.css/animate.min.css',
    bowerdir + '/owl.carousel/dist/assets/owl.carousel.min.css',
    bowerdir + '/owl.carousel/dist/assets/owl.theme.default.min.css',
    bowerdir + '/ekko-lightbox/dist/ekko-lightbox.css',
];

const JS_COMPONENTS = [
    // Reihenfolge und Umfang aus dem ausgelieferten dist/js/script_new3.js
    // zurueckgerechnet - ueber markante Bezeichner, nicht ueber Dateiinhalte:
    // das Bundle ist minifiziert, Quelltextzeilen kommen darin nicht woertlich
    // vor. Ein erster Versuch ueber Zeilenvergleiche hatte jquery.pin
    // faelschlich als "nicht enthalten" ausgewiesen.
    //
    // jsvat fehlt tatsaechlich - es prueft USt-IdNr. im Shop, den es auf
    // dieser Seite nicht gibt.
    //
    // common.js steht bewusst am Ende: Die Datei beginnt mit einem
    // $(document).ready(...)-Block. Steht sie vor jquery, wirft sie
    // "ReferenceError: $ is not defined" - und ein ungefangener Fehler auf
    // oberster Ebene bricht die Ausfuehrung des gesamten Bundles ab, jQuery
    // eingeschlossen. Die Seite haette dann ueberhaupt kein JavaScript mehr.
    bowerdir + '/jquery/jquery.min.js',
    bowerdir + '/jquery-hoverIntent/jquery.hoverIntent.js',
    bowerdir + '/owl.carousel/dist/owl.carousel.min.js',
    bowerdir + '/isotope/dist/isotope.pkgd.js',
    bowerdir + '/jquery.pin/jquery.pin.mod.js',
    bowerdir + '/ekko-lightbox/dist/ekko-lightbox.js',
    builddir + '/js/bootstrap.js',
    './dc/common/common.js',
];

const BOOTSTRAP_JS = [
    bowerdir + '/bootstrap/js/affix.js',
    bowerdir + '/bootstrap/js/alert.js',
    bowerdir + '/bootstrap/js/button.js',
    bowerdir + '/bootstrap/js/modal.js',
    bowerdir + '/bootstrap/js/tooltip.js',
    bowerdir + '/bootstrap/js/transition.js',
];

function bootstrapCss() {
    return gulp
        .src(bowerdir + '/bootstrap/less/bootstrap_modified.less')
        .pipe(less(LESS_OPTIONS))
        .pipe(cleanCSS({ compatibility: 'ie8' }))
        .pipe(rename('bootstrap.css'))
        .pipe(gulp.dest(builddir + '/css/'));
}

function bootstrapJs() {
    return gulp
        .src(BOOTSTRAP_JS, { allowEmpty: true })
        .pipe(concat('bootstrap.js'))
        .pipe(gulp.dest(builddir + '/js/'));
}

function concatCssComponents() {
    return gulp
        .src(CSS_COMPONENTS, { allowEmpty: true })
        .pipe(concat('components.css'))
        .pipe(gulp.dest(builddir + '/css/'));
}

function concatJsComponents() {
    return gulp
        .src(JS_COMPONENTS, { allowEmpty: true })
        .pipe(concat('components.js'))
        .pipe(gulp.dest(builddir + '/js/'));
}

// Entwicklung: lesbar, mit Sourcemap, ohne Minifizierung.
function stylesDev() {
    return gulp
        .src([builddir + '/css/components.css', srcdir + '/less/app/' + cssfile + '.less'])
        .pipe(sourcemaps.init())
        .pipe(less(LESS_OPTIONS))
        .pipe(postcss([autoprefixer()]))
        .pipe(concat(cssfile + '.css'))
        .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest(distdir + '/css/'));
}

// Auslieferung: identische Quellen, zusaetzlich minifiziert.
function stylesPublic() {
    return gulp
        .src([builddir + '/css/components.css', srcdir + '/less/app/' + cssfile + '.less'])
        .pipe(less(LESS_OPTIONS))
        .pipe(postcss([autoprefixer()]))
        .pipe(concat(cssfile + '.min.css'))
        .pipe(cleanCSS({ compatibility: 'ie8' }))
        .pipe(gulp.dest(distdir + '/css/'));
}

// Stylesheet fuer den CKEditor-Inhaltsbereich im Backend.
function stylesFck() {
    return gulp
        .src(srcdir + '/less/app/fck.less', { allowEmpty: true })
        .pipe(less(LESS_OPTIONS))
        .pipe(postcss([autoprefixer()]))
        .pipe(gulp.dest(distdir + '/css/'));
}

// Symbole liegen als SVG im Quellordner und werden unveraendert
// uebernommen - nichts zu kompilieren, nur zu kopieren.
function icons() {
    return gulp
        .src(srcdir + '/icons/*.svg', { allowEmpty: true })
        .pipe(gulp.dest(distdir + '/icons/'));
}

function scripts() {
    return gulp
        .src([builddir + '/js/components.js', srcdir + '/js/' + jsfile + '.js'], { allowEmpty: true })
        .pipe(concat(jsfile + '.js'))
        .pipe(gulp.dest(distdir + '/js/'));
}

// Minifiziert an Ort und Stelle - genau wie der urspruengliche uglify-js-Task.
// Das Template bindet script_new3.js ohne .min ein, die Datei ist also im
// Auslieferungsstand minifiziert.
function uglifyJs() {
    return gulp
        .src(distdir + '/js/' + jsfile + '.js')
        .pipe(terser())
        .pipe(gulp.dest(distdir + '/js/'));
}

function watch() {
    gulp.watch(srcdir + '/less/**/*.less', gulp.series(stylesDev));
    gulp.watch(srcdir + '/js/**/*.js', gulp.series(scripts));
    return Promise.resolve();
}

const components = gulp.series(bootstrapCss, bootstrapJs, concatCssComponents, concatJsComponents);

exports['bootstrap-css'] = bootstrapCss;
exports['bootstrap-js'] = bootstrapJs;
exports['concat-css-components'] = concatCssComponents;
exports['concat-js-components'] = concatJsComponents;
exports.styles_dev = stylesDev;
exports.styles_public = stylesPublic;
exports.styles_fck = stylesFck;
exports.icons = icons;
exports.scripts = scripts;
exports.watch = watch;

exports['uglify-js'] = uglifyJs;

exports.public = gulp.series(components, stylesPublic, stylesFck, icons, scripts, uglifyJs);
exports.default = gulp.series(components, stylesDev, icons, scripts, watch);
