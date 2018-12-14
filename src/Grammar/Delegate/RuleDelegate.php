<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Compiler\Grammar\Delegate;

use Railt\Compiler\Grammar\LookaheadIterator;
use Railt\Lexer\Result\Eoi;
use Railt\Lexer\Result\Token;
use Railt\Lexer\TokenInterface;
use Railt\Parser\Ast\LeafInterface;
use Railt\Parser\Ast\NodeInterface;
use Railt\Parser\Ast\Rule;
use Railt\Parser\Ast\RuleInterface;

/**
 * Class RuleDelegate
 */
class RuleDelegate extends Rule
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
     * @return iterable|TokenInterface[]|LookaheadIterator
     */
    public function getInnerTokens(): iterable
    {
        return new LookaheadIterator((function () {
            yield from $this->getTokens($this->getProduction());
            yield new Eoi(0);
        })->call($this));
    }

    /**
     * @param RuleInterface|NodeInterface $rule
     * @return \Traversable
     */
    private function getTokens(RuleInterface $rule): \Traversable
    {
        /** @var LeafInterface $child */
        foreach ($rule->getChildren() as $child) {
            if ($child instanceof RuleInterface) {
                yield from $this->getTokens($child);
            } else {
                yield new Token($child->getName(), $child->getValues(), $child->getOffset());
            }
        }
    }

    /**
     * @return LeafInterface|NodeInterface|RuleInterface
     * @throws \LogicException
     */
    private function getProduction(): RuleInterface
    {
        foreach ($this->getChildren() as $child) {
            if ($child->is('RuleProduction')) {
                return $child;
            }
        }

        throw new \LogicException('Could not find RuleProduction node');
    }

    /**
     * @return string
     * @throws \LogicException
     */
    public function getRuleName(): string
    {
        foreach ($this->getChildren() as $child) {
            if ($child->is('RuleName')) {
                return $child->getValue(0);
            }
        }

        throw new \LogicException('Could not find RuleName node');
    }

    /**
     * @return bool
     */
    public function isKept(): bool
    {
        foreach ($this->getChildren() as $child) {
            if ($child->is('ShouldKeep')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return string|null
     */
    public function getDelegate(): ?string
    {
        foreach ($this->getChildren() as $child) {
            if ($child->is('RuleDelegate')) {
                return $child->getValue(0);
            }
        }

        return null;
    }
}
