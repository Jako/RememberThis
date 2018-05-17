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
                    position: 'bottom',
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
            mgr: {
                src: [
                    'source/js/rememberthis.js'
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
                    'assets/components/rememberthis/css/rememberthis.css': 'source/sass/rememberthis.scss'
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
            css: {
                src: [
                    'assets/components/rememberthis/css/rememberthis.css'
                ],
                dest: 'assets/components/rememberthis/css/rememberthis.min.css'
            }
        },
        imagemin: {
            img: {
                options: {
                    optimizationLevel: 7,
                    svgoPlugins: [{removeViewBox: false}]
                },
                files: [{
                    expand: true,
                    cwd: 'source/img',
                    src: ['**/*.{png,jpg,gif}'],
                    dest: 'assets/components/rememberthis/img'
                }]
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
            js: {
                files: [
                    'assets/components/rememberthis/js/rememberthis.js'
                ],
                tasks: ['uglify', 'usebanner', 'sftp:js']
            },
            css: {
                files: [
                    'assets/components/rememberthis/sass/rememberthis.scss'
                ],
                tasks: ['sass', 'postcss', 'cssmin', 'usebanner', 'sftp:css']
            },
            config: {
                files: [
                    '_build/config.json'
                ],
                tasks: ['default']
            }
        },
        bump: {
            copyright: {
                files: [{
                    src: 'core/components/rememberthis/model/rememberthis/rememberthis.class.php',
                    dest: 'core/components/rememberthis/model/rememberthis/rememberthis.class.php'
                }],
                options: {
                    replacements: [{
                        pattern: /Copyright \d{4}(-\d{4})? by/g,
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
            },
            docs: {
                files: [{
                    src: 'mkdocs.yml',
                    dest: 'mkdocs.yml'
                }],
                options: {
                    replacements: [{
                        pattern: /&copy; \d{4}(-\d{4})?/g,
                        replacement: '&copy; ' + (new Date().getFullYear() > 2008 ? '2008-' : '') + new Date().getFullYear()
                    }]
                }
            }
        }
    });

    //load the packages
    grunt.loadNpmTasks('grunt-banner');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-imagemin');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-postcss');
    grunt.loadNpmTasks('grunt-sass');
    grunt.loadNpmTasks('grunt-ssh');
    grunt.loadNpmTasks('grunt-string-replace');
    grunt.renameTask('string-replace', 'bump');

    //register the task
    grunt.registerTask('default', ['bump', 'uglify', 'sass', 'postcss', 'cssmin', 'imagemin', 'usebanner', 'sftp']);
};
