const gulp = require('gulp'),
concat = require('gulp-concat'),
inject = require('gulp-inject'),
transform = require('gulp-transform'),
watch = require('gulp-watch'),
browserSync = require('browser-sync'),
//gutil  = require('gulp-util'),
//ftp = require( 'vinyl-ftp' ),
//sftp = require('gulp-sftp'),
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

/*gulp.task('deploy', function() {
    const config = require('./sftp-config.json');

    const globs = [
        'folder/file',
        'folder/file',
        'folder/file',
    ];

    if (config.type == 'ftp') {
        //  FTP version
        const conn = ftp.create( {
            host:     config.host,
            user:     config.user,
            password: config.password,
            port:     config.port,
            parallel: 10,
            reload:   true,
            debug:    function(d){console.log(d);},
            log:      gutil.log
        });
        return gulp.src( globs, { base: '.', buffer: false } )
            .pipe( conn.newer( '/dest_folder/' ) ) // only upload newer files
            .pipe( conn.dest( '/dest_folder/' ) );
    } else {
        // SFTP version
        const conn = sftp({
                host: config.host,
                user: config.user,
                pass: config.password,
                port: config.port,
                remotePath: config.remote_path,
            });
        return gulp.src(globs, { base: '.', buffer: false } )
            .pipe(conn);
    }
});     */
