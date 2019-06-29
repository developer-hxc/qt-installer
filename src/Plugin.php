<?php

namespace Hxc\Composer;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;

class Plugin implements PluginInterface
{
    public function activate(Composer $composer, IOInterface $io)
    {
        $manager = $composer->getInstallationManager();
        //扩展
        $manager->addInstaller(new QtExtend($io, $composer));
    }
}