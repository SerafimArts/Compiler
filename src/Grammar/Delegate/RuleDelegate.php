<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Compiler\Grammar\Delegate;

use Railt\Parser\Ast\Delegate;
use Railt\Parser\Ast\Rule;
use Railt\Parser\Ast\RuleInterface;
use Railt\Parser\Environment;
use Railt\Parser\Grammar;
use Railt\Parser\GrammarInterface;

/**
 * Class RuleDelegate
 */
class RuleDelegate extends Rule implements Delegate
{
    /**
     * @param Environment $env
     */
    public function boot(Environment $env): void
    {
        /** @var GrammarInterface|Grammar $grammar */
        $grammar = $env->get(GrammarInterface::class);

        if ($delegate = $this->getDelegate()) {
            $grammar->addDelegate($this->getRuleName(), $delegate);
        }

        // TODO Add analyzer
    }

    /**
     * @return string
     */
    public function getRuleName(): string
    {
        return $this
            ->first('RuleName')
            ->first('T_NAME')
            ->getValue();
    }

    /**
     * @return bool
     */
    public function isKept(): bool
    {
        return (bool)$this->first('ShouldKeep');
    }

    /**
     * @return null|string
     */
    public function getDelegate(): ?string
    {
        $delegate = $this->first('RuleDelegate');

        if ($delegate instanceof RuleInterface) {
            return $delegate->first('T_NAME')->getValue();
        }

        return null;
    }
}
