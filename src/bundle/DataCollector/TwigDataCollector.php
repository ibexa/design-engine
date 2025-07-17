<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\Bundle\DesignEngine\DataCollector;

use Ibexa\DesignEngine\Templating\TemplatePathRegistryInterface;
use Symfony\Bridge\Twig\DataCollector\TwigDataCollector as BaseCollector;
use Twig\Environment;
use Twig\Profiler\Profile;

class TwigDataCollector extends BaseCollector
{
    public function __construct(
        Profile $profile,
        Environment $environment,
        private TemplatePathRegistryInterface $templatePathRegistry
    ) {
        parent::__construct($profile, $environment);
    }

    private function getTemplatePathRegistry(): TemplatePathRegistryInterface
    {
        return $this->templatePathRegistry;
    }

    #[\Override]
    public function lateCollect(): void
    {
        parent::lateCollect();
        $this->data['template_path_registry'] = serialize($this->templatePathRegistry);
    }

    /**
     * @return array<string, int>
     */
    #[\Override]
    public function getTemplates(): array
    {
        $registry = $this->getTemplatePathRegistry();
        $templates = [];
        foreach (parent::getTemplates() as $template => $count) {
            $templates[sprintf('%s (%s)', $template, $registry->getTemplatePath($template))] = $count;
        }

        return $templates;
    }
}
