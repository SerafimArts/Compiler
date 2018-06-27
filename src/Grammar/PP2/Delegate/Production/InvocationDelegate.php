<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Compiler\Grammar\PP2\Delegate\Production;

use Railt\Compiler\Exception\UnknownRuleException;
use Railt\Compiler\Exception\UnrecognizedTokenException;
use Railt\Compiler\Grammar\PP2;
use Railt\Compiler\Grammar\PP2\Lexer;
use Railt\Parser\Ast\LeafInterface;
use Railt\Parser\Rule\Symbol;
use Railt\Parser\Rule\Token;

/**
 * Class InvocationDelegate
 */
class InvocationDelegate extends BaseProductionDelegate
{
    /**
     * @var array|Token[]
     */
    private static $tokens = [];

    /**
     * @return Symbol
     * @throws \Railt\Io\Exception\ExternalFileException
     */
    public function create(): Symbol
    {
        /** @var LeafInterface $first */
        $first = $this->getChild(0);

        switch ($first->getName()) {
            case Lexer::T_INVOKE:
                return $this->toInvocation($first);
            case Lexer::T_KEPT:
                return $this->toToken($first, true);
            case Lexer::T_SKIPPED:
                return $this->toToken($first, false);
        }

        $error = \sprintf('Unprocessable terminal %s', $first);

        throw (new UnrecognizedTokenException($error))
            ->throwsIn($this->getFile(), $first->getOffset());
    }

    /**
     * @param LeafInterface $token
     * @return Symbol
     * @throws \Railt\Io\Exception\ExternalFileException
     */
    private function toInvocation(LeafInterface $token): Symbol
    {
        $rule = $token->getValue(1);

        if (! $this->env(PP2::ENV_RULES)->hasRule($rule)) {
            $error = \sprintf('Could not call an unexpected rule "%s"', $rule);
            throw (new UnknownRuleException($error))->throwsIn($this->file, $token->getOffset());
        }

        return $this->env(PP2::ENV_RULES)->getRule($rule);
    }

    /**
     * @param LeafInterface $token
     * @param bool $keep
     * @return Symbol
     */
    private function toToken(LeafInterface $token, bool $keep): Symbol
    {
        $name = $token->getValue(1);
        $hash = $name . ':' . ($keep ? '+' : '-');

        if (! \array_key_exists($hash, self::$tokens)) {
            self::$tokens[$hash] = new Token(parent::getId(), $name, $keep);
        }

        return self::$tokens[$hash];
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->getRuleDefinition()->getId();
    }
}
