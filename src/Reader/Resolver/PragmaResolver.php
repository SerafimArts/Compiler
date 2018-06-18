<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Compiler\Reader\Resolver;

use Railt\Compiler\Exception\UnknownPragmaException;
use Railt\Io\Readable;
use Railt\Lexer\TokenInterface;
use Railt\Parser\Parser;

/**
 * Class PragmaResolver
 */
class PragmaResolver implements ResolverInterface
{
    private const ALLOWED_PRAGMAS = [
        Parser::PRAGMA_ROOT,
        Parser::PRAGMA_RUNTIME,
        Parser::PRAGMA_LOOKAHEAD,
    ];

    /**
     * @var array
     */
    private $configs = [];

    /**
     * @param Readable $readable
     * @param TokenInterface $token
     * @throws \Railt\Io\Exception\ExternalFileException
     */
    public function resolve(Readable $readable, TokenInterface $token): void
    {
        if (! \in_array($token->value(1), self::ALLOWED_PRAGMAS, true)) {
            $error = \vsprintf('Unknown configuration pragma rule "%s" with value "%s"', [
                $token->value(1),
                $token->value(2)
            ]);

            throw (new UnknownPragmaException($error))->throwsIn($readable, $token->offset());
        }

        $this->configs[$token->value(1)] = $token->value(2);
    }
}
