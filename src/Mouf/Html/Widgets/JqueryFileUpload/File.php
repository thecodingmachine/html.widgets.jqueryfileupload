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
	 * @param string $mode One of RenameEnum constants (MOVE, MOVE_AND_OVERWRITE, MOVE_AND_RENAME)
	 */
	public function move($targetDir, $mode = RenameEnum::MOVE) {
		$fs = new Filesystem();
		if (!$fs->exists($targetDir)) {
			$fs->mkdir($targetDir, 0775);
			chmod($targetDir, 0775);
		}

		if ($mode === RenameEnum::MOVE_AND_RENAME) {
			$targetFileName = $this->getTargetName($targetDir, $this->fileName);
		} else {
			$targetFileName = $this->fileName;
		}

		$fs->rename($this->directory.'/'.$this->fileName, $targetDir.'/'.$targetFileName, $mode === RenameEnum::MOVE_AND_OVERWRITE);
		$fs->chmod($targetDir.'/'.$targetFileName, 0664);
		$this->directory = $targetDir;
	}
	
	/**
	 * Renames the file (without changing the directory)
	 * 
	 * @param string $newFileName
	 * @param string $mode One of RenameEnum constants (MOVE, MOVE_AND_OVERWRITE, MOVE_AND_RENAME)
	 */
	public function rename($newFileName, $mode = RenameEnum::MOVE) {
		$fs = new Filesystem();

		if ($mode === RenameEnum::MOVE_AND_RENAME) {
			$targetFileName = $this->getTargetName($this->directory, $newFileName);
		} else {
			$targetFileName = $newFileName;
		}

		$fs->rename($this->directory.'/'.$this->fileName, $this->directory.'/'.$targetFileName, $mode === RenameEnum::MOVE_AND_OVERWRITE);
		$fs->chmod($this->directory.'/'.$targetFileName, 0664);
		$this->fileName = $newFileName;
	}
	
	/**
	 * Moves the file to the target directory.
	 *
	 * @param string $targetDir
	 * @param string $newFileName
	 * @param string $mode One of RenameEnum constants (MOVE, MOVE_AND_OVERWRITE, MOVE_AND_RENAME)
	 */
	public function moveAndRename($targetDir, $newFileName, $mode = RenameEnum::MOVE) {
		$fs = new Filesystem();
		if (!$fs->exists($targetDir)) {
			$fs->mkdir($targetDir, 0775);
			chmod($targetDir, 0775);
		}

		if ($mode === RenameEnum::MOVE_AND_RENAME) {
			$targetFileName = $this->getTargetName($targetDir, $newFileName);
		} else {
			$targetFileName = $newFileName;
		}

		$fs->rename($this->directory.'/'.$this->fileName, $targetDir.'/'.$targetFileName, $mode === RenameEnum::MOVE_AND_OVERWRITE);
		$fs->chmod($targetDir.'/'.$targetFileName, 0664);
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

	/**
	 * Finds a valid file name for $fileName in directory $destPath
	 * If the file already exists in $destPath, a new file name is proposed.
	 *
	 * @param string $destPath Destination directory
	 * @param string $fileName
	 * @return string
	 */
	private function getTargetName($destPath, $fileName) {
		$fs = new Filesystem();
		$counter = 1;

		$pathInfo = pathinfo($fileName);
		$baseFileName = $pathInfo['filename'];
		$extension = $pathInfo['extension'];

		while (true) {

			if ($counter == 1) {
				$newName = $fileName;
			} else {
				$newName = $baseFileName."_".$counter.'.'.$extension;
			}

			if (!$fs->exists($destPath.'/'.$newName)) {
				break;
			}
			$counter++;
		}

		return $newName;
	}
	
}
