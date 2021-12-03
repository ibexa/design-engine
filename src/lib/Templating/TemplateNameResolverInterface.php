<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\DesignEngine\Templating;

/**
 * Interface for template name resolvers.
 * A template name resolver will check provided template name and resolve it for current design.
 */
interface TemplateNameResolverInterface
{
    public const MAIN_DESIGN_NAMESPACE = 'ibexadesign';

    /**
     * @deprecated Use \Ibexa\DesignEngine\Templating\TemplateNameResolverInterface::MAIN_DESIGN_NAMESPACE instead.
     */
    public const EZ_DESIGN_NAMESPACE = self::MAIN_DESIGN_NAMESPACE;

    public const DESIGN_NAMESPACES = [
        self::MAIN_DESIGN_NAMESPACE,
        'ezdesign'
    ];

    /**
     * Resolves provided template name within current design and returns properly namespaced template name.
     *
     * @param string $name Template name to resolve
     *
     * @return string
     */
    public function resolveTemplateName($name);

    /**
     * Checks if provided template name is using @ezdesign namespace.
     *
     * @param string $name
     *
     * @return bool
     */
    public function isTemplateDesignNamespaced($name);
}

class_alias(TemplateNameResolverInterface::class, 'EzSystems\EzPlatformDesignEngine\Templating\TemplateNameResolverInterface');
