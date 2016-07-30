<?php

namespace Rz\RedirectBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class RzRedirectExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $loader->load('admin.xml');
        $loader->load('orm.xml');
        $loader->load('redirect.xml');

        $this->configureManagerClass($config, $container);
        $this->configureClass($config, $container);
        $this->configureAdmin($config, $container);
        $this->configureSettings($config, $container);
    }


    /**
     * @param array                                                   $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @return void
     */
    public function configureClass($config, ContainerBuilder $container)
    {
        $container->setParameter('rz.redirect.admin.redirect.entity', $config['class']['redirect']);
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    public function configureManagerClass($config, ContainerBuilder $container)
    {
        $container->setParameter('rz.redirect.manager.redirect.entity',  $config['class']['redirect']);
        $container->setParameter('rz.redirect.manager.redirect.class',  $config['manager_class']['orm']['redirect']);
    }

    /**
     * @param array                                                   $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @return void
     */
    public function configureAdmin($config, ContainerBuilder $container)
    {
        $container->setParameter('rz.redirect.admin.redirect.class', $config['admin']['redirect']['class']);
        $container->setParameter('rz.redirect.admin.redirect.controller', $config['admin']['redirect']['controller']);
        $container->setParameter('rz.redirect.admin.redirect.translation_domain', $config['admin']['redirect']['translation']);
    }

    /**
     * @param array                                                   $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @return void
     */
    public function configureSettings($config, ContainerBuilder $container)
    {
        $types = [];
        foreach ($config['redirect']['types'] as $id=>$type) {
            $types[$id] = $type['name'];
        }
        $container->setParameter('rz.redirect.types', $types);
        $container->setParameter('rz.redirect.default_type', $config['redirect']['default_type']);
    }
}
