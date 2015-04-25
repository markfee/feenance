//Gruntfile
module.exports = function(grunt) {
  grunt.registerTask('pig', 'My "pig" task.', function() {
    grunt.log.writeln('Oink! Oink!');
  });
  //Initializing the configuration object
    grunt.initConfig( {

      // Task configuration
      concat: {
        //...
      },
      less: {
        development: {
          options: {
            compress: false //minifying the result
          },
          files: {
            //compiling frontend.less into frontend.css
            "./public/assets/stylesheets/frontend.css": "./app/assets/stylesheets/frontend.less",
            //compiling backend.less into backend.css
            "./public/assets/stylesheets/backend.css": "./app/assets/stylesheets/backend.less"
          }
        }
      },
      uglify: {
        //...
      },
      phpunit: {
        //...
      },
      watch: {
          src: {
              files: ["./app/assets/stylesheets/*.less"],
              tasks: ["less"]
          }
      }
    });

  // Plugin loading
    grunt.loadNpmTasks('grunt-contrib-less');
    grunt.loadNpmTasks('grunt-contrib-watch');


  // Task definition

};
