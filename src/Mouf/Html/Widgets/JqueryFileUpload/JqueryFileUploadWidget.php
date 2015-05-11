<?php
namespace Mouf\Html\Widgets\JqueryFileUpload;


use Mouf\MoufManager;
use Mouf\Html\HtmlElement\HtmlElementInterface;
use Mouf\Utils\Value\ValueUtils;
use Mouf\Utils\Value\ValueInterface;
use Mouf\Html\Renderer\Renderable;

/**
 * This class represent an HTML file upload widget.
 *
 */
class JqueryFileUploadWidget implements HtmlElementInterface {
	use Renderable {
		Renderable::toHtml as toHtmlParent;
	}
	
	/**
	 * Number of fields displayed
	 *
	 * @var int
	 */
	protected static $count = 0;
	protected $number;
	
	// TODO: think about replacing this with an object in charge of pointing to the right place.
	protected $uploadDir;
	
	protected $name;
	// Name of the hidden input fields that contains the list of files to delete (if any)
	protected $deleteName;
	protected $formAcceptCharset;
	protected $dropZoneCssSelector = "body";
	protected $pasteZoneCssSelector = "body";
	protected $sequentialUploads;
	protected $limitConcurrentUploads;
	protected $progressInterval;
	protected $bitrateInterval;
	protected $formData;
	protected $embedFormData;
	
	protected $acceptFileTypes;
	protected $maxFileSize;
	protected $minFileSize;
	protected $maxNumberOfFiles;
	protected $disableValidation;
	
	// A unique token representing the folder containing the files uploaded via this file uploader.
	protected $token;
	
	/**
	 * @var JQueryFileUploadListenerInterface[]
	 */
	protected $uploadListeners = [];
	
	/**
	 * The list of files that will be displayed below the file upload.
	 * @var FileWidget[]
	 */
	protected $files = array();
	
	public function __construct() {
		$this->number = self::$count;
		self::$count++;
	}
	
	/**
	 * Sets the name attribute of the hidden input field that will contain the token pointing to the directory that
	 * contains the files. 
	 * 
	 * @param string $name
	 * @return self
	 */
	public function setName($name) {
		$this->name = $name;
		return $this;
	}
	
	public function getName() {
		if ($this->name !== null) {
			return $this->name;
		} else {
			return "jquery_mouf_fileupload_".$this->number;
		}
	}
	
	/**
	 * Sets the name attribute of the hidden input field that will contain the list of files to delete
	 * (in the case of files already there by default that we need to remove) 
	 * 
	 * @param string $deleteName
	 * @return self
	 */
	public function setDeleteName($deleteName) {
		$this->deleteName = $deleteName;
		return $this;
	}
	
	public function getDeleteName() {
		if ($this->deleteName !== null) {
			return $this->deleteName;
		} else {
			return $this->getName()."_delete";
		}
	}
	
	/**
	 * The id of the file uploader DOM element
	 * @param string $id
	 */
	public function setId($id){
		$this->id = $id;
	}
		
	/**
	 * Create a unique token representing the directory that
	 * contains the files (and stores the token in the session.
	 * 
	 * @return string
	 */
	protected function createNewToken() {
		$moufManager = MoufManager::getMoufManager();
		$moufManager->getInstance('sessionManager')->start();
		
		$this->token = date('YmdHis').rand(0, 9999999);
		
		$_SESSION["mouf_jqueryfileupload_autorizeduploads"][$this->token] = $this->getTmpDirectory();
		
		return $this->token;
	}
	
	protected function getTokenFromRequest() {
		$this->token = $_REQUEST[$this->getName()];
		return $this->token;
	}
	
	/**
	 * Returns a unique token representing the directory that
	 * contains the files (and stores the token in the session.
	 * 
	 * @return string
	 */
	public function getToken() {
		return $this->token;
	}
	
