<?php
namespace Mouf\Html\Widgets\Form;

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

        // Let's rewrite the MoufComponents.php file to save the component
        $moufManager->rewriteMouf();
    }
}
