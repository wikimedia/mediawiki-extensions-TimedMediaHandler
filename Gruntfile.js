/* eslint-env node, es6 */
module.exports = function ( grunt ) {
	var conf = grunt.file.readJSON( 'extension.json' );

	grunt.loadNpmTasks( 'grunt-banana-checker' );
	grunt.loadNpmTasks( 'grunt-contrib-copy' );
	grunt.loadNpmTasks( 'grunt-eslint' );
	grunt.loadNpmTasks( 'grunt-exec' );
	grunt.loadNpmTasks( 'grunt-patcher' );
	grunt.loadNpmTasks( 'grunt-stylelint' );

	grunt.initConfig( {
		eslint: {
			options: {
				reportUnusedDisableDirectives: true,
				extensions: [ '.js', '.json' ],
				cache: true
			},
			all: [
				'**/*.js{,on}',
				// Third party resources
				'!resources/{videojs*,mwembed/lib}/**',
				'!{vendor,node_modules}/**'
			]
		},
		stylelint: {
			options: {
				syntax: 'less'
			},
			all: [
				'**/*.{css,less}',
				'!resources/mw-info-button/**',
				// Third party resources
				'!resources/mwembed/**',
				'!resources/videojs*/**',
				'!node_modules/**',
				'!vendor/**'
			]
		},
		banana: {
			options: {
				requireLowerCase: false
			},
			all: conf.MessagesDirs.TimedMediaHandler
		},
		exec: {
			'npm-update-videojs': {
				cmd: 'npm update ogv video.js videojs-resolution-switcher-v6 videojs-ogvjs videojs-responsive-layout',
				callback: function ( error, stdout, stderr ) {
					grunt.log.write( stdout );
					if ( stderr ) {
						grunt.log.write( 'Error: ' + stderr );
					}

					if ( error !== null ) {
						grunt.log.error( 'update error: ' + error );
					}
				}
			}
		},
		copy: {
			'ogv.js': {
				expand: true,
				cwd: 'node_modules/ogv/dist/',
				src: [
					'**'
				],
				dest: 'resources/mwembed/lib/binPlayers/ogv.js/'
			},
			'video.js': {
				expand: true,
				cwd: 'node_modules/video.js/dist/',
				src: [
					'**',
					'!alt/**',
					'!examples/**',
					'!*.zip',
					'!*.swf',
					'!**/*.min.js',
					'!**/*.min.css',
					'!**/*.js.map',
					'!**/*.cjs.js',
					'!**/*.es.js',
					'!ie8/**'
				],
				dest: 'resources/videojs/'
			},
			'videojs-ogvjs': {
				expand: true,
				cwd: 'node_modules/videojs-ogvjs/dist/',
				src: [ '**', '!**/*.min.js' ],
				dest: 'resources/videojs-ogvjs/'
			},
			'videojs-resolution-switcher': {
				expand: true,
				cwd: 'node_modules/videojs-resolution-switcher-v6/lib/',
				src: [ '**' ],
				dest: 'resources/videojs-resolution-switcher/'
			},
			'videojs-responsive-layout': {
				expand: true,
				cwd: 'node_modules/videojs-responsive-layout/dist/',
				src: [ '**' ],
				dest: 'resources/videojs-responsive-layout/'
			}
		},
		patch: {
			'videojs-ogvjs': {
				options: {
					patch: 'patches/videojs-ogvjs-webm.patch'
				},
				files: {
					'resources/videojs-ogvjs/videojs-ogvjs.js': 'resources/videojs-ogvjs/videojs-ogvjs.js'
				}
			},
			'videojs-responsive-layout': {
				options: {
					patch: 'patches/videojs-responsive-layout-ie11.patch'
				},
				files: {
					'resources/videojs-responsive-layout/videojs-responsive-layout.js': 'resources/videojs-responsive-layout/videojs-responsive-layout.js'
				}
			},
			'videojs-resolution-switcher': {
				options: {
					patch: 'patches/videojs-resolution-switcher-v6.patch'
				},
				files: {
					'resources/videojs-resolution-switcher/videojs-resolution-switcher.js': 'resources/videojs-resolution-switcher/videojs-resolution-switcher.js'
				}
			},
			'videojs-resolution-switcher-icon': {
				options: {
					patch: 'patches/videojs-resolution-switcher-v6-icon.patch'
				},
				files: {
					'resources/videojs-resolution-switcher/videojs-resolution-switcher.css': 'resources/videojs-resolution-switcher/videojs-resolution-switcher.css'
				}
			}
		}
	} );

	grunt.registerTask( 'update-videojs', [
		'exec:npm-update-videojs',
		'copy:video.js',
		'copy:videojs-resolution-switcher',
		'copy:videojs-ogvjs',
		'copy:videojs-responsive-layout',
		'patch:videojs-resolution-switcher',
		'patch:videojs-resolution-switcher-icon',
		'patch:videojs-ogvjs',
		'patch:videojs-responsive-layout'
	] );
	grunt.registerTask( 'update-ogvjs', [ 'exec:npm-update-videojs', 'copy:ogv.js' ] );
	grunt.registerTask( 'test', [ 'eslint', 'stylelint', 'banana' ] );
	grunt.registerTask( 'default', 'test' );
};
