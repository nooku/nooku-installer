<?php
/**
 * Nooku Installer plugin - https://github.com/nooku/nooku-composer
 *
 * @copyright    Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license      GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link         https://github.com/nooku/nooku-composer for the canonical source repository
 */

namespace Nooku\Composer;

use Nooku\Composer\Installer\Framework;

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
    protected $_delegates   = array();
    protected $_config      = null;

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
        if (!isset($this->_delegates[$packageType]))
        {
            switch($packageType)
            {
                case 'nooku-framework':
                    $this->_delegates[$packageType] = new Framework($this->io, $this->composer, 'nooku-framework');
                    break;
                default:
                    throw new \InvalidArgumentException('Unknown package type `'.$packageType.'`.');
                    break;
            }
        }

        return $this->_delegates[$packageType];
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
        return $packageType === 'nooku-framework';
    }

    /**
     * {@inheritDoc}
     */
    public function isInstalled(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        return $this->getDelegate($package->getType())->isInstalled($repo, $package);
    }
}