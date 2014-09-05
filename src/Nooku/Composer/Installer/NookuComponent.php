<?php
/**
 * Nooku Installer plugin - https://github.com/nooku/nooku-installer
 *
 * @copyright    Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license      GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link         https://github.com/nooku/nooku-installer for the canonical source repository
 */

namespace Nooku\Composer\Installer;

use Nooku\Composer\Joomla\Application as Application;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Package\PackageInterface;
use Composer\Repository\InstalledRepositoryInterface;
use Composer\Installer\LibraryInstaller;

/**
 * Installer class to install reusable Nooku components into a Nooku Framework installation.
 *
 * @author  Oli Griffiths <https://github.com/oligriffiths>
 * @package Nooku\Composer\Installer
 */
class NookuComponent extends LibraryInstaller
{
    /**
     * Installs specific package.
     *
     * @param  InstalledRepositoryInterface $repo    repository in which to check
     * @param  PackageInterface             $package package instance
     * @throws InvalidArgumentException
     */
    public function install(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        if (substr($package->getPrettyName(), -strlen('-component')) !== '-component') {
            throw new \InvalidArgumentException('The name of any package typed `nooku-component` must be formatted as `vendor/name-component`. Aborting.');
        }

        parent::install($repo, $package);

        $this->_installAutoloader($package);
    }

    /**
     * {@inheritDoc}
     */
    public function update(InstalledRepositoryInterface $repo, PackageInterface $initial, PackageInterface $target)
    {
        $this->_removeAutoloader($target);

        parent::update($repo, $initial, $target);

        $this->_installAutoloader($target);
    }

    /**
     * Installs the default autoloader if no autoloader is supplied.
     *
     * @param PackageInterface $package
     */
    protected function _installAutoloader(PackageInterface $package)
    {
        $path = $this->_getAutoloaderPath($package);

        if(!file_exists($path))
        {
            $name                     = substr($package->getPrettyName(), 0, -strlen('-component'));
            list($vendor, $component) = explode('/', $name);

            $contents = <<<EOL
<?php
/**
 * This file has been generated automatically by Composer. Any changes to this file will not persist.
 * You can override this autoloader by supplying an autoload.php file in the root of the relevant component.
 **/

KObjectManager::getInstance()
    ->getObject('lib:object.bootstrapper')
    ->registerComponent(
        '$component',
        dirname(__FILE__),
        '$vendor'
    );
EOL;

            file_put_contents($path, $contents);
        }
    }

    /**
     * Removes the autoloader for the given package if it was generated automatically.
     *
     * @param PackageInterface $package
     */
    protected function _removeAutoloader(PackageInterface $package)
    {
        $path = $this->_getAutoloaderPath($package);

        if(file_exists($path))
        {
            $contents = file_get_contents($path);

            if (strpos($contents, 'This file has been generated automatically by Composer.') !== false) {
                unlink($path);
            }
        }
    }

    /**
     * Build the path to the package's autoloader file.
     *
     * @param PackageInterface $package
     * @return string
     */
    protected function _getAutoloaderPath(PackageInterface $package)
    {
        $path = $this->getInstallPath($package);

        return rtrim($path, '/').'/autoload.php';
    }
}