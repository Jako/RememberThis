module.exports = function (grunt) {
    // Project configuration.
    grunt.initConfig({
        modx: grunt.file.readJSON('_build/config.json'),
        sshconfig: grunt.file.readJSON('/Users/jako/Documents/MODx/partout.json'),
        banner: '/*!\n' +
        ' * <%= modx.name %> - <%= modx.description %>\n' +
        ' * Version: <%= modx.version %>\n' +
        ' * Build date: <%= grunt.template.today("yyyy-mm-dd") %>\n' +
        ' */\n',
        usebanner: {
            dist: {
                options: {
                    position: 'top',
                    banner: '<%= banner %>'
                },
                files: {
                    src: [
                        'assets/components/rememberthis/js/rememberthis.min.js',
                        'assets/components/rememberthis/css/rememberthis.css',
                        'assets/components/rememberthis/css/rememberthis.min.css'
                    ]
                }
            }
        },
        uglify: {
            rememberthis: {
                src: [
                    'assets/components/rememberthis/js/rememberthis.js'
                ],
                dest: 'assets/components/rememberthis/js/rememberthis.min.js'
            }
        },
        sass: {
            options: {
                outputStyle: 'expanded',
                sourcemap: false
            },
            dist: {
                files: {
                    'assets/components/rememberthis/css/rememberthis.css': 'assets/components/rememberthis/sass/rememberthis.scss'
                }
            }
        },
        cssmin: {
            rememberthis: {
                src: [
                    'assets/components/rememberthis/css/rememberthis.css'
                ],
                dest: 'assets/components/rememberthis/css/rememberthis.min.css'
            }
        },
        sftp: {
            css: {
                files: {
                    "./": [
                        'assets/components/rememberthis/css/rememberthis.css',
                        'assets/components/rememberthis/css/rememberthis.min.css'
                    ]
                },
                options: {
                    path: '<%= sshconfig.hostpath %>develop/rememberthis/',
                    srcBasePath: 'develop/rememberthis/',
                    host: '<%= sshconfig.host %>',
                    username: '<%= sshconfig.username %>',
                    privateKey: grunt.file.read("/Users/jako/.ssh/id_dsa"),
                    passphrase: '<%= sshconfig.passphrase %>',
                    showProgress: true
                }
            },
            js: {
                files: {
                    "./": [
                        'assets/components/rememberthis/js/rememberthis.js',
                        'assets/components/rememberthis/js/rememberthis.min.js'
                    ]
                },
                options: {
                    path: '<%= sshconfig.hostpath %>develop/rememberthis/',
                    srcBasePath: 'develop/rememberthis/',
                    host: '<%= sshconfig.host %>',
                    username: '<%= sshconfig.username %>',
                    privateKey: grunt.file.read("/Users/jako/.ssh/id_dsa"),
                    passphrase: '<%= sshconfig.passphrase %>',
                    showProgress: true
                }
            }
        },
        watch: {
            scripts: {
                files: ['assets/components/rememberthis/js/rememberthis.js'],
                tasks: ['uglify', 'usebanner', 'sftp:js']
            },
            css: {
                files: ['assets/components/rememberthis/sass/rememberthis.scss'],
                tasks: ['sass', 'cssmin', 'usebanner', 'sftp:css']
            }
        }
    });

    //load the packages
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-banner');
    grunt.loadNpmTasks('grunt-ssh');
    grunt.loadNpmTasks('grunt-sass');

    //register the task
    grunt.registerTask('default', ['uglify', 'sass', 'cssmin', 'usebanner', 'sftp']);
};