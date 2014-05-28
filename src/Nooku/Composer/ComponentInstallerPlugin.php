<?php
/**
 * Nooku Composer plugin - https://github.com/nooku/nooku-composer
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		https://github.com/nooku/nooku-composer for the canonical source repository
 */

namespace Nooku\Composer;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;

/**
 * Composer installer plugin
 *
 * @author  Steven Rombauts <https://github.com/stevenrombauts>
 * @package Nooku\Composer
 */
class ComponentInstallerPlugin implements PluginInterface
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