var gulp = require('gulp'),
concat = require('gulp-concat'),
inject = require('gulp-inject'),
transform = require('gulp-transform'),
uglify = require('gulp-uglify');


gulp.task('javascript', function() {
return gulp.src([
    './src/javascript/*.js',
    ])
    .pipe(concat('lib.js'))
    .pipe(uglify())
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
