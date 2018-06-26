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
 * Class PragmaDelegate
 */
class PragmaDefinitionDelegate extends Rule
{
    /**
     * @return LeafInterface|NodeInterface
     */
    private function getDefinition(): LeafInterface
    {
        return $this->getChild(0);
    }

    /**
     * @return string
     */
    public function getPragmaName(): string
    {
        return $this->getDefinition()->getValue(1);
    }

    /**
     * @return string
     */
    public function getPragmaValue(): string
    {
        return $this->getDefinition()->getValue(2);
    }
}
