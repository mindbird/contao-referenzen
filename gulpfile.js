const gulp = require('gulp');
const concat = require('gulp-concat');
const uglify = require('gulp-uglify-es').default;

gulp.task('scripts', function() {
    return gulp.src([
        './node_modules/isotope-layout/dist/isotope.pkgd.js'
    ]).pipe(concat('script.min.js'))
        .pipe(uglify())
        .pipe(gulp.dest('./src/Resources/public/js'));
});

gulp.task('default', ['scripts']);