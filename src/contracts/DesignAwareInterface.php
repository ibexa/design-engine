<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\DesignEngine;

interface DesignAwareInterface
{
    public const string DESIGN_NAMESPACE = 'ibexadesign';

    public function getCurrentDesign(): ?string;
}
