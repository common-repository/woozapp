/**
 * Woozapp Tasks
 */
module.exports = function ( grunt ) {
    'use strict';

    grunt.initConfig( {

        /**
         * Package
         */
        pkg: grunt.file.readJSON( 'package.json' ),

        /**
         * Assets Directory
         */
        _assets_dir: 'assets/',

        /**
         * Svg Min
         *
         * Minify SVG using SVGO
         *
         * @link https://github.com/sindresorhus/grunt-svgmin
         */
        svgmin: {
            min: {
                files  : [ {
                    expand: true,
                    src   : [ '<%= _assets_dir %>/svg/*.svg' ]
                } ],
                options: {
                    plugins: [
                        { removeViewBox: false },
                        { removeUselessStrokeAndFill: false },
                        { cleanupIDs: false }
                    ]
                }
            }
        },

        /**
         * Uglify
         *
         * Concatenate and compress the javascript assets
         *
         * @link https://www.npmjs.com/package/grunt-contrib-uglify
         */
        uglify: {
            options: {
                compress: false,
                mangle  : false
            },
            build  : {
                files: {
                    '<%= _assets_dir %>js/admin.min.js': [
                        '<%= _assets_dir %>js/admin.js'
                    ]
                }
            }
        },

        /**
         * Post Css
         *
         * Apply several post-processors to your CSS using PostCSS.
         * PostCSS is a tool for transforming styles with JS plugins.
         *
         * @link https://github.com/nDmitry/grunt-postcss
         * @link https://github.com/postcss/postcss
         */
        postcss: {
            options: {
                map       : true,
                processors: [
                    require( 'autoprefixer' )( {
                        browsers: [ 'last 2 versions' ]
                    } ),
                    require( 'postcss-flexibility' )
                ]
            },
            build  : {
                src: '<%= _assets_dir %>css/admin.css'
            }
        }
    } );

    // https://github.com/sindresorhus/grunt-svgmin
    grunt.loadNpmTasks( 'grunt-svgmin' );

    // https://www.npmjs.com/package/grunt-contrib-uglify
    grunt.loadNpmTasks( 'grunt-contrib-uglify' );

    // https://github.com/postcss/postcss
    grunt.loadNpmTasks( 'grunt-postcss' );

    /**
     * Setup Tasks
     *
     * Execute all basic tasks to setup the project
     */
    grunt.registerTask( 'update', [
        'svgmin:min'
    ] );

    /**
     * Build Task
     *
     * - Uglify
     */
    grunt.registerTask( 'build', [ 'svgmin:min', 'postcss', 'uglify:build' ] );
};