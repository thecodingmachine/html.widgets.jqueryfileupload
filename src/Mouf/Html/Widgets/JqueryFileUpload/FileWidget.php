<?php
namespace Mouf\Html\Widgets\JqueryFileUpload;

use Mouf\Html\Renderer\Renderable;
use Mouf\Html\HtmlElement\HtmlElementInterface;

/**
 * A simple widget representing a file 
 *
 * @author David Négrier
 */
class FileWidget implements HtmlElementInterface {
	use Renderable;
	
	protected $id;
	
	protected $fileName;
	/**
	 * 
	 * @var \SplFileInfo
	 */
	protected $fileInfo;
	
	/**
	 * 
	 * @param string $fileName The filename (full path to file)
	 */
	public function __construct($fileName, $id = null) {
		$this->id = $id;
		$this->fileName = $fileName;
		$this->fileInfo = new \SplFileInfo($fileName);
		
		if (!$this->fileInfo->isFile()) {
			throw new \Exception("The file '".$this->fileName."' does not exists.");
		}
	}

	public function getFileInfo() {
		return $this->fileInfo;
	}
}
