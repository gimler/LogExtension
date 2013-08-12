<?php

namespace Behat\LogExtension;

use Symfony\Component\Config\FileLocator,
    Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition,
    Symfony\Component\DependencyInjection\ContainerBuilder,
    Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

use Behat\Behat\Extension\ExtensionInterface;

/*
 * This file is part of the Behat\LogExtension
 *
 * (c) Gordon Franke <info@nevalon.de>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * Log extension for Behat class.
 *
 * @author Gordon Franke <info@nevalon.de>
 */
class Extension implements ExtensionInterface
{
    /**
     * Loads a specific configuration.
     *
     * @param array            $config    Extension configuration hash (from behat.yml)
     * @param ContainerBuilder $container ContainerBuilder instance
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/services'));
        $loader->load('listener.xml');

        $container->setParameter('behat.log.parameters', $config);
    }

    /**
     * Setups configuration for current extension.
     *
     * @param ArrayNodeDefinition $builder
     */
    public function getConfig(ArrayNodeDefinition $builder)
    {
        $config = $this->loadEnvironmentConfiguration();

        $builder->
            children()->
                scalarNode('output_path')->
                    defaultValue(isset($config['output_path']) ? $config['output_path'] : realpath(__DIR__ . '/../../../../../..'))->
                end()->
                scalarNode('access_log')->
                    defaultValue(isset($config['access_log']) ? $config['access_log'] : '/var/log/apache/access.log')->
                end()->
            end()->
        end();
    }

    protected function loadEnvironmentConfiguration()
    {
        $config = array();
        if ($envConfig = getenv('BEHAT_LOG_EXTENSION_PARAMS')) {
            parse_str($envConfig, $config);
        }

        return $config;
    }

    public function getCompilerPasses()
    {
        return array();
    }
}
