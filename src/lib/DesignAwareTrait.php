<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\DesignEngine;

use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;

trait DesignAwareTrait
{
    /**
     * @var \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface
     */
    private $configResolver;

    public function setConfigResolver(ConfigResolverInterface $configResolver): void
    {
        $this->configResolver = $configResolver;
    }

    public function getCurrentDesign(): ?string
    {
        return $this->configResolver->getParameter('design');
    }
}
