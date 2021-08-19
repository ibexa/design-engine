<?php

/*
 * This file is part of the EzPlatformDesignEngine package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\DesignEngine;

interface DesignAwareInterface
{
    /**
     * Returns current design.
     *
     * @return string
     */
    public function getCurrentDesign();
}

class_alias(DesignAwareInterface::class, 'EzSystems\EzPlatformDesignEngine\DesignAwareInterface');
