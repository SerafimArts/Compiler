<?php
/**
 * This file is part of Railgun package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Serafim\Railgun\Contracts\Partials;

use Serafim\Railgun\Contracts\Types\TypeInterface;
use Serafim\Railgun\Contracts\TypeDefinitionInterface;

/**
 * Interface FieldTypeInterface
 * @package Serafim\Railgun\Contracts\Types
 */
interface FieldTypeInterface extends TypeInterface
{
    /**
     * @return TypeDefinitionInterface
     */
    public function getType(): TypeDefinitionInterface;

    /**
     * @return bool
     */
    public function isResolvable(): bool;

    /**
     * @return bool
     */
    public function isDeprecated(): bool;

    /**
     * @return string
     */
    public function getDeprecationReason(): string;

    /**
     * @param $value
     * @param array $arguments
     * @return mixed
     */
    public function resolve($value, array $arguments = []);

}