	/**
	 * Returns the fully qualified path to a temporary directory where uploaded files will be stored.
	 */
	protected function getTmpDirectory() {
		return sys_get_temp_dir().'/mouf_jqueryfileupload_files/'.$this->token.'/';
	}
	
	/**
	 * Returns the absolute path of the upload directory.
	 * @return string
	 */
	/*public function getAbsoluteUploadDir() {
		return ROOT_PATH.$this->uploadDir;
	}*/
	
	/**
	 * Upload directory, relative to ROOT_PATH.
	 * 
	 * @param string $uploadDir
	 */
	/*public function setUploadDir($uploadDir) {
		$this->uploadDir = $uploadDir;
	}*/
	
	/**
	 * The parameter name for the file form data (the request argument name).
	 * If undefined or empty, the name property of the file input field is used, or "files[]" if the file input name property is also empty. Can be a string or an array of strings.
	 * 
	 * See: https://github.com/blueimp/jQuery-File-Upload/wiki/Options#paramname
	 * 
	 * @param string|array<string> $name
	 * @return \Mouf\Html\Widgets\FileUploaderWidget\JqueryFileUploadWidget
	 */
	/*public function setName($name) {
		$this->name = $name;
		return $this;
	}*/
	
	/**
	 * Allows to set the accept-charset attribute for the iframe upload forms.
	 * If formAcceptCharset is not set, the accept-charset attribute of the file upload widget form is used, if available.
	 *
	 * See: https://github.com/blueimp/jQuery-File-Upload/wiki/Options#formacceptcharset
	 * @param string $formAcceptCharset
	 * @return \Mouf\Html\Widgets\FileUploaderWidget\JqueryFileUploadWidget
	 */
	public function setFormAcceptCharset($formAcceptCharset) {
		$this->formAcceptCharset = $formAcceptCharset;
		return $this;
	}
	
	
	/**
	 * The drop target CSS selector, by default the complete document.
	 * Set to null to disable drag & drop support.
	 * 
	 * See: https://github.com/blueimp/jQuery-File-Upload/wiki/Options#dropzone
	 * 
	 * @param string $dropZoneCssSelector
	 * @return \Mouf\Html\Widgets\FileUploaderWidget\JqueryFileUploadWidget
	 */
	public function setDropZoneCssSelector($dropZoneCssSelector) {
		$this->dropZoneCssSelector = $dropZoneCssSelector;
		return $this;
	}
		
	/**
	 * The paste target CSS selector, by the default the complete document.
	 * Set to null to disable paste support:
	 * 
	 * See: https://github.com/blueimp/jQuery-File-Upload/wiki/Options#pastezone
	 * 
	 * @param string $pasteZoneCssSelector
	 * @return \Mouf\Html\Widgets\FileUploaderWidget\JqueryFileUploadWidget
	 */
	public function setPasteZoneCssSelector($pasteZoneCssSelector) {
		$this->pasteZoneCssSelector = $pasteZoneCssSelector;
		return $this;
	}
	
	/**
	 * Set this option to true to issue all file upload requests in a sequential order instead of simultaneous requests.
	 * Default: false
	 * 
	 * See: https://github.com/blueimp/jQuery-File-Upload/wiki/Options#sequentialuploads
	 * 
	 * @param bool $sequentialUploads
	 * @return \Mouf\Html\Widgets\FileUploaderWidget\JqueryFileUploadWidget
	 */
	public function setSequentialUploads($sequentialUploads) {
		$this->sequentialUploads = $sequentialUploads;
		return $this;
	}
	
	/**
	 * To limit the number of concurrent uploads, set this option to an integer value greater than 0.
	 * Default: undefined
	 * Note: This option is ignored, if sequentialUploads is set to true.
	 * 
	 * See: https://github.com/blueimp/jQuery-File-Upload/wiki/Options#sequentialuploads
	 * 
	 * @param int $limitConcurrentUploads
	 * @return \Mouf\Html\Widgets\FileUploaderWidget\JqueryFileUploadWidget
	 */
	public function setLimitConcurrentUploads($limitConcurrentUploads) {
		$this->limitConcurrentUploads = $limitConcurrentUploads;
		return $this;
	}
	
