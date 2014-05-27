<?php
namespace Nooku\Composer;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Package\PackageInterface;
use Composer\Repository\InstalledRepositoryInterface;
use Composer\Installer\LibraryInstaller;

class ComponentInstaller extends LibraryInstaller
{
    protected $_ignored_paths = array('composer.json', 'install.sql');

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
            $target = getcwd().'/component/'.$extension.'/';

            if (file_exists($target)) {
                throw new \InvalidArgumentException('Target component already exists: '.$extension);
            }

            mkdir($target, 0777, true);

            $this->_copyDirectory($source, $target);
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
        $directory = getcwd().'/component/'.$extension.'/';

        if (!file_exists($directory) || !is_dir($directory)) {
            return false;
        }

        return parent::isInstalled($repo, $package);
    }

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