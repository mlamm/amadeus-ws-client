<?php
namespace Flight\Service\Amadeus\Application;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class ApplicationConfiguration
 * @package Flight\Service\Amadeus\Application
 */
class ApplicationConfiguration implements ConfigurationInterface
{
    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        $rootNode = $treeBuilder->root('application');
        $rootNode
            ->children()
                ->arrayNode('service')
                ->children()
                    ->arrayNode('search')
                    ->children()
                        ->scalarNode('wsdl_name')->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
