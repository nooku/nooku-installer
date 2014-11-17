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
        $path     = $this->_getAutoloaderPath($package);
        $manifest = $this->_getKoowaManifest($this->getInstallPath($package));

        if (!($manifest instanceof \SimpleXMLElement))
        {
            throw new \InvalidArgumentException(
                'Failed to load `koowa-component.xml` manifest for package `'.$package->getPrettyName().'`.'
            );
        }

        if(!file_exists($path))
        {
            $classname      = $this->_getObjectManagerClassName();
            list($vendor, ) = explode('/', $package->getPrettyName());
            $component      = (string) $manifest->name;

            $contents = <<<EOL
<?php
/**
 * This file has been generated automatically by Composer. Any changes to this file will not persist.
 * You can override this autoloader by supplying an autoload.php file in the root of the relevant component.
 **/

$classname::getInstance()
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
     * Attempts to locate and initialize the koowa-component.xml manifest
     *
     * @param PackageInterface $package
     * @return bool|\SimpleXMLElement   Instance of SimpleXMLElement or false on failure
     */
    protected function _getKoowaManifest($path)
    {
        $directory = new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::KEY_AS_PATHNAME);
        $iterator  = new \RecursiveIteratorIterator($directory);
        $regex     = new \RegexIterator($iterator, '/koowa-component\.xml/', \RegexIterator::GET_MATCH);
        $files     = iterator_to_array($regex);

        if (empty($files)) {
            return false;
        }

        $manifests = array_keys($files);
        $manifest  = simplexml_load_file($manifests[0]);

        return $manifest;
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

    /**
     * Determine the correct object manager class name to be used in the
     * autoloader. When installing into Nooku Platform, use Nooku\Library\ObjectManager,
     * otherwise assume we are installing alongside Framework and use KObjectManager.
     *
     * @return string
     */
    protected function _getObjectManagerClassName()
    {
        $files    = array('./library/nooku.php', './component');
        $platform = true;

        foreach ($files as $file)
        {
            if (!file_exists($file))
            {
                $platform = false;
                break;
            }
        }

        return $platform ? 'Nooku\Library\ObjectManager' : 'KObjectManager';
    }
}