	/**
	 * The minimum time interval in milliseconds to calculate and trigger progress events.
	 * Default: 100
	 * 
	 * See: https://github.com/blueimp/jQuery-File-Upload/wiki/Options#progressinterval
	 * 
	 * @param int $progressInterval
	 * @return \Mouf\Html\Widgets\FileUploaderWidget\JqueryFileUploadWidget
	 */
	public function setProgressInterval($progressInterval) {
		$this->progressInterval = $progressInterval;
		return $this;
	}
	
	/**
	 * The minimum time interval in milliseconds to calculate progress bitrate.
	 * Default: 100
	 * 
	 * See: https://github.com/blueimp/jQuery-File-Upload/wiki/Options#bitrateinterval
	 * 
	 * @param int $bitrateInterval
	 * @return \Mouf\Html\Widgets\FileUploaderWidget\JqueryFileUploadWidget
	 */
	public function setBitrateInterval($bitrateInterval) {
		$this->bitrateInterval = $bitrateInterval;
		return $this;
	}
	
	/**
	 * Additional form data to be sent along with the file uploads can be set using this option, which accepts an array of key=>value properties.
	 * Warning! These data are passed client side, so they are not safe.
	 * 
	 * @param array<string, string> $formData
	 * @return \Mouf\Html\Widgets\FileUploaderWidget\JqueryFileUploadWidget
	 */
	public function setFormData($formData) {
		$this->formData = $formData;
		return $this;
	}
					
	
	/**
	 * Set this to "true" to embed the current state of the form being filled during the upload process.
	 *
	 * @param bool $embedFormData
	 * @return \Mouf\Html\Widgets\FileUploaderWidget\JqueryFileUploadWidget
	 */
	public function setEmbedFormData($embedFormData) {
		$this->embedFormData = $embedFormData;
		return $this;
	}
	
	/**
	 * The regular expression for allowed file types, matches against either file type or file name as only browsers with support for the File API report the file type.
	 * Example: /(\.|\/)(gif|jpe?g|png)$/i
	 * 
	 * See: https://github.com/blueimp/jQuery-File-Upload/wiki/Options#acceptfiletypes
	 * 
	 * @param string $acceptFileTypes
	 */
	public function setAcceptFileTypes($acceptFileTypes) {
		$this->acceptFileTypes = $acceptFileTypes;
		return $this;
	}
	
	/**
	 * The maximum allowed file size in bytes.
	 * Example: 10000000 // 10 MB
	 * 
	 * Note: This option has only an effect for browsers supporting the File API.
	 * 
	 * See: https://github.com/blueimp/jQuery-File-Upload/wiki/Options#maxfilesize
	 * @param int $maxFileSize        	
	 */
	public function setMaxFileSize($maxFileSize) {
		$this->maxFileSize = $maxFileSize;
		return $this;
	}
	
	/**
	 * The minimum allowed file size in bytes.
	 * Example: 1 // 1 Byte
	 * 
	 * Note: This option has only an effect for browsers supporting the File API.
	 * 
	 * See: https://github.com/blueimp/jQuery-File-Upload/wiki/Options#minfilesize
	 * 
	 * @param int $minFileSize        	
	 */
	public function setMinFileSize($minFileSize) {
		$this->minFileSize = $minFileSize;
		return $this;
	}
	
	/**
	 * This option limits the number of files that are allowed to be uploaded using this widget.
	 * By default, unlimited file uploads are allowed.
	 * 
	 * See: https://github.com/blueimp/jQuery-File-Upload/wiki/Options#maxnumberoffiles
	 * 
	 * @param int $maxNumberOfFiles        	
	 */
	public function setMaxNumberOfFiles($maxNumberOfFiles) {
		$this->maxNumberOfFiles = $maxNumberOfFiles;
		return $this;
	}
	
