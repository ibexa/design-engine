<?php

/*
 * This file is part of the EzPlatformDesignEngine package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\DesignEngine;

use eZ\Publish\Core\MVC\ConfigResolverInterface;

trait DesignAwareTrait
{
    /**
     * @var ConfigResolverInterface
     */
    private $configResolver;

    public function setConfigResolver(ConfigResolverInterface $configResolver)
    {
        $this->configResolver = $configResolver;
    }

    /**
     * Returns the current design.
     *
     * @return string
     */
    public function getCurrentDesign()
    {
        return $this->configResolver->getParameter('design');
    }
}

class_alias(DesignAwareTrait::class, 'EzSystems\EzPlatformDesignEngine\DesignAwareTrait');
