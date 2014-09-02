<?php
/**
 * Nooku Installer plugin - https://github.com/nooku/nooku-installer
 *
 * @copyright    Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license      GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link         https://github.com/nooku/nooku-installer for the canonical source repository
 */

namespace Nooku\Composer;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Package\PackageInterface;
use Composer\Repository\InstalledRepositoryInterface;
use Composer\Installer\LibraryInstaller;

/**
 * Composer installer class
 *
 * @author  Steven Rombauts <https://github.com/stevenrombauts>
 * @package Nooku\Composer
 */
class Installer extends LibraryInstaller
{
    protected $_config      = null;
    protected $_instances   = array();
    protected $_delegates   = array(
        'nooku-framework'   =>  'Nooku\Composer\Installer\NookuFramework',
        'nooku-component'   =>  'Nooku\Composer\Installer\NookuComponent',
        'joomla-extension'  =>  'Nooku\Composer\Installer\JoomlaExtension'
    );

    /**
     * {@inheritDoc}
     */
    public function __construct(IOInterface $io, Composer $composer, $type = 'library')
    {
        parent::__construct($io, $composer, $type);

        $this->_config = $composer->getConfig();
    }

    /**
     * Returns a specialized LibraryInstaller subclass to deal with the given package type.
     *
     * @return Composer\Installer\LibraryInstaller
     * @throws \InvalidArgumentException
     */
    public function getDelegate($packageType)
    {
        if (!isset($this->_instances[$packageType]))
        {
            if (isset($this->_delegates[$packageType]))
            {
                $classname = $this->_delegates[$packageType];
                $instance  = new $classname($this->io, $this->composer, 'nooku-framework');

                $this->_instances[$packageType] = $instance;
            }
            else throw new \InvalidArgumentException('Unknown package type `'.$packageType.'`.');
        }

        return $this->_instances[$packageType];
    }

    /**
     * {@inheritDoc}
     */
    public function getInstallPath(PackageInterface $package)
    {
        return $this->getDelegate($package->getType())->getInstallPath($package);
    }

    /**
     * {@inheritDoc}
     */
    public function install(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        $this->getDelegate($package->getType())->install($repo, $package);
    }

    /**
     * {@inheritDoc}
     */
    public function update(InstalledRepositoryInterface $repo, PackageInterface $initial, PackageInterface $target)
    {
        $this->getDelegate($initial->getType())->update($repo, $initial, $target);
    }

    /**
     * {@inheritDoc}
     */
    public function supports($packageType)
    {
        return in_array($packageType, array_keys($this->_delegates));
    }

    /**
     * {@inheritDoc}
     */
    public function isInstalled(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        return $this->getDelegate($package->getType())->isInstalled($repo, $package);
    }
}