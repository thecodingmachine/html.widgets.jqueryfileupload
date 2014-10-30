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
	public function handlePost() {
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
// Note: $fileName is the full path to the file.
$this->fileUploadWidget->addDefaultFile(new FileWidget($fileName, $uniqueId);
```


Handling deleted files
----------------------

When you display previously uploaded files, the user will be allowed to delete any previously uploaded file (using the cross next to the filename).
The list of deleted files unique id will be available if you set the deletedNames property.

Here is a sample code:

###src/views/MyController.php'
```php
class MyController extends Controller {

	public function __construct() {
		// Instead of creating the widget in the constructor, you will problably want to inject
		// it via dependency injection. We do this to keep the sample short enough.
		$this->fileUploadWidget = new JqueryFileUploadWidget();
		// The only property we really need to set is the "name" property.
		$this->fileUploadWidget->setName("files");
		$this->fileUploadWidget->setDeleteName("deleted_files");
	}

	/**
	 * @URL uploadtest
	 * @Get
	 */
	public function index() {
		// Let's add some previously uploaded files:
		$this->fileUploadWidget->addDefaultFile(new FileWidget(ROOT_PATH."/upload/myfile.jpg", 42);
	
		$this->content->addFile(ROOT_PATH.'src/views/upload.php');
		$this->template->toHtml();
	}
	
	/**
	 * @URL uploadtest
	 * @Post
	 */
	public function handlePost($deleted_files = array()) {
		foreach ($deleted_files as $deletedFileId) {
			// $deletedFileId is the unique ID of the file we passed to the FileWidget constructor
			// Let's admit we have some way to find the file from the ID using a getFileById function
			unlink($this->getFileById($deletedFileId));
		}
	
		// Once the post is received, the files are accessible through the getFiles method.
		foreach ($this->fileUploadWidget->getFiles() as $file) {
			$file->move(ROOT_PATH.'/tmp/');
		}
	}
}
```

 


Customizing the display
-----------------------

The JqueryFileUpload widget is using [Mouf's rendering mechanism](http://mouf-php.com/packages/mouf/html.renderer/README.md).
Therefore, you can easily overload the way it is displayed if you want to.

You just have to copy the `vendor/mouf/html.widgets.jqueryfileupload/src/templates/Mouf/Html/Widgets/JqueryFileUpload/FileWidget.php`
into `src/templates/Mouf/Html/Widgets/JqueryFileUpload/FileWidget.php` and customize it.

By default, the uploaded file renderer will display thumbnails for png, gif and jpg images.
If you want to simply modify the size of the thumbnails or the way they are displayed, you can have a look at the
`resizeUploadThumbnails` instance in Mouf.
