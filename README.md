# PHPWebThread

The PHPWebThread class is very simple: Just load the file, create a instance of it passing as a argument a function with the part you want to load and start it! At the end of script, just call the static method PHPWebThread::printScripts(); and you have the parts specified loading asyncronously in every browser with support to HTML 5.

This is similar to BigPipe, just renamed and programated to allow more simplicity in the use.

The class have some requeriments:

- DOM extension - native in very much implementations of PHP
- Support to Sessions - to allow the class to localizate the file in which the functions is localizated

Is possible (i think) to adjust the Nginx configuration file to allow the use of this file too. But, it not the high priority now. Some priorities is localizated in below:

## TODO

Now, the class has good support to use. But, has some things to do:

- Organizate the class
- Documentate it (The class have a poor documentation, but we can change it!)
- Optimizate the Javascript file (with use of Ajax to load the Javascript/CSS files and other things)
- Remove the internal dependency of [LazyLoad] loader
- And what your imagination can do! :D

## Example of use

The class is very much simple to use. Just look at the example below:

    <?php
      require("PHPWebThread.php");
    ?>
    <html>
      <head>
        <title>Test</title>
      </head>
      <body>
        <?php
          function example(){
            echo "Hello! This is just a example"; // Just echo it, simple..
          }
          $thread = new PHPWebThread("example"); // Construct the class
          $thread->start(); // And start it
          PHPWebThread::printScripts(); // Oh, and echo the code to load the PHPWebThread.js file! (it's really needed)
        ?>
      </body>
    </html>

## Support

If you like this script, just start it! If you encountered a bug, just post a issue in this repository..Simple, huh?

## License

Oh, this class is licensed with the MIT license. And uses the code from the [LazyLoad] loader to load CSS and JS files required by the PHPWebThreads with callbacks.

## Questions?

Just e-mail to f.j.mota13 [at] gmail.com with the subject PHPWebThread ;)

## Install

To use the class, we recommend to alter the RewriteBase directive in the .htaccess. Just a tip. :P

[LazyLoad]: https://github.com/rgrove/lazyload/  "LazyLoad Loader"
