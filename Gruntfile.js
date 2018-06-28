/* eslint-env node */
module.exports = function ( grunt ) {
	grunt.loadNpmTasks( 'grunt-banana-checker' );
	grunt.loadNpmTasks( 'grunt-contrib-copy' );
	grunt.loadNpmTasks( 'grunt-eslint' );
	grunt.loadNpmTasks( 'grunt-exec' );
	grunt.loadNpmTasks( 'grunt-jsonlint' );
	grunt.loadNpmTasks( 'grunt-patcher' );
	grunt.loadNpmTasks( 'grunt-stylelint' );

	grunt.initConfig( {
		eslint: {
			all: [
				'**/*.js',
				'!resources/videojs*/**',
				// TODO: Third party resources should be moved to "lib" folders
				'!MwEmbedModules/EmbedPlayer/binPlayers/**',
				'!MwEmbedModules/NewMwEmbedSupport/fullScreenApi/**',
				'!MwEmbedModules/NewMwEmbedSupport/iscroll/**',
				'!MwEmbedModules/NewMwEmbedSupport/fullScreenApi/**',
				'!MwEmbedModules/NewMwEmbedSupport/jquery/jquery.debouncedresize.js',
				'!MwEmbedModules/NewMwEmbedSupport/jquery/jquery.ui.touchPunch.js',
				'!MwEmbedModules/NewMwEmbedSupport/jquery.embedMenu/**',
				'!MwEmbedModules/NewMwEmbedSupport/jquery.loadingSpinner/Spinner.js',
				'!node_modules/**',
				'!vendor/**'
			]
		},
		stylelint: {
			options: {
				syntax: 'less'
			},
			all: [
				'**/*.{css,less}',
				'!MwEmbedModules/**',
				'!resources/videojs*/**',
				'!resources/mw-info-button/**',
				'!node_modules/**',
				'!vendor/**'
			]
		},
		banana: {
			all: 'i18n/',
			EmbedPlayer: 'MwEmbedModules/EmbedPlayer/i18n/',
			TimedText: 'MwEmbedModules/TimedText/i18n/'
		},
		jsonlint: {
			all: [
				'*.json',
				'**/*.json',
				'!node_modules/**',
				'!vendor/**'
			]
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
				dest: 'MwEmbedModules/EmbedPlayer/binPlayers/ogv.js/'
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
			videojs: {
				options: {
					patch: 'patches/videojs-responsive-classes.patch'
				},
				files: {
					'resources/videojs/video-js.css': 'resources/videojs/video-js.css'
				}
			},
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
			}
		}
	} );

	grunt.registerTask( 'update-videojs', [ 'exec:npm-update-videojs', 'copy:video.js', 'copy:videojs-resolution-switcher', 'copy:videojs-ogvjs', 'copy:videojs-responsive-layout', 'patch:videojs', 'patch:videojs-resolution-switcher', 'patch:videojs-ogvjs', 'patch:videojs-responsive-layout' ] );
	grunt.registerTask( 'update-ogvjs', [ 'exec:npm-update-videojs', 'copy:ogv.js' ] );
	grunt.registerTask( 'test', [ 'eslint', 'stylelint', 'jsonlint', 'banana' ] );
	grunt.registerTask( 'default', 'test' );
};
