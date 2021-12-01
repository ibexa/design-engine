<?php

/*
 * This file is part of the EzPlatformDesignEngine package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\DesignEngine\Exception;

use InvalidArgumentException;

class InvalidDesignException extends InvalidArgumentException
{
}

class_alias(InvalidDesignException::class, 'EzSystems\EzPlatformDesignEngine\Exception\InvalidDesignException');
