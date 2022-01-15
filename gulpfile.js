const gulp = require('gulp'),
    autoprefixer = require('autoprefixer'),
    composer = require('gulp-uglify/composer'),
    concat = require('gulp-concat'),
    cssnano = require('cssnano'),
    footer = require('gulp-footer'),
    format = require('date-format'),
    header = require('gulp-header'),
    postcss = require('gulp-postcss'),
    rename = require('gulp-rename'),
    replace = require('gulp-replace'),
    sass = require('gulp-sass')(require('sass')),
    uglifyjs = require('uglify-js'),
    uglify = composer(uglifyjs, console),
    pkg = require('./_build/config.json');

const banner = '/*!\n' +
    ' * <%= pkg.name %> - <%= pkg.description %>\n' +
    ' * Version: <%= pkg.version %>\n' +
    ' * Build date: ' + format("yyyy-MM-dd", new Date()) + '\n' +
    ' */';
const year = new Date().getFullYear();

let phpversion;
let modxversion;
pkg.dependencies.forEach(function (dependency, index) {
    switch (pkg.dependencies[index].name) {
        case 'php':
            phpversion = pkg.dependencies[index].version.replace(/>=/, '');
            break;
        case 'modx':
            modxversion = pkg.dependencies[index].version.replace(/>=/, '');
            break;
    }
});

gulp.task('scripts-web', function () {
    return gulp.src([
        'source/js/rememberthis.js'
    ])
        .pipe(concat('rememberthis.min.js'))
        .pipe(uglify())
        .pipe(header(banner + '\n', {pkg: pkg}))
        .pipe(gulp.dest('assets/components/rememberthis/js/'))
});

gulp.task('sass-web', function () {
    return gulp.src([
        'source/sass/mgr/rememberthis.scss'
    ])
        .pipe(sass().on('error', sass.logError))
        .pipe(postcss([
            autoprefixer()
        ]))
        .pipe(gulp.dest('assets/components/rememberthis/css/'))
        .pipe(concat('rememberthis.css'))
        .pipe(postcss([
            cssnano({
                preset: ['default', {
                    discardComments: {
                        removeAll: true
                    }
                }]
            })
        ]))
        .pipe(rename({
            suffix: '.min'
        }))
        .pipe(footer('\n' + banner, {pkg: pkg}))
});

gulp.task('images-web', function () {
    return gulp.src('./source/img/**/*.+(png|jpg|gif|svg)')
        .pipe(gulp.dest('assets/components/rememberthis/img/'));
});

gulp.task('bump-copyright', function () {
    return gulp.src([
        'core/components/rememberthis/model/rememberthis/rememberthis.class.php',
        'core/components/rememberthis/src/RememberThis.php'
    ], {base: './'})
        .pipe(replace(/Copyright 2008(-\d{4})? by/g, 'Copyright ' + (year > 2008 ? '2008-' : '') + year + ' by'))
        .pipe(gulp.dest('.'));
});
gulp.task('bump-version', function () {
    return gulp.src([
        'core/components/rememberthis/src/RememberThis.php'
    ], {base: './'})
        .pipe(replace(/version = '\d+\.\d+\.\d+[-a-z0-9]*'/ig, 'version = \'' + pkg.version + '\''))
        .pipe(gulp.dest('.'));
});
gulp.task('bump-docs', function () {
    return gulp.src([
        'mkdocs.yml',
    ], {base: './'})
        .pipe(replace(/&copy; 2008(-\d{4})?/g, '&copy; ' + (year > 2008 ? '2008-' : '') + year))
        .pipe(gulp.dest('.'));
});
gulp.task('bump-requirements', function () {
    return gulp.src([
        'docs/index.md',
    ], {base: './'})
        .pipe(replace(/[*-] MODX Revolution \d.\d.*/g, '* MODX Revolution ' + modxversion + '+'))
        .pipe(replace(/[*-] PHP (v)?\d.\d.*/g, '* PHP ' + phpversion + '+'))
        .pipe(gulp.dest('.'));
});
gulp.task('bump', gulp.series('bump-copyright', 'bump-version', 'bump-docs', 'bump-requirements'));

gulp.task('watch', function () {
    // Watch .js files
    gulp.watch(['source/js/**/*.js'], gulp.series('scripts-web'));
    // Watch .scss files
    gulp.watch(['source/sass/**/*.scss'], gulp.series('sass-web'));
    // Watch *.(png|jpg|gif|svg) files
    gulp.watch(['source/img/**/*.(png|jpg|gif|svg)'], gulp.series('images-web'));
});

// Default Task
gulp.task('default', gulp.series('bump', 'scripts-web', 'sass-web', 'images-web'));