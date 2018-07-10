<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Compiler\Grammar\Delegate;

use Railt\Lexer\LexerInterface;
use Railt\Lexer\SimpleLexerInterface;
use Railt\Parser\Ast\Delegate;
use Railt\Parser\Ast\Rule;
use Railt\Parser\Environment;

/**
 * Class TokenDelegate
 */
class TokenDelegate extends Rule implements Delegate
{
    /**
     * @param Environment $env
     */
    public function boot(Environment $env): void
    {
        /** @var SimpleLexerInterface $lexer */
        $lexer = $env->get(LexerInterface::class);

        $lexer->add($this->getTokenName(), $this->getTokenPattern());

        if (! $this->isKept()) {
            $lexer->skip($this->getTokenName());
        }
    }

    /**
     * @return bool
     */
    protected function isKept(): bool
    {
        return $this->getChild(0)->getName() === 'T_TOKEN';
    }

    /**
     * @return string
     */
    protected function getTokenName(): string
    {
        return $this->getChild(0)->getValue(0);
    }

    /**
     * @return string
     */
    protected function getTokenPattern(): string
    {
        return $this->getChild(0)->getValue(1);
    }
}
