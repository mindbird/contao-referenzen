const { src, dest, parallel } = require('gulp');
const concat = require('gulp-concat');

function js() {
    return src([
        './node_modules/isotope-layout/dist/isotope.pkgd.js'
    ], { sourcemaps: true })
        .pipe(concat('script.min.js'))
        .pipe(dest('./src/Resources/public/js', { sourcemaps: true }))
}


exports.default = parallel(js);
