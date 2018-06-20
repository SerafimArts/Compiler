<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Compiler\Reader;

use Railt\Lexer\LexerInterface;
use Railt\Parser\ParserInterface;

/**
 * Class Result
 */
class Result
{
    /**
     * @var ProvidePragmas
     */
    private $pragmas;

    /**
     * @var ProvideTokens
     */
    private $tokens;

    /**
     * @var ProvideRules
     */
    private $rules;

    /**
     * Result constructor.
     * @param ProvidePragmas $pragmas
     * @param ProvideTokens $tokens
     * @param ProvideRules $rules
     */
    public function __construct(ProvidePragmas $pragmas, ProvideTokens $tokens, ProvideRules $rules)
    {
        $this->pragmas = $pragmas;
        $this->tokens  = $tokens;
        $this->rules   = $rules;
    }

    /**
     * @return ProvidePragmas
     */
    public function getPragmas(): ProvidePragmas
    {
        return $this->pragmas;
    }

    /**
     * @return ProvideTokens
     */
    public function getTokens(): ProvideTokens
    {
        return $this->tokens;
    }

    /**
     * @return ProvideRules
     */
    public function getRules(): ProvideRules
    {
        return $this->rules;
    }

    /**
     * @return Runtime
     */
    public function getRuntime(): Runtime
    {
        return new Runtime($this);
    }

    /**
     * @return ParserInterface
     * @throws \InvalidArgumentException
     */
    public function getParser(): ParserInterface
    {
        return $this->getRuntime()->getParser();
    }

    /**
     * @return LexerInterface
     */
    public function getLexer(): LexerInterface
    {
        return $this->getRuntime()->getLexer();
    }
}
