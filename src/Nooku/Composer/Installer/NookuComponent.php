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
        $this->_copyAssets($package);
    }

    /**
     * {@inheritDoc}
     */
    public function update(InstalledRepositoryInterface $repo, PackageInterface $initial, PackageInterface $target)
    {
        $this->_removeAutoloader($target);

        parent::update($repo, $initial, $target);

        $this->_installAutoloader($target);
        $this->_copyAssets($target);
    }
    /**
     * Installs the default autoloader if no autoloader is supplied.
     *
     * @param PackageInterface $package
     */
    protected function _installAutoloader(PackageInterface $package)
    {
        $path     = $this->_getAutoloaderPath($package);
        $manifest = $this->_getKoowaManifest($package);

        if(!file_exists($path))
        {
            $platform       = $this->_isPlatform();
            $classname      = $platform ? 'Nooku\Library\ObjectManager' : 'KObjectManager';
            $bootstrap      = $platform ? '' : 'KoowaAutoloader::bootstrap();';

            list($vendor, ) = explode('/', $package->getPrettyName());
            $component      = (string) $manifest->name;

            $contents = <<<EOL
<?php
/**
 * This file has been generated automatically by Composer. Any changes to this file will not persist.
 * You can override this autoloader by supplying an autoload.php file in the root of the relevant component.
 **/

$bootstrap

$classname::getInstance()
    ->getObject('lib:object.bootstrapper')
    ->registerComponent(
        '$component',
        __DIR__,
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
     * @throws `InvalidArgumentException` on failure to load the XML manifest
     */
    protected function _getKoowaManifest(PackageInterface $package)
    {
        $path      = $this->getInstallPath($package);
        $directory = new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::KEY_AS_PATHNAME);
        $iterator  = new \RecursiveIteratorIterator($directory);
        $regex     = new \RegexIterator($iterator, '/koowa-component\.xml/', \RegexIterator::GET_MATCH);
        $files     = iterator_to_array($regex);

        if (empty($files)) {
            return false;
        }

        $manifests = array_keys($files);
        $manifest  = simplexml_load_file($manifests[0]);

        if (!($manifest instanceof \SimpleXMLElement))
        {
            throw new \InvalidArgumentException(
                'Failed to load `koowa-component.xml` manifest for package `'.$package->getPrettyName().'`.'
            );
        }

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
     * Determine the environment we are working in: either Nooku Platform
     * or Nooku Framework. We need this to determine the correct
     * object manager class name to be used in the autoloader and other things.
     *
     * @return string
     */
    protected function _isPlatform()
    {
        $files    = array('./library/nooku.php', './component');

        foreach ($files as $file)
        {
            if (!file_exists($file)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Copy assets into the media folder if the installation is running in a Joomla context
     *
     * @param PackageInterface $package
     */
    protected function _copyAssets(PackageInterface $package)
    {
        $path       = rtrim($this->getInstallPath($package), '/');
        $asset_path = $path.'/resources/assets';
        $vendor_dir = dirname(dirname($path));

        // Check for libraries/joomla. vendor directory sits in libraries/ folder in Joomla 3.4+
        $is_joomla = is_dir(dirname($vendor_dir).'/joomla') || is_dir(dirname($vendor_dir).'/libraries/joomla');

        if ($is_joomla && is_dir($asset_path))
        {
            $manifest    = $this->_getKoowaManifest($package);

            $root        = is_dir(dirname($vendor_dir).'/joomla') ? dirname(dirname($vendor_dir)) : dirname($vendor_dir);
            $destination = $root.'/media/koowa/com_'.$manifest->name;

            $this->_copyDirectory($asset_path, $destination);
        }
    }

    /**
     * Copy source folder into target. Clears the target folder first.
     *
     * @param $source
     * @param $target
     * @return bool
     */
    protected function _copyDirectory($source, $target)
    {
        $result = false;

        if (!is_dir($target)) {
            $result = mkdir($target, 0755, true);
        }
        else
        {
            // Clear directory
            $iter = new \RecursiveDirectoryIterator($target);
            foreach (new \RecursiveIteratorIterator($iter, \RecursiveIteratorIterator::CHILD_FIRST) as $f)
            {
                if ($f->isDir())
                {
                    if (!in_array($f->getFilename(), array('.', '..'))) {
                        rmdir($f->getPathname());
                    }
                } else {
                    unlink($f->getPathname());
                }
            }
        }

        if (is_dir($target))
        {
            $result = true; // needed for empty directories
            $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($source), \RecursiveIteratorIterator::SELF_FIRST);
            foreach ($iterator as $f)
            {
                if ($f->isDir()) {
                    $path = $target.'/'.$iterator->getSubPathName();
                    if (!is_dir($path)) {
                        $result = mkdir($path);
                    }
                } else {
                    $result = copy($f, $target.'/'.$iterator->getSubPathName());
                }

                if ($result === false) {
                    break;
                }
            }
        }

        return $result;
    }
}