	/**
	 * Disables file validation.
	 * 
	 * See: https://github.com/blueimp/jQuery-File-Upload/wiki/Options#disablevalidation
	 * @param bool $disableValidation        	
	 */
	public function setDisableValidation($disableValidation) {
		$this->disableValidation = $disableValidation;
		return $this;
	}
			
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
// 	/**
// 	 * Debug mode.
// 	 * Set to true to output server response to console
// 	 * 
// 	 * @Property
// 	 * @var boolean
// 	 */
// 	public $debug = false;
	
// 	/**
// 	 * The destination directory for the file to be written. 
// 	 * If it does not start with "/", this is relative to ROOT_PATH.
// 	 * The directory is created if it does not exist.
// 	 * 
// 	 * You can of course set this value dynamically, in your code, using
// 	 * <pre>$instance->directory = "my/directory";</pre>
// 	 * 
// 	 * @Property
// 	 * @var string
// 	 */
// 	public $directory;
	
// 	/**
// 	 * The destination file name for the file to be written.
// 	 * This is a unique file name and cannot contain "/".
// 	 *
// 	 * Most of the time, you will set this value dynamically, in your code, using
// 	 * <pre>$instance->fileName = "myFileName.ext";</pre>
// 	 * 
// 	 * If not set, the name of the file provided by the user is used instead.
// 	 * 
// 	 * @Property
// 	 * @var string
// 	 */
// 	public $fileName;
	
// 	/**
// 	 * If you want to trigger some code when the file is uploaded, you will need to give the file a unique ID.
// 	 * You should set this ID programmatically, using:
// 	 * <pre>$instance->fileId = $myId;</pre>
// 	 * Then, you should register a listener that will be triggered when the file is uploaded (see the "listeners"
// 	 * property). The ID will be passed to the listener when an upload is completed.
// 	 * 
// 	 * @Property
// 	 * @var string
// 	 */
// 	public $fileId;
	
// 	/**
// 	 * Enable or disabled the multiple file upload.
// 	 * Authorize the user to send many file in 1 time (by drag & drop or explorer).
// 	 * Note: It's only a graphic parameter.
// 	 * 
// 	 * @Property
// 	 * @var boolean
// 	 */
// 	public $multiple = true;
	
// 	/**
// 	 * Replace the other file if they has the same folder and name.
// 	 * If disabled the file is renamed with _ and random number.
// 	 * By default replace is true
// 	 * 
// 	 * @Property
// 	 * @var boolean
// 	 */
// 	public $replace = true;
	
// 	/**
// 	 * A list of instances that will be notified when an upload occurs.
// 	 * To be registered, an instance should implement the FileUploaderOnUploadInterface interface.
// 	 * 
// 	 * @Property
// 	 * @var array<FileUploaderOnUploadInterface>
// 	 */
// 	public $listenersBefore;
	
// 	/**
// 	 * A list of instances that will be notified when an upload occurs.
// 	 * To be registered, an instance should implement the FileUploaderOnUploadInterface interface.
// 	 * 
// 	 * @Property
// 	 * @var array<FileUploaderOnUploadInterface>
// 	 */
// 	public $listenersAfter;
	
// 	/**
// 	 * The name of the javascript function to trigger on upload completed.
// 	 * Function called : function(id, fileName, responseJSON){} 
// 	 * 
// 	 * @Property
// 	 * @var string
// 	 */
// 	public $onComplete;
	
// 	/**
// 	 * The name of the javascript function to trigger on upload progress.
// 	 * Function called : function(id, fileName, loaded, total){} 
// 	 * 
// 	 * @Property
// 	 * @var string
// 	 */
// 	public $onProgress;
	
// 	/**
// 	 * The name of the javascript function to trigger on submit.
// 	 * Function called : function(id, fileName){} 
// 	 * 
// 	 * @Property
// 	 * @var string
// 	 */
// 	public $onSubmit;
	
// 	/**
// 	 * The name of the javascript function to trigger on cancel.
// 	 * Function called : function(id, fileName){} 
// 	 * 
// 	 * @Property
// 	 * @var string
// 	 */
// 	public $onCancel;
	
// 	/**
// 	 * The name of the javascript function to trigger on send message.
// 	 * Function called : function(message){ alert(message); }
// 	 * 
// 	 * @Property
// 	 * @var string
// 	 */
// 	public $showMessage;
	

// 	/**
// 	 * Text display by default on the file upload buttom
// 	 *
// 	 * @Property
// 	 * @var string|ValueInterface
// 	 */
// 	public $textDefault;

// 	/**
// 	 * Text display on hover of the file upload buttom
// 	 *
// 	 * @Property
// 	 * @var string|ValueInterface
// 	 */
// 	public $textHover;

// 	/**
// 	 * Text display to cancel the upload
// 	 *
// 	 * @Property
// 	 * @var string|ValueInterface
// 	 */
// 	public $textCancel;

// 	/**
// 	 * Text display when the file failed
// 	 *
// 	 * @Property
// 	 * @var string|ValueInterface
// 	 */
// 	public $textFailed;
	
// 	/**
// 	 * If you want to add parameter to your application
// 	 * The parameters must be serializable, to be saved in SESSION
// 	 * 
// 	 * @var mixed
// 	 */
// 	private $params;
	
	
	protected $id;
	protected $options;
	
