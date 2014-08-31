<?php
/**
 * Nooku Installer plugin - https://github.com/nooku/nooku-composer
 *
 * @copyright    Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license      GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link         https://github.com/nooku/nooku-composer for the canonical source repository
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
    public function install(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        if ($package->getPrettyName() !== 'nooku/nooku-framework') {
            throw new \InvalidArgumentException('Only the `nooku/nooku-framework` package can be installed using the `nooku-framework` Composer type.');
        }

        parent::install($repo, $package);

        $query = 'UPDATE #__extensions SET enabled = 1 WHERE  type = \'plugin\' AND element = \'koowa\' AND folder = \'system\'';
        \JFactory::getDBO()->setQuery($query)->query();
    }

    /**
     * {@inheritDoc}
     */
    public function isInstalled(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        return $this->_application->hasExtension('pkg_koowa', 'package');
    }
}