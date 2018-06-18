<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Compiler\Reader;

use Railt\Compiler\ParsingResult;
use Railt\Compiler\Reader\Resolver\PragmaResolver;
use Railt\Compiler\Reader\Resolver\ResolverInterface;
use Railt\Compiler\Reader\Resolver\RuleResolver;
use Railt\Compiler\Reader\Resolver\TokenResolver;
use Railt\Io\Readable;
use Railt\Lexer\TokenInterface;

/**
 * Class Parser
 */
class Parser
{
    private const STATE_CONFIGURE = 0x00;
    private const STATE_TOKEN = 0x01;
    private const STATE_PRODUCTIONS = 0x02;

    /**
     * @var array|ResolverInterface[]
     */
    private $resolvers;

    /**
     * Parser constructor.
     */
    public function __construct()
    {
        $this->resolvers = [
            self::STATE_CONFIGURE => new PragmaResolver(),
            self::STATE_TOKEN => new TokenResolver(),
            self::STATE_PRODUCTIONS => new RuleResolver(),
        ];
    }

    /**
     * @param TokenInterface $token
     * @return int
     */
    private function getState(TokenInterface $token): int
    {
        switch ($token->name()) {
            case 'T_PRAGMA':
                return self::STATE_CONFIGURE;
            case 'T_TOKEN':
            case 'T_SKIP':
                return self::STATE_TOKEN;
            default:
                return self::STATE_PRODUCTIONS;
        }
    }

    /**
     * @param Readable $file
     * @param TokenInterface $token
     */
    public function process(Readable $file, TokenInterface $token): void
    {
        $this->resolvers[$this->getState($token)]->resolve($file, $token);
    }

    public function getResult(): ParsingResult
    {
        dd($this->resolvers);
    }
}