	/**
	 * Renders the object in HTML.
	 * The Html is echoed directly into the output.
	 *
	 */
	public function toHtml() {
		if($this->id == null){
			$this->id = "jquery_mouf_fileupload_".$this->number;
		}
		
		$options = [];
		/*if ($this->name !== null) {
			$this->options['paramName'] = $this->name;
		}*/
		if ($this->formAcceptCharset !== null) {
			$this->options['formAcceptCharset'] = $this->formAcceptCharset;
		}
		if ($this->dropZoneCssSelector !== null) {
			$this->options['dropZone'] = $this->dropZoneCssSelector;
		}
		if ($this->pasteZoneCssSelector !== null) {
			$this->options['pasteZone'] = $this->pasteZoneCssSelector;
		}
		if ($this->sequentialUploads !== null) {
			$this->options['sequentialUploads'] = $this->sequentialUploads;
		}
		if ($this->limitConcurrentUploads !== null) {
			$this->options['limitConcurrentUploads'] = $this->limitConcurrentUploads;
		}
		if ($this->progressInterval !== null) {
			$this->options['progressInterval'] = $this->progressInterval;
		}
		if ($this->bitrateInterval !== null) {
			$this->options['bitrateInterval'] = $this->bitrateInterval;
		}
		if ($this->formData !== null) {
			$this->options['formData'] = $this->formData;
		}
		if ($this->acceptFileTypes !== null) {
			$this->options['acceptFileTypes'] = $this->acceptFileTypes;
		}
		if ($this->maxFileSize !== null) {
			$this->options['maxFileSize'] = $this->maxFileSize;
		}
		if ($this->minFileSize !== null) {
			$this->options['minFileSize'] = $this->minFileSize;
		}
		if ($this->maxNumberOfFiles !== null) {
			$this->options['maxNumberOfFiles'] = $this->maxNumberOfFiles;
		}
		if ($this->disableValidation !== null) {
			$this->options['disableValidation'] = $this->disableValidation;
		}
		
		$this->options['url'] = ROOT_URL.'vendor/mouf/html.widgets.jqueryfileupload/src/direct/upload.php';
		
		// TODO: embedFormData
		
		// Start a session using the session manager.
		$token = $this->createNewToken();
		
		$this->options['formData'][] = [
			"name" => "jqueryFileUploadUniqueId",
			"value" => $token
		];
		$this->options['formData'][] = [
			"name" => "instanceName",
			"value" => MoufManager::getMoufManager()->findInstanceName($this)
		];
		
		self::toHtmlParent();

		
		
//		$moufManager = MoufManager::getMoufManager();
//		$moufManager->findInstanceName($this);
//		$thisInstanceName = $moufManager->findInstanceName($this);
		
// 		$_SESSION["mouf_fileupload_autorizeduploads"][$uniqueId] = array(/*"path"=>$this->getFileUploadPath(),
// 				"fileId"=>$this->fileId,*/
// 				"instanceName"=>$thisInstanceName);
		/*if($this->params === null)
			$this->params = array();
		$_SESSION["mouf_fileupload_autorizeduploads"][$uniqueId]['params'] = serialize($this->params);*/
	}
	
