<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Compiler\Grammar\PP2\Delegate;

use Railt\Parser\Ast\LeafInterface;
use Railt\Parser\Ast\NodeInterface;
use Railt\Parser\Ast\Rule;

/**
 * Class TokenDelegate
 */
class TokenDefinitionDelegate extends Rule
{
    /**
     * @return LeafInterface|NodeInterface
     */
    private function getToken(): LeafInterface
    {
        return $this->getChild(0);
    }

    /**
     * @return string
     */
    public function getDefinitionName(): string
    {
        return $this->getToken()->getValue(1);
    }

    /**
     * @return string
     */
    public function getDefinitionValue(): string
    {
        return $this->getToken()->getValue(2);
    }

    /**
     * @return bool
     */
    public function isKept(): bool
    {
        return $this->getToken()->getName() === 'T_TOKEN';
    }
}
