/* eslint-env node, es6 */
module.exports = function ( grunt ) {
	const conf = grunt.file.readJSON( 'extension.json' );

	grunt.loadNpmTasks( 'grunt-banana-checker' );
	grunt.loadNpmTasks( 'grunt-contrib-copy' );
	grunt.loadNpmTasks( 'grunt-eslint' );
	grunt.loadNpmTasks( 'grunt-exec' );
	grunt.loadNpmTasks( 'grunt-patcher' );
	grunt.loadNpmTasks( 'grunt-stylelint' );

	grunt.initConfig( {
		eslint: {
			options: {
				cache: true
			},
			all: '.'
		},
		stylelint: {
			all: [
				'**/*.{css,less}',
				'!resources/mw-info-button/**',
				// Third party resources
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
		copy: {
			'ogv.js': {
				expand: true,
				cwd: 'node_modules/ogv/dist/',
				src: [
					'**'
				],
				dest: 'resources/ogv.js/'
			},
			'video.js': {
				expand: true,
				cwd: 'node_modules/video.js/dist/',
				src: [
					'**',
					'!video.js',
					'!alt/video.core.js',
					'!alt/video.debug.js',
					'!alt/*.css',
					'!alt/*.novtt.js',
					'!alt/*.novtt.min.js',
					'!examples/**',
					'!font/**',
					'!types/**',
					'!*.zip',
					'!*.swf',
					'!lang/*.json',
					'!*.min.js',
					'!**/*.min.css',
					'!**/*.js.map',
					'!**/*.cjs.js',
					'!**/*.es.js',
					'!ie8/**'
				],
				dest: 'resources/videojs/'
			}
		}
	} );

	grunt.registerTask( 'update-videojs', [ 'copy:video.js' ] );
	grunt.registerTask( 'update-ogvjs', [ 'copy:ogv.js' ] );
	grunt.registerTask( 'test', [ 'eslint', 'stylelint', 'banana' ] );
	grunt.registerTask( 'default', 'test' );
};
