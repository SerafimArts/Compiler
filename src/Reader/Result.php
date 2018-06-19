<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Compiler\Reader;

use Railt\Compiler\Reader\Analyzer\GrammarAnalyzer;
use Railt\Compiler\Reader\Resolver\PragmaResolver;
use Railt\Compiler\Reader\Resolver\RuleResolver;
use Railt\Compiler\Reader\Resolver\TokenResolver;
use Railt\Lexer\LexerInterface;
use Railt\Parser\ParserInterface;

/**
 * Class Result
 */
class Result
{
    /**
     * @var Analyzer\Analyzer[]
     */
    private const RULE_ANALYZERS = [
        GrammarAnalyzer::class
    ];

    /**
     * @var PragmaResolver
     */
    private $pragmas;

    /**
     * @var TokenResolver
     */
    private $tokens;

    /**
     * @var RuleResolver
     */
    private $rules;

    /**
     * Result constructor.
     * @param PragmaResolver $pragmas
     * @param TokenResolver $tokens
     * @param RuleResolver $rules
     */
    public function __construct(PragmaResolver $pragmas, TokenResolver $tokens, RuleResolver $rules)
    {
        $this->pragmas = $pragmas;
        $this->tokens  = $tokens;
        $this->rules   = $rules;
    }

    /**
     * @return TokenResolver
     */
    public function tokensResolver(): TokenResolver
    {
        return $this->tokens;
    }

    /**
     * @return RuleResolver
     */
    public function rulesResolver(): RuleResolver
    {
        return $this->rules;
    }

    /**
     * @return PragmaResolver
     */
    public function pragmasResolver(): PragmaResolver
    {
        return $this->pragmas;
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
