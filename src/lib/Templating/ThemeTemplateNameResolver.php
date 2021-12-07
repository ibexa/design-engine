<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\DesignEngine\Templating;

use Ibexa\Core\MVC\ConfigResolverInterface;
use Ibexa\DesignEngine\DesignAwareTrait;

class ThemeTemplateNameResolver implements TemplateNameResolverInterface
{
    use DesignAwareTrait;

    /**
     * Collection of already resolved template names.
     *
     * @var array
     */
    private $resolvedTemplateNames = [];

    public function __construct(ConfigResolverInterface $configResolver)
    {
        $this->setConfigResolver($configResolver);
    }

    public function resolveTemplateName($name)
    {
        if (!$this->isTemplateDesignNamespaced($name)) {
            return $name;
        }

        return $this->resolvedTemplateNames[$name] ?? ($this->resolvedTemplateNames[$name] = str_replace(
                '@' . self::DESIGN_NAMESPACE,
                '@' . $this->getCurrentDesign(),
                $name
            ));
    }

    public function isTemplateDesignNamespaced($name)
    {
        return (strpos($name, '@' . self::DESIGN_NAMESPACE) !== false) || (strpos($name, '@' . $this->getCurrentDesign()) !== false);
    }
}

class_alias(ThemeTemplateNameResolver::class, 'EzSystems\EzPlatformDesignEngine\Templating\ThemeTemplateNameResolver');
