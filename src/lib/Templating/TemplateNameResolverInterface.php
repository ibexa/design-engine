<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\DesignEngine\Templating;

use Ibexa\Contracts\DesignEngine\DesignAwareInterface;

/**
 * Interface for template name resolvers.
 * A template name resolver will check provided template name and resolve it for current design.
 */
interface TemplateNameResolverInterface extends DesignAwareInterface
{
    /**
     * Resolves provided template name within current design and returns properly namespaced template name.
     *
     * @param string $name Template name to resolve
     */
    public function resolveTemplateName(string $name): string;

    /**
     * Checks if provided template name is using `@ibexadesign` namespace.
     */
    public function isTemplateDesignNamespaced(string $name): bool;
}
