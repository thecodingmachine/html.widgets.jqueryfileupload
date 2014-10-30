<?php 
namespace Mouf\Html\Widgets\JqueryFileUpload;

// The blueimp package does not feature an autoloader, so let's autoload manually.
require_once __DIR__.'/../../../../../../../blueimp/jquery-file-upload/server/php/UploadHandler.php';

/**
 * A class extending and customizing the behaviour of the default UploadHandler
 * provided by the blueimp/jquery-file-upload component.
 * 
 * @author David Négrier <david@mouf-php.com>
 */
class MoufUploadHandler extends \UploadHandler {
	
	protected function set_additional_file_properties($file) {
		parent::set_additional_file_properties($file);
		
		$file_path = $this->get_upload_path($file->name);
		
		// Let's add the HTML representing the file to the object returned.
		$fileWidget = new FileWidget($file_path, md5($file_path));
		
		ob_start();
		$fileWidget->toHtml();
		$file->html = ob_get_clean();  
	}
}