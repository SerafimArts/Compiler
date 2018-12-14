<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Compiler\Grammar\Delegate;

use Railt\Parser\Ast\Rule;
use Railt\Parser\Ast\RuleInterface;

/**
 * Class TokenDelegate
 */
class TokenDelegate extends Rule
{
    /**
     * IncludeDelegate constructor.
     * @param RuleInterface $rule
     */
    public function __construct(RuleInterface $rule)
    {
        parent::__construct($rule->getName(), $rule->getChildren(), $rule->getOffset());
    }

    /**
     * @return bool
     */
    public function isKept(): bool
    {
        return $this->getChild(0)->getName() === 'T_TOKEN';
    }

    /**
     * @return string
     */
    public function getTokenName(): string
    {
        return $this->getChild(0)->getValue(1);
    }

    /**
     * @return string
     */
    public function getTokenPattern(): string
    {
        return $this->getChild(0)->getValue(2);
    }
}