	/**
	 *
	 * @return array
	 */
	public function getOptions() {
		return $this->options;
	}
	
	/**
	 * 
	 * @return string
	 */
	public function getId() {
		return $this->id;
	}
	public function getUploadDir() {
		return $this->uploadDir;
	}
	public function setUploadDir($uploadDir) {
		$this->uploadDir = $uploadDir;
		return $this;
	}
	
	
// 	/**
// 	 * Return an HTML string to render the object
// 	 *
// 	 */
// 	public function returnHtmlString() {
// 		self::$count++;
// 		$id = "mouf_fileupload_".self::$count;
	
// 		//Parse InlineStyles
// 		$styles = "";
// 		if ($this->useInlineStyles){
// 			foreach ($this->inlineStyles as $attribute => $value){
// 				$styles .= $attribute.": ".$value."; ";
// 			}
// 		}
	
// 		$html = '<div id="'.plainstring_to_htmlprotected($id).'" style="'.$styles.'">
// 		<noscript><p>Please enable JavaScript to use file uploader.</p></noscript>
// 		</div>';
	
// 		$version = basename(dirname(__FILE__));
	
// 		$uniqueId = date('YmdHis').rand(0, 9999999);
// 		$moufManager = MoufManager::getMoufManager();
// 		$moufManager->findInstanceName($this);
// 		$thisInstanceName = $moufManager->findInstanceName($this);
	
// 		$scriptDataArray = array("uniqueId"=>$uniqueId,
// 				"path" =>$this->getFileUploadPath(),
// 				"fileId" =>$this->fileId,
// 				"instanceName" =>$thisInstanceName);
// 		if($this->fileName)
// 			$scriptDataArray['fileName']= $this->fileName;
			
// 		$fileUploaderParam = array();
// 		if($this->fileExtensions) {
// 			$fileUploaderParam['allowedExtensions'] = '['.$this->fileExtensions.']';
// 		}
// 		if($this->multiple)
// 			$fileUploaderParam['multiple'] = 'true';
// 		else
// 			$fileUploaderParam['multiple'] = 'false';
// 		if($this->sizeLimit) {
// 			$fileUploaderParam['sizeLimit'] = $this->sizeLimit;
// 		}
// 		if($this->minSizeLimit) {
// 			$fileUploaderParam['minSizeLimit'] = $this->minSizeLimit;
// 		}
// 		if($this->multiple)
// 			$fileUploaderParam['debug'] = 'true';
// 		else
// 			$fileUploaderParam['debug'] = 'false';
// 		if($this->onCancel) {
// 			$fileUploaderParam['onCancel'] = $this->onCancel;
// 		}
// 		if($this->onComplete) {
// 			$fileUploaderParam['onComplete'] = $this->onComplete;
// 		}
// 		if($this->onProgress) {
// 			$fileUploaderParam['onProgress'] = $this->onProgress;
// 		}
// 		if($this->onSubmit) {
// 			$fileUploaderParam['onSubmit'] = $this->onSubmit;
// 		}
// 		if($this->showMessage) {
// 			$fileUploaderParam['showMessage'] = $this->showMessage;
// 		}
// 		$html .= '<script type="text/javascript">
// 		var uploader'.self::$count.' = new qq.FileUploader({
// 		element: document.getElementById("'.plainstring_to_htmlprotected($id).'"),
// 		action: "'.ROOT_URL.'vendor/mouf/html.widgets.fileuploaderwidget/src/direct/upload.php",';
// 		if($this->textDefault) {
// 			$html .= 'textDefault: "'.ValueUtils::val($this->textDefault).'",';
// 		}
// 		if($this->textHover) {
// 			$html .= 'textHover: "'.ValueUtils::val($this->textHover).'",';
// 		}
// 		if($this->textCancel) {
// 			$html .= 'textCancel: "'.ValueUtils::val($this->textCancel).'",';
// 		}
// 		if($this->textFailed) {
// 			$html .= 'textFailed: "'.ValueUtils::val($this->textFailed).'",';
// 		}
// 		$html .= 'params: '.json_encode($scriptDataArray);
// 		foreach ($fileUploaderParam as $key => $value) {
// 			$html .= ','.$key.':'.$value;
// 		}
			
// 		$html .= '});
// 		</script>';
	
// 		// Start a session using the session manager.
// 		$moufManager = MoufManager::getMoufManager();
// 		$moufManager->getInstance('sessionManager')->start();
	
// 		$_SESSION["mouf_fileupload_autorizeduploads"][$uniqueId] = array("path"=>$this->getFileUploadPath(),
// 				"fileId"=>$this->fileId,
// 				"instanceName"=>$thisInstanceName);
// 		if($this->params === null)
// 			$this->params = array();
// 		$_SESSION["mouf_fileupload_autorizeduploads"][$uniqueId]['params'] = serialize($this->params);
// 		return $html;
// 	}
	
// 	/**
// 	 * Save parameter. They must be serializable.
// 	 * 
// 	 * @param mixed $params
// 	 */
// 	public function setParams($params) {
// 		$this->params = $params;
// 	}
	
// 	/**
// 	 * Add parameter. They must be serializable.
// 	 * Caution, elements are saved in array
// 	 * 
// 	 * @param mixed $params
// 	 */
// 	public function addParams($params) {
// 		if($this->params === null)
// 			$this->params = $params;
// 		else
// 			$this->params = array_merge($this->params, $params);
// 	}

// 	/**
// 	 * Return the parameters saved in instance.
// 	 * @return mixed
// 	 */
// 	public function getParams($uniqueId) {
// 		// Start a session using the session manager.
// 		$moufManager = MoufManager::getMoufManager();
// 		$moufManager->getInstance('sessionManager')->start();
// 		if(isset($_SESSION["mouf_fileupload_autorizeduploads"][$uniqueId]['params']))
// 			return unserialize($_SESSION["mouf_fileupload_autorizeduploads"][$uniqueId]['params']);
// 		else
// 			return array();
// 	}
	
// 	/**
// 	 * Returns the complete absolute path to the file that will be uploaded.
// 	 * @return string
// 	 */
// 	public function getFileUploadPath() {
// 		$directory = $this->directory;
// 		if (strpos($directory, '/') !== 0 && strpos($directory, ':') !== 1) {
// 			$directory = ROOT_PATH.$directory;
// 		}
// 		rtrim($directory, DIRECTORY_SEPARATOR);
// 		$directory .= DIRECTORY_SEPARATOR;
// 		$file = $directory.basename($this->fileName);
// 		$file = str_replace(DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $file);
// 		return $file;
// 	}
	
// 	/**
// 	 * Call all listener before upload file.
// 	 *
// 	 * @param string $targetFile The final path of the uploaded file. When the afterUpload method is called, the file is there.
// 	 *  * @param string $fileName The final name of the uploaded file. When the beforeUpload method is called, the file is not yet there. In this function, you can change the value of $fileName since it is passed by reference
// 	 * @param string $fileId The fileId that was set in the uploadify widget (see FileUploadWidget::fileId)
// 	 * @param array $result The result array that will be returned to the page as a JSON object.
// 	 * @param string $uniqueId Unique id of file uploader form.
// 	 */
// 	public function triggerBeforeUpload(&$targetFile, &$fileName, $fileId, array &$returnArray, $uniqueId) {
// 		if (is_array($this->listenersBefore)) {
// 			foreach ($this->listenersBefore as $listener) {
// 				/* @var $listener UploadifyOnUploadInterface */
// 				$result = $listener->beforeUpload($targetFile, $fileName, $fileId, $this, $returnArray, $this->getParams($uniqueId));
// 				if($result === false) {
// 					return false;
// 				}
// 				if(is_array($result)) {
// 					$returnArray = array_merge($returnArray, $result);
// 				}
// 			}
// 		}
// 		return true;
// 	}
	
// 	/**
// 	 * Call all listener after upload file.
// 	 *
// 	 * @param string $targetFile The final path of the uploaded file. When the afterUpload method is called, the file is there.
// 	 * @param string $fileId The fileId that was set in the uploadify widget (see FileUploadWidget::fileId)
// 	 * @param array $result The result array that will be returned to the page as a JSON object.
// 	 * @param string $uniqueId Unique id of file uploader form.
// 	 */
// 	public function triggerAfterUpload(&$targetFile, $fileId, array &$returnArray, $uniqueId) {
// 		if (is_array($this->listenersAfter)) {
// 			foreach ($this->listenersAfter as $listener) {
// 				/* @var $listener UploadifyOnUploadInterface */
// 				$result = $listener->afterUpload($targetFile, $fileId, $this, $returnArray, $this->getParams($uniqueId));
// 				if($result === false) {
// 					$returnArray = array_merge($returnArray, $result);
// 					break;
// 				}
// 			}
// 		}
// 	}

