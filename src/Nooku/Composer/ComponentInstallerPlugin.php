<?php
namespace Nooku\Composer;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;

class ExtensionInstallerPlugin implements PluginInterface
{
    /**
     * Apply plugin modifications to composer
     *
     * @param Composer $composer
     * @param IOInterface $io
     */
    public function activate(Composer $composer, IOInterface $io)
    {
        $installer = new ComponentInstaller($io, $composer);

        $composer->getInstallationManager()->addInstaller($installer);
    }
}