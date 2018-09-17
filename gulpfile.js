var gulp = require('gulp'),
concat = require('gulp-concat'),
inject = require('gulp-inject'),
transform = require('gulp-transform'),
watch = require('gulp-watch'),
browserSync = require('browser-sync'),
uglify = require('gulp-uglify');


gulp.task('javascript', function() {
return gulp.src([
    './src/javascript/*.js',
    ])
    .pipe(concat('lib.js'))
    .pipe(gulp.dest('./bin/javascript/'));
});

gulp.task('php', function() {
    return gulp.src('./src/page-black-belt-subscription.php')
        .pipe(inject(gulp.src(['./src/html/*.html']), {
            starttag: '<!-- inject:form_pages:{{ext}} -->',
            transform: function (filePath, file) {
                return file.contents.toString('utf8')
            }
        }))
        .pipe(gulp.dest('./'));
});


gulp.task('browser-sync', function () {
    browserSync.init({
        proxy: "http://localhost/wordpress/black-belt-subscription/",
        notify: false
    });
     gulp.watch('src/**/*.*', ['javascript', 'php']).on('change', browserSync.reload);
});

gulp.task('default', ['browser-sync']);
