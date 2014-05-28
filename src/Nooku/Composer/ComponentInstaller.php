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
use Composer\Package\PackageInterface;
use Composer\Repository\InstalledRepositoryInterface;
use Composer\Installer\LibraryInstaller;

use Nooku\Library;

/**
 * Composer installer class
 *
 * @author  Steven Rombauts <https://github.com/stevenrombauts>
 * @package Nooku\Composer
 */
class ComponentInstaller extends LibraryInstaller
{
    protected $_ignored_paths = array('composer.json', 'install.sql');

    /**
     * {@inheritDoc}
     */
    public function __construct(IOInterface $io, Composer $composer, $type = 'library', Filesystem $filesystem = null)
    {
        parent::__construct($io, $composer, $type, $filesystem);

        // @TODO There should not be a need to change the error reporting level.
        // There are two issues at the moment that require us to lower the error reporting level :
        // Firstly, because of the legacy Joomla libraries, a lot of strict errors are being thrown, breaking execution.
        // Secondly, because of the reliance on HttpUrl throughout the framework, this throws a lot of warnings since HttpUrl cannot deal with file:/// URLs.
        error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_STRICT);

        $this->_bootstrap();
    }

    /**
     * {@inheritDoc}
     */
    public function install(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        parent::install($repo, $package);

        $this->io->write('    <fg=cyan>Installing</fg=cyan> into Nooku'.PHP_EOL);

        $source    = $this->getInstallPath($package);
        $extension = substr($package->getPrettyName(), strlen('nooku/'));

        if (is_dir($source))
        {
            // Copy the source files
            $target = getcwd().'/component/'.$extension.'/';

            if (file_exists($target)) {
                throw new \InvalidArgumentException('Target component already exists: '.$extension);
            }

            mkdir($target, 0777, true);

            $this->_copyDirectory($source, $target);

            // Import SQL if install.sql
            $this->_importMySQL($source.'/install.sql');

            // Register the component in the `extensions` table
            $data = array(
                    'title'   => ucfirst($extension),
                    'name'    => $extension,
                    'enabled' => 1
            );

            $row = Library\ObjectManager::getInstance()->getObject('com:extensions.model.extensions')->getRow();
            $row->setData($data)->save();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function supports($packageType)
    {
        return $packageType === 'nooku-installer';
    }

    /**
     * {@inheritDoc}
     */
    public function isInstalled(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        $extension = substr($package->getPrettyName(), strlen('nooku/'));

        $row = Library\ObjectManager::getInstance()->getObject('com:application.database.rowset.extensions')->getExtension($extension);
        if (!$row->isNew()) {
            return true;
        }

        $directory = getcwd().'/component/'.$extension.'/';
        if (!file_exists($directory) || !is_dir($directory)) {
            return false;
        }

        return parent::isInstalled($repo, $package);
    }

    /**
     * Bootstraps the Nooku library
     *
     * @throws \InvalidArgumentException
     */
    protected function _bootstrap()
    {
        if (defined('JPATH_ROOT')) {
            return;
        }

        define('JPATH_ROOT'         , getcwd());
        define('JPATH_APPLICATION'  , JPATH_ROOT.'/application/admin');
        define('JPATH_VENDOR'       , JPATH_ROOT.'/vendor');
        define('JPATH_SITES'        , JPATH_ROOT.'/sites');

        define('JPATH_BASE'         , JPATH_APPLICATION);

        define('DS', DIRECTORY_SEPARATOR);

        if (!file_exists(JPATH_ROOT . '/config/config.php') || (filesize(JPATH_ROOT . '/config/config.php') < 10)) {
            throw new \InvalidArgumentException('No configuration file found.');
        }

        require_once(JPATH_VENDOR . '/joomla/import.php');
        jimport('joomla.environment.uri');
        jimport('joomla.html.html');
        jimport('joomla.html.parameter');
        jimport('joomla.utilities.utility');
        jimport('joomla.language.language');

        require_once JPATH_ROOT.'/config/config.php';
        $config = new \JConfig();

        require_once(JPATH_ROOT.'/library/nooku.php');

        \Nooku::getInstance(array(
            'cache_prefix' => md5($config->secret) . '-cache-koowa',
            'cache_enabled' => $config->caching
        ));

        unset($config);

        Library\ClassLoader::getInstance()->getLocator('com')->registerNamespaces(
            array(
                '\\'              => JPATH_APPLICATION.'/component',
                'Nooku\Component' => JPATH_ROOT.'/component'
            )
        );

        Library\ClassLoader::getInstance()->addApplication('admin', JPATH_ROOT.'/application/admin');

        Library\ObjectManager::getInstance()->getObject('lib:bootstrapper.application', array(
            'directory' => JPATH_APPLICATION.'/component'
        ))->bootstrap();
    }

    /**
     * Imports a given MySQL dump.
     *
     * @param $file - the MySQL dump file
     * @throws \InvalidArgumentException
     */
    protected function _importMySQL($file)
    {
        if (!file_exists($file)) {
            return;
        }

        if (!is_readable($file) || !is_file($file)) {
            throw new \InvalidArgumentException('Unable to read MySQL import file: '.basename($file));
        }

        $adapter = Library\ObjectManager::getInstance()->getObject('lib:database.adapter.mysql');

        $fp = fopen($file, 'r');

        $query = array();
        while (feof($fp) === false)
        {
            $query[] = fgets($fp);

            if (preg_match('~' . preg_quote(';', '~') . '\s*$~iS', end($query)) === 1)
            {
                $query = trim(implode('', $query));

                $adapter->execute($query);

                $query = array();
            }
        }

        fclose($fp);
    }

    /**
     * Copy directory $source to $target
     *
     * @param $source
     * @param $target
     * @throws \InvalidArgumentException
     */
    protected function _copyDirectory($source, $target)
    {
        if (!is_dir($source)) {
            throw new \InvalidArgumentException('Source directory does not exist: '.$source);
        }

        $directory = new \RecursiveDirectoryIterator($source, \FilesystemIterator::SKIP_DOTS);

        $filter = new \RecursiveCallbackFilterIterator($directory, function ($current, $key, $iterator) {
            if ($current->getFilename()[0] === '.') {
                return false;
            }

            return !in_array($current->getFilename(), $this->_ignored_paths);
        });

        $iterator  = new \RecursiveIteratorIterator($filter, \RecursiveIteratorIterator::SELF_FIRST);

        foreach ($iterator as $file)
        {
            $path    = $file->__toString();
            $newPath = str_replace($source, $target, $path);

            if ($file->isFile()) {
                copy($path, $newPath);
            }
            else if($file->isDir()) {
                mkdir($newPath, 0777, true);
            }
        }
    }
}