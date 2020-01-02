<?php

namespace TechDivision\Import\Cli\Simple;

use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;

class AppKernel extends Kernel
{
    public function registerBundles()
    {

        $dotenv = new Dotenv();
        $dotenv->loadEnv(__DIR__.'/.env');

        $bundles = array(
            new FrameworkBundle(),
            new ImportBundle()
        );

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/Resources/config/config_'.$this->getEnvironment().'.yml');
    }
}