	/**
	 * Returns an array of file objets that have been uploaded with this widget.
	 * 
	 * @param string $token You can pass optionnally the token for this widget. Otherwise, it is retrieved from the request, based on the "name" parameter.
     * @return File[]
	 */
	public function getFiles($token = null) {
		if (!$token) {
			$token = $this->getTokenFromRequest();
		}
		$this->token = $token;
		
		$moufManager = MoufManager::getMoufManager();
		$moufManager->getInstance('sessionManager')->start();
		
		$tmpDirectory = $_SESSION["mouf_jqueryfileupload_autorizeduploads"][$this->token];
		
		$files = array();
		if (is_dir($tmpDirectory)) {
			foreach (new \DirectoryIterator($tmpDirectory) as $fileInfo) {
				if ($fileInfo->isFile()) {
					// Let's check the extension of the files.
					if ($this->acceptFileTypes && !preg_match($this->acceptFileTypes, $fileInfo->getFilename())) {
						throw new JqueryFileUploadException("Sorry, invalid file type for file '".$fileInfo->getFilename()."'");
					}
					$files[] = new File($fileInfo->getFilename(), $fileInfo->getPath());
				}
			}
		}
		return $files;
	}
	
	public function beforeUpload($targetDir, $token){
		foreach ($this->uploadListeners as $listener){
			/* @var $listener JQueryFileUploadListenerInterface. */
			$listener->onBeforeUpload($targetDir, $token);
		}
	}

	public function afterUpload($targetDir, $token){
		foreach ($this->uploadListeners as $listener){
			/* @var $listener JQueryFileUploadListenerInterface. */
			$listener->onAfterUpload($targetDir, $token);
		}
	}
	
	/**
	 * 
	 * @return JqueryFileUploadWidget[]
	 */
	public function getDefaultFiles() {
		return $this->files;
	}
	
	/**
	 * Add a file that will be displayed in the list of files.
	 * 
	 * @param FileWidget $file
	 */
	public function addDefaultFile(FileWidget $file) {
		$this->files[] = $file;
	}
	
	/**
	 * The set of listeners that will be called before and after upload is performed
	 * @param JQueryFileUploadListenerInterface[] $listeners
	 */
	public function setJqueryFileUploadListeners($listeners){
		$this->uploadListeners = $listeners;
	}
}
?>
