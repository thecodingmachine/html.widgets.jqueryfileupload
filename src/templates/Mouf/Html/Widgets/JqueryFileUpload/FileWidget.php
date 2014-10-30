<?php
use Mouf\Utils\Graphics\MoufImage\Filters\MoufImageFromFile;
/* @var $object Mouf\Html\Widgets\JqueryFileUpload\FileWidget */
?>
<span class="badge file-widget" data-id="<?php echo htmlentities($id); ?>">
<?php  
$extension = strtolower($object->getFileInfo()->getExtension());
if ($extension == "png" || $extension == "gif" || $extension == "jpg") {
	$origin = new MoufImageFromFile();
	$origin->path = $object->getFileInfo()->getRealPath();
	
	$resizeService = Mouf::getResizeUploadThumbnails();
	$resizeService->source = $origin;
	$moufImageResource = $resizeService->getResource();
	
	$finalImage = $moufImageResource->resource;
	$image_info = $moufImageResource->originInfo;
	$image_type = $image_info[2];
	
	ob_start();
	//create the image
	if( $image_type == IMAGETYPE_JPEG ) {
		imagejpeg($finalImage);
	} elseif( $image_type == IMAGETYPE_GIF ) {
		imagegif($finalImage);
	} elseif( $image_type == IMAGETYPE_PNG ) {
		imagepng($finalImage);
	}
	$imageAsString = ob_get_clean();
	
	echo '<img style="vertical-align:top;" alt="'.htmlentities($object->getFileInfo()->getFilename()).'" src="data:image/'.$extension.';base64,';
	echo base64_encode($imageAsString); 
	echo '" />';
} else {
	echo htmlentities($object->getFileInfo()->getFilename());
}
?>
 <i class="icon-remove file-delete-button" data-id="<?php echo htmlentities($id); ?>"></i>
</span>
<span>&nbsp;</span>