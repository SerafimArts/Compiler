<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Compiler\Reader;
use Railt\Compiler\Reader\Resolver\PragmaResolver;
use Railt\Compiler\Reader\Resolver\RuleResolver;
use Railt\Compiler\Reader\Resolver\TokenResolver;
use Railt\Lexer\Driver\NativeStateless;
use Railt\Lexer\LexerInterface;
use Railt\Parser\Parser;
use Railt\Parser\ParserInterface;
use Railt\Parser\Rule\Token;

/**
 * Class Runtime
 */
class Runtime
{
    /**
     * @var RuleResolver
     */
    private $rules;

    /**
     * @var PragmaResolver
     */
    private $config;

    /**
     * @var TokenResolver
     */
    private $tokens;

    /**
     * Runtime constructor.
     * @param Result $result
     */
    public function __construct(Result $result)
    {
        $this->rules = $result->rulesResolver();
        $this->tokens = $result->tokensResolver();
        $this->config = $result->pragmasResolver();
    }
    /**
     * @return LexerInterface
     */
    public function getLexer(): LexerInterface
    {
        $lexer = new NativeStateless();

        foreach ($this->tokens->getTokens() as $name => $pcre) {
            $lexer->add($name, $pcre, \in_array($name, $this->tokens->getSkip(), true));
        }

        return $lexer;
    }

    /**
     * @return ParserInterface
     * @throws \InvalidArgumentException
     */
    public function getParser(): ParserInterface
    {
        $configs = $this->config->all(PragmaResolver::GROUP_PARSER);

        $parser = new Parser($this->getLexer(), $this->rules->getParsedRules(), $configs);

        foreach ($this->rules->getDelegates() as $name => $delegate) {
            $parser->addDelegate($name, $delegate);
        }

        return $parser;
    }
}
