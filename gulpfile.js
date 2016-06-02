const gulp = require('gulp'),
    plumber = require('gulp-plumber'),
    rename = require('gulp-rename'),
    postcss = require('gulp-postcss'),
    sourcemaps = require('gulp-sourcemaps'),
    autoprefixer = require('autoprefixer'),
    concat = require('gulp-concat'),
    uglify = require('gulp-uglify'),
    cache = require('gulp-cache'),
    minifycss = require('gulp-minify-css'),
    less = require('gulp-less'),
    browserSync = require('browser-sync'),
    imagemin = require('gulp-imagemin'),
    pngquant = require('imagemin-pngquant');


const src = 'www/src/',
    dist = 'www/dist/';

gulp.task('imagemin', function () {
    return gulp.src(src + 'img/**/*')
        .pipe(imagemin({
            progressive: true,
            svgoPlugins: [{removeViewBox: false}],
            use: [pngquant()]
        }))
        .pipe(gulp.dest(dist + 'img/'));
});

gulp.task('styles', function () {
    gulp.src([src + 'less/index.less'])
        .pipe(plumber({
            errorHandler: function (error) {
                console.log(error.message);
                this.emit('end');
            }
        }))
        .pipe(less())
        .pipe(sourcemaps.init())
        .pipe(postcss([autoprefixer({browsers: ['last 2 versions']})]))
        .pipe(sourcemaps.write('.'))
        .pipe(rename('style.css'))
        .pipe(gulp.dest(dist + 'css/'))
        .pipe(rename('style.min.css'))
        .pipe(minifycss())
        .pipe(gulp.dest(dist + 'css/'))
        // .pipe(browserSync.stream())
});

gulp.task('scripts', function () {
    return gulp.src(src + 'js/**/*.js')
        .pipe(plumber({
            errorHandler: function (error) {
                console.log(error.message);
                this.emit('end');
            }
        }))
        .pipe(concat('scripts.js'))
        .pipe(gulp.dest(dist + 'js/'))
        .pipe(rename({suffix: '.min'}))
        .pipe(uglify())
        .pipe(gulp.dest(dist + 'js/'))
        // .pipe(browserSync.stream())
});

gulp.task('watch', function () {
    // browsersync.init({
    //     proxy: 'svatbamartinkyalukase.dev/www/'
    // });
    gulp.watch(src + "less/**/*.less", ['styles']);
    gulp.watch(src + "js/**/*.js", ['scripts']);
    // gulp.watch("**/*.latte").on('change', browserSync.reload);
    // gulp.watch("**/*.php").on('change', browserSync.reload);
    gulp.watch(src + "img/**", ['imagemin']);
});

gulp.task('default', ['styles', 'scripts', 'imagemin', 'watch']);
