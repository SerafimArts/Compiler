<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Compiler\Grammar\PP2;

use Railt\Compiler\Reader\Result;
use Railt\Io\Readable;
use Railt\Lexer\TokenInterface;

/**
 * Class Parser
 */
class Parser
{
    private const STATE_CONFIGURE   = 0x00;
    private const STATE_TOKEN       = 0x01;
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
        $tokens  = new TokenResolver();
        $pragmas = new PragmaResolver();
        $rules   = new RuleResolver($tokens);

        $this->resolvers = [
            self::STATE_TOKEN       => $tokens,
            self::STATE_CONFIGURE   => $pragmas,
            self::STATE_PRODUCTIONS => $rules,
        ];
    }

    /**
     * @param Readable $file
     * @param TokenInterface $token
     */
    public function process(Readable $file, TokenInterface $token): void
    {
        $this->resolvers[$this->getState($token)]->resolve($file, $token);
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
     * @return Result
     */
    public function getResult(): Result
    {
        return new Result(
            $this->resolvers[self::STATE_CONFIGURE],
            $this->resolvers[self::STATE_TOKEN],
            $this->resolvers[self::STATE_PRODUCTIONS]
        );
    }
}
