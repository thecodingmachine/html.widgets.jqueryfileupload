JqueryFileUpload widget installation guide
==========================================

The jQueryFileUpload widget for Mouf combines HTML, JS and PHP code to let you upload files on your website.
It is designed to integrate smoothly in a Mouf application, and being based on the [renderer mechanism](http://mouf-php.com/packages/mouf/html.renderer/README.md), 
the appearance of the file uploader is fully customizable.

Moreover, it is possible to send many files at the same time and drag and drop them in the browser.

Dependencies
------------

jQueryFileUpload comes as a *Composer* package and requires the "Mouf" framework to run.
The first step is therefore to [install Mouf](http://www.mouf-php.com/).

Once Mouf is installed, you can process to the jQueryFileUpload widget installation.

Install jQueryFileUpload widget
-------------------------------

Edit your *composer.json* file, and add a dependency on *mouf/html.widgets.jqueryfileupload*.

A minimal *composer.json* file might look like this:
```
	{
	    "require": {
	        "mouf/mouf": "~2.0",
	        "mouf/html.widgets.jqueryfileupload": "dev-master"
	    },
	    "prefer-stable": true,
	    "minimum-stability": "dev"
	}
```
As explained above, jQueryFileUpload is a package of the [Mouf framework](http://mouf-php.com). Mouf allows you (amoung other things) to visualy "build" your project's dependencies and instances.

At this point, the FileUploader packages is installed. You should be created instance to implement it. Start the Mouf admin interface at http://localhost/{yourproject}/vendor/mouf/mouf

[We can now jump to the tutorial](tutorial.md)