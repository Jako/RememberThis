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
            css: {
                options: {
                    position: 'top',
                    banner: '<%= banner %>'
                },
                files: {
                    src: [
                        'assets/components/rememberthis/css/rememberthis.css',
                        'assets/components/rememberthis/css/rememberthis.min.css'
                    ]
                }
            },
            js: {
                options: {
                    position: 'top',
                    banner: '<%= banner %>'
                },
                files: {
                    src: [
                        'assets/components/rememberthis/js/rememberthis.min.js'
                    ]
                }
            }
        },
        uglify: {
            web: {
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
        postcss: {
            options: {
                processors: [
                    require('pixrem')(),
                    require('autoprefixer')({
                        browsers: 'last 2 versions, ie >= 8'
                    })
                ]
            },
            dist: {
                src: [
                    'assets/components/rememberthis/css/rememberthis.css'
                ]
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
                    privateKey: '<%= sshconfig.privateKey %>',
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
                    privateKey: '<%= sshconfig.privateKey %>',
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
                tasks: ['sass', 'postcss', 'cssmin', 'usebanner', 'sftp:css']
            }
        },
        bump: {
            copyright: {
                files: [{
                    src: 'core/components/rememberthis/model/rememberthis/rememberthis.class.php',
                    dest: 'core/components/rememberthis/model/rememberthis/rememberthis.class.php'
                },{
                    src: 'assets/components/rememberthis/js/rememberthis.js',
                    dest: 'assets/components/rememberthis/js/rememberthis.js'
                }],
                options: {
                    replacements: [{
                        pattern: /Copyright 2008(-\d{4})? by/g,
                        replacement: 'Copyright ' + (new Date().getFullYear() > 2008 ? '2008-' : '') + new Date().getFullYear() + ' by'
                    }]
                }
            },
            version: {
                files: [{
                    src: 'core/components/rememberthis/model/rememberthis/rememberthis.class.php',
                    dest: 'core/components/rememberthis/model/rememberthis/rememberthis.class.php'
                }],
                options: {
                    replacements: [{
                        pattern: /version = '\d+.\d+.\d+[-a-z0-9]*'/ig,
                        replacement: 'version = \'' + '<%= modx.version %>' + '\''
                    }]
                }
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
    grunt.loadNpmTasks('grunt-postcss');
    grunt.loadNpmTasks('grunt-string-replace');
    grunt.renameTask('string-replace', 'bump');

    //register the task
    grunt.registerTask('default', ['bump', 'uglify', 'sass', 'postcss', 'cssmin', 'usebanner', 'sftp']);
};