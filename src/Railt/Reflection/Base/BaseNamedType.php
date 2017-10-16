<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Reflection\Base;

use Railt\Reflection\Base\Behavior\BaseName;
use Railt\Reflection\Base\Containers\BaseDirectivesContainer;
use Railt\Reflection\Contracts\Types\NamedTypeDefinition;

/**
 * Class BaseNamedType
 */
abstract class BaseNamedType extends BaseType implements NamedTypeDefinition
{
    use BaseName;
    use BaseDirectivesContainer;

    /**
     * @return array
     */
    public function __sleep(): array
    {
        return \array_merge(parent::__sleep(), [
            'name',
            'description',
            'directives'
        ]);
    }
}
