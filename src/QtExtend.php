<?php

namespace Hxc\Composer;

use Composer\Installer\LibraryInstaller;
use Composer\Package\PackageInterface;
use Composer\Repository\InstalledRepositoryInterface;

class QtExtend extends LibraryInstaller
{
    public function install(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        parent::install($repo, $package);
        $this->copyExtraFiles($package);
    }

    protected function copyExtraFiles(PackageInterface $package)
    {
        if ($this->composer->getPackage()->getType() == 'project') {
            $extra = $package->getExtra();
            if (!empty($extra['qt-extend'])) {
                $composerExtra = $this->composer->getPackage()->getExtra();
                $appDir = !empty($composerExtra['app-path']) ? $composerExtra['app-path'] : 'application';
                if (is_dir($appDir)) {
                    $extraDir = $appDir . DIRECTORY_SEPARATOR . 'extra';
                    $this->filesystem->ensureDirectoryExists($extraDir);
                    $appControllerDir = $appDir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'controller';
                    $this->filesystem->ensureDirectoryExists($appControllerDir);
                    //配置文件
                    foreach ((array)$extra['qt-extend'] as $config) {
                        if (!isset($config['type'])) {
                            continue;
                        }
                        if ($config['type'] == 'config') {
                            $target = $extraDir . DIRECTORY_SEPARATOR . $config['to'] . '.php';
                        } elseif ($config['type'] == 'app-controller') {
                            $target = $appControllerDir . DIRECTORY_SEPARATOR . $config['to'] . '.php';
                        }

                        $source = $this->getInstallPath($package) . DIRECTORY_SEPARATOR . $config['from'];
                        if (is_file($target)) {
                            $this->io->write("<info>File {$target} exist!</info>");
                            continue;
                        }
                        if (!is_file($source)) {
                            $this->io->write("<info>File {$target} not exist!</info>");
                            continue;
                        }
                        copy($source, $target);
                    }
                }
            }
        }
    }

    public function update(InstalledRepositoryInterface $repo, PackageInterface $initial, PackageInterface $target)
    {
        parent::update($repo, $initial, $target);
        $this->copyExtraFiles($target);
    }

    public function supports($packageType)
    {
        return 'qt-extend' === $packageType;
    }
}