How to use jQueryFileUpload
===========================

Absolutely minimal use of the widget
------------------------------------

In your controller, you need to create an instance of the `JqueryFileUploadWidget` class.
You can create this instance in Mouf, or right in your controller.

Assuming you are using Splash with a template, the minimal code to create a form with an upload widget would look like this:

###src/views/MyController.php'
```php
class MyController extends Controller {

	public function __construct() {
		// Instead of creating the widget in the constructor, you will problably want to inject
		// it via dependency injection. We do this to keep the sample short enough.
		$this->fileUploadWidget = new JqueryFileUploadWidget();
		// The only property we really need to set is the "name" property.
		$this->fileUploadWidget->setName("files");
	}

	/**
	 * @URL uploadtest
	 * @Get
	 */
	public function index() {
		$this->content->addFile(ROOT_PATH.'src/views/upload.php');
		$this->template->toHtml();
	}
	
	/**
	 * @URL uploadtest
	 * @Post
	 */
	public function testdavid3post() {
		// Once the post is received, the files are accessible through the getFiles method.
		foreach ($this->fileUploadWidget->getFiles() as $file) {
			$file->move(ROOT_PATH.'/tmp/');
		}
	}
}
```

###src/views/upload.php'
```php
<form method='post'>
	<?php 
		// Let's render the widget
		$fileUploadWidget->toHtml();
	?>
	<button type='submit'>Submit</button>
</form>
```

As you can see, this is fairly minimal.
Here is what happens behind the scene.

When the widget is displayed on the screen, the user can upload any number of files. The files are directed to a temporary
directory on the server. When the user submits the form, the files are fetched from the temporary directory (using the
`getFiles` method). From there, you can do whatever you want with the files. Most likely, you will want to move them
in a special directory to archive them, or you may want to rename them. 


Displaying previously uploaded files
------------------------------------

If you are displaying the widget in a form in edit mode, you will want to display the previously uploaded files next to the upload
button. You can do this with the widget using the `addDefaultFile` method:

```php

$this->fileUploadWidget->addDefaultFile(new FileWidget($fileName, $uniqueId);
```

Customizing the display
-----------------------

The JqueryFileUpload widget is using [Mouf's rendering mechanism](http://mouf-php.com/packages/mouf/html.renderer/README.md).
Therefore, you can easily overload the way it is displayed if you want to.
