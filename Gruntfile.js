/*jshint node:true */
module.exports = function ( grunt ) {
	grunt.loadNpmTasks( 'grunt-contrib-copy' );
	grunt.loadNpmTasks( 'grunt-contrib-jshint' );
	grunt.loadNpmTasks( 'grunt-jsonlint' );
	grunt.loadNpmTasks( 'grunt-banana-checker' );
	grunt.loadNpmTasks( 'grunt-jscs' );
	grunt.loadNpmTasks( 'grunt-exec' );
	grunt.loadNpmTasks( 'grunt-patch' );

	grunt.initConfig( {
		jshint: {
			options: {
				jshintrc: true
			},
			all: [
				'**/*.js',
				'!MwEmbedModules/**',
				'!resources/videojs*/**',
				'!node_modules/**'
			]
		},
		jscs: {
			src: '<%= jshint.all %>'
		},
		banana: {
			all: 'i18n/'
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
				cmd: 'npm update video.js videojs-resolution-switcher',
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
			'videojs-resolution-switcher': {
				expand: true,
				cwd: 'node_modules/videojs-resolution-switcher/lib/',
				src: [ '**' ],
				dest: 'resources/videojs-resolution-switcher/'
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

	grunt.registerTask( 'update-videojs', [ 'exec:npm-update-videojs', 'copy:video.js', 'copy:videojs-resolution-switcher', 'patch:video.js' ] );
	grunt.registerTask( 'test', [ 'jshint', 'jscs', 'jsonlint', 'banana' ] );
	grunt.registerTask( 'default', 'test' );
};
