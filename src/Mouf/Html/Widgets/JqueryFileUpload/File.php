<?php
namespace Mouf\Html\Widgets\JqueryFileUpload;


use Mouf\MoufManager;
use Mouf\Html\HtmlElement\HtmlElementInterface;
use Mouf\Utils\Value\ValueUtils;
use Mouf\Utils\Value\ValueInterface;
use Mouf\Html\Renderer\Renderable;
use Symfony\Component\Filesystem\Filesystem;

/**
 * This class represent a file that was just uploaded.
 * It offers methods to rename/move/delete the file. 
 *
 * @author David Négrier
 */
class File {
	protected $fileName;
	protected $directory;
	
	/**
	 * 
	 * @param string $fileName The filename
	 * @param string $directory The directory containing the file
	 */
	public function __construct($fileName, $directory) {
		$this->fileName = $fileName;
		
		if (strrpos($directory, '/') == strlen($directory) - 1) {
			$directory = substr($directory, 0, strlen($directory) - 1);
		}
		
		$this->directory = $directory;
		
		if (!$this->fileExists()) {
			return new \Exception("The file '".$this->directory.'/'.$this->fileName."' does not exists.");
		}
	}
	
	/**
	 * Returns whether the pointed file exists or not.
	 * 
	 * @return boolean
	 */
	public function fileExists() {
		return file_exists($this->directory.'/'.$this->fileName);
	}
	
	/**
	 * Moves the file to the target directory.
	 * 
	 * @param string $targetDir
	 */
	public function move($targetDir) {
		$fs = new Filesystem();
		if (!$fs->exists($targetDir)) {
			$fs->mkdir($targetDir, 0775);
			chmod($targetDir, 0775);
		}
		
		$fs->rename($this->directory.'/'.$this->fileName, $targetDir.'/'.$this->fileName);
		$fs->chmod($targetDir.'/'.$this->fileName, 0664);
		$this->directory = $targetDir;
	}
	
	/**
	 * Renames the file (without changing the directory)
	 * 
	 * @param string $newFileName
	 */
	public function rename($newFileName) {
		$fs = new Filesystem();
		
		$fs->rename($this->directory.'/'.$this->fileName, $this->directory.'/'.$newFileName);
		$fs->chmod($this->directory.'/'.$newFileName, 0664);
		$this->fileName = $newFileName;
	}
	
	/**
	 * Moves the file to the target directory.
	 *
	 * @param string $targetName
	 * @param string $newFileName
	 */
	public function moveAndRename($targetDir, $newFileName) {
		$fs = new Filesystem();
		if (!$fs->exists($targetDir)) {
			$fs->mkdir($targetDir, 0775);
			chmod($targetDir, 0775);
		}
		
		$fs->rename($this->directory.'/'.$this->fileName, $targetDir.'/'.$newFileName);
		$fs->chmod($targetDir.'/'.$newFileName, 0664);
		$this->directory = $targetDir;
		$this->fileName = $newFileName;
	}
	
	/**
	 * Returns the full path to the file
	 * 
	 * @return string
	 */
	public function getFullPath() {
		return $this->directory.'/'.$this->fileName;
	}
	
	/**
	 * Deletes the file
	 */
	public function delete() {
		unlink($this->getFullPath());
	}
	
	/**
	 * Returns the name of the file
	 * @return string
	 */
	public function getFileName() {
		return $this->fileName;
	}
	
	/**
	 * Returns the directory containing that file
	 * @return string
	 */
	public function getDirectory() {
		return $this->directory;
	}
	
	
	
}
