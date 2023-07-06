/*!
 * Main gruntfile for assets
 * Homepage: https://wdmg.com.ua/
 * Author: Vyshnyvetskyy Alexsander (alex.vyshyvetskyy@gmail.com)
 * Copyright 2019-2023 W.D.M.Group, Ukraine
 * Licensed under MIT
*/

module.exports = function(grunt) {
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        sass: {
            style: {
                files: {
                    'assets/css/terminal.css': ['assets/scss/terminal.scss']
                }
            }
        },
        autoprefixer: {
            dist: {
                files: {
                    'assets/css/terminal.css': ['assets/css/terminal.css']
                }
            }
        },
        cssmin: {
            options: {
                mergeIntoShorthands: false,
                roundingPrecision: -1
            },
            target: {
                files: {
                    'assets/css/terminal.min.css': ['assets/css/terminal.css']
                }
            }
        },
        watch: {
            scss: {
                files: ['assets/scss/terminal.scss'],
                tasks: ['sass:style', 'cssmin'],
                options: {
                    spawn: false
                }
            }
        }
    });

    grunt.loadNpmTasks('grunt-css');
    grunt.loadNpmTasks('grunt-contrib-sass');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-autoprefixer');
    grunt.registerTask('default', ['sass', 'autoprefixer', 'cssmin', 'watch']);
};