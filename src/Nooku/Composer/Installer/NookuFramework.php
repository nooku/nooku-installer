<?php
/**
 * Nooku Installer plugin - https://github.com/nooku/nooku-installer
 *
 * @copyright    Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license      GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link         https://github.com/nooku/nooku-installer for the canonical source repository
 */

namespace Nooku\Composer\Installer;

use Composer\Package\PackageInterface;
use Composer\Repository\InstalledRepositoryInterface;

/**
 * Installer class to install nooku-framework into a Joomla site.
 *
 * @author  Steven Rombauts <https://github.com/stevenrombauts>
 * @package Nooku\Composer\Installer
 */
class NookuFramework extends JoomlaExtension
{
    /**
     * {@inheritDoc}
     */
    public function install(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        if (!$this->_isValidName($package->getPrettyName()))
        {
            throw new \InvalidArgumentException(
                'Invalid package name `'.$package->getPrettyName().'`. '.
                'The `nooku-framework` composer type can only install the `nooku/nooku-framework` package.'
            );
        }

        parent::install($repo, $package);

        $query = 'UPDATE #__extensions SET enabled = 1 WHERE  type = \'plugin\' AND element = \'koowa\' AND folder = \'system\'';
        \JFactory::getDBO()->setQuery($query)->query();

        $this->_loadKoowaPlugin();
    }

    /**
     * {@inheritDoc}
     */
    public function isInstalled(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        return $this->_application->hasExtension('pkg_koowa', 'package');
    }

    protected function _isValidName($packageName)
    {
        return in_array($packageName, array('nooku/nooku-framework', 'nooku/nooku-framework-joomla'));
    }
}
