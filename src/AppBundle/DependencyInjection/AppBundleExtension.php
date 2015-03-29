<?php
namespace AppBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Config\FileLocator;

class AppBundleExtension extends Extension
{
  public function load(array $configs, ContainerBuilder $container) {
    // ...
//     $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
//     $loader->load('admin.yml');
  }
}