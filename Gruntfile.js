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
				'!MwEmbedModules/**',
				'!resources/videojs*/**',
				'!resources/mw-info-button/**',
				'!node_modules/**'
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
				'!node_modules/**'
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
				'!node_modules/**'
			]
		},
		exec: {
			'npm-update-videojs': {
				cmd: 'npm update video.js videojs-resolution-switcher videojs-ogvjs videojs-responsive-layout videojs-replay',
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
			'video.js': {
				expand: true,
				cwd: 'node_modules/video.js/dist/',
				src: [
					'**',
					'!alt/**',
					'!examples/**',
					'!*.zip',
					'!**/*.min.js',
					'!**/*.min.css',
					'!**/*.js.map'
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
				cwd: 'node_modules/videojs-resolution-switcher/lib/',
				src: [ '**' ],
				dest: 'resources/videojs-resolution-switcher/'
			},
			'videojs-responsive-layout': {
				expand: true,
				cwd: 'node_modules/videojs-responsive-layout/dist/',
				src: [ '**' ],
				dest: 'resources/videojs-responsive-layout/'
			},
			'videojs-replay': {
				expand: true,
				cwd: 'node_modules/videojs-replay/dist/',
				src: [ '**', '!**/*.min.js' ],
				dest: 'resources/videojs-replay/'
			}
		},
		patch: {
			'video.js': {
				options: {
					patch: 'patches/videojs.defaults.patch'
				},
				files: {
					'resources/videojs/video.js': 'resources/videojs/video.js'
				}

			}
		}
	} );

	grunt.registerTask( 'update-videojs', [ 'exec:npm-update-videojs', 'copy:video.js', 'copy:videojs-resolution-switcher', 'copy:videojs-ogvjs', 'copy:videojs-responsive-layout', 'copy:videojs-replay', 'patch:video.js' ] );
	grunt.registerTask( 'test', [ 'eslint', 'stylelint', 'jsonlint', 'banana' ] );
	grunt.registerTask( 'default', 'test' );
};
