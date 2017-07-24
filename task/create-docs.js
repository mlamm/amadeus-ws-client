const gulp = require('gulp');
const exec = require('child_process').exec;
const gutil = require('gulp-util');

/**
 * Task to create the static documentation based off the swagger file
 */
gulp.task(
    'create-docs',
    done => {
        exec(
            'npm run docs',
            (err, stdout, stderr) => {
                if (err || stderr) {
                    console.error(err, stderr);
                    gutil.log('an', gutil.colors.red('error occured'), '.');
                } else {
                    gutil.log('docs', gutil.colors.green('successfully created'), '.');
                }

                done();
            }
        );
    }
);
