module.exports = function(grunt) {

    // Project configuration.
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),

        connect: {
            options: {
                port: 9000,
                livereload: 35729,
                // Change this to '0.0.0.0' to access the server from outside
                hostname: 'localhost'
            },
            livereload: {
                options: {
                    open: true,
                    base: [
                        '.tmp',
                        'public'
                    ]
                }
            },
        },
        watch: {
            options: {
                livereload: true,
            },
            livereload: {
                options: {
                    livereload: '<%= connect.options.livereload %>'
                },
                files: [
                    'public/{,*/}*.html',
                    'public/css/{,*/}*.css',
                    'public/images/{,*/}*'
                ]
            },
            compass: {
                files: ['**/*.{scss,sass}'],
                tasks: ['compass:dev']
            },
        },
        compass: {
            dev: {
                options: {
                    sassDir: ['src/stylesheets'],
                    cssDir: ['public/assets/css'],
                    environment: 'development'
                }
            },
            prod: {
                options: {
                    sassDir: ['src/stylesheets'],
                    cssDir: ['public/css'],
                    environment: 'production'
                }
            },
        }
    });

    // Load the plugin
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-compass');
    grunt.loadNpmTasks('grunt-contrib-connect');

    // Default task(s).
    grunt.registerTask('default', ['compass:dev', 'watch']);
    // prod build
    grunt.registerTask('prod', ['compass:prod']);

};
