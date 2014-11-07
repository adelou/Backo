<?php

namespace App\MediaBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('app_media');

        $rootNode
            ->children()
                ->scalarNode('upload_dir')
                    ->defaultValue('/uploads/medias') // or whatever default value
                ->end()
                ->scalarNode('media_dir')
                    ->defaultValue('/uploads/medias') // or whatever default value
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
