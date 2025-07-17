<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\Bundle\DesignEngine\DependencyInjection;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\ParserInterface;
use Ibexa\Bundle\Core\DependencyInjection\Configuration\SiteAccessAware\ContextualizerInterface;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;

class DesignConfigParser implements ParserInterface
{
    /**
     * @param array<string, mixed> $scopeSettings
     * @param string $currentScope
     */
    public function mapConfig(array &$scopeSettings, $currentScope, ContextualizerInterface $contextualizer): void
    {
        if (isset($scopeSettings['design'])) {
            $contextualizer->setContextualParameter('design', $currentScope, $scopeSettings['design']);
        }
    }

    public function preMap(array $config, ContextualizerInterface $contextualizer): void
    {
        // Nothing to map
    }

    public function postMap(array $config, ContextualizerInterface $contextualizer): void
    {
        // Nothing to map
    }

    public function addSemanticConfig(NodeBuilder $nodeBuilder): void
    {
        $nodeBuilder
            ->scalarNode('design')
                ->cannotBeEmpty()
                ->info('Name of the design to use. Must be declared in "ibexa.design.list"')
            ->end();
    }
}
