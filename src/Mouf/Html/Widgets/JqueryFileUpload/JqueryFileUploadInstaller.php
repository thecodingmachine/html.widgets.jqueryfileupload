<?php
namespace Mouf\Html\Widgets\JqueryFileUpload;

use Mouf\Installer\PackageInstallerInterface;
use Mouf\MoufManager;
use Mouf\Html\Renderer\RendererUtils;

/**
 * The installer for this package.
 */
class JqueryFileUploadInstaller implements PackageInstallerInterface {

    /**
     * (non-PHPdoc)
     * @see \Mouf\Installer\PackageInstallerInterface::install()
     */
    public static function install(MoufManager $moufManager) {
		// Let's create the renderer
		RendererUtils::createPackageRenderer($moufManager, "mouf/html.widgets.jqueryfileupload");
		
		// Let's create the instance.
		$resizeUploadThumbnails = InstallUtils::getOrCreateInstance('resizeUploadThumbnails', 'Mouf\\Utils\\Graphics\\MoufImage\\Filters\\MoufImageResize', $moufManager);
		
		if (!$resizeUploadThumbnails->getPublicFieldProperty('height')->isValueSet()) {
			$resizeUploadThumbnails->getPublicFieldProperty('height')->setValue('100');
		}
		if (!$resizeUploadThumbnails->getPublicFieldProperty('width')->isValueSet()) {
			$resizeUploadThumbnails->getPublicFieldProperty('width')->setValue('100');
		}
		if (!$resizeUploadThumbnails->getPublicFieldProperty('keepRatio')->isValueSet()) {
			$resizeUploadThumbnails->getPublicFieldProperty('keepRatio')->setValue(true);
		}
		if (!$resizeUploadThumbnails->getPublicFieldProperty('allowEnlarge')->isValueSet()) {
			$resizeUploadThumbnails->getPublicFieldProperty('allowEnlarge')->setValue(true);
		}
		if (!$resizeUploadThumbnails->getPublicFieldProperty('enforceRequestedDimensions')->isValueSet()) {
			$resizeUploadThumbnails->getPublicFieldProperty('enforceRequestedDimensions')->setValue(true);
		}
		if (!$resizeUploadThumbnails->getPublicFieldProperty('backgroundRed')->isValueSet()) {
			$resizeUploadThumbnails->getPublicFieldProperty('backgroundRed')->setValue('255');
		}
		if (!$resizeUploadThumbnails->getPublicFieldProperty('backgroundGreen')->isValueSet()) {
			$resizeUploadThumbnails->getPublicFieldProperty('backgroundGreen')->setValue('255');
		}
		if (!$resizeUploadThumbnails->getPublicFieldProperty('backgroundBlue')->isValueSet()) {
			$resizeUploadThumbnails->getPublicFieldProperty('backgroundBlue')->setValue('255');
		}
		if (!$resizeUploadThumbnails->getPublicFieldProperty('backgroundAlpha')->isValueSet()) {
			$resizeUploadThumbnails->getPublicFieldProperty('backgroundAlpha')->setValue('0');
		}
		
        // Let's rewrite the MoufComponents.php file to save the component
        $moufManager->rewriteMouf();
    }
}
