<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Compiler\Lexer;

use Railt\Compiler\Iterator\RegexNamedGroupsIterator;
use Railt\Compiler\Lexer\Result\Eoi;
use Railt\Compiler\Lexer\Result\Token;
use Railt\Compiler\Lexer\Result\Unknown;
use Railt\Compiler\TokenInterface;
use Railt\Io\Readable;

/**
 * Class NativeStateful
 */
class NativeStateful implements Stateful
{
    /**
     * @var string
     */
    protected $pattern;

    /**
     * @var array
     */
    protected $skipped;

    /**
     * NativeStateful constructor.
     * @param string $pattern
     * @param array $skipped
     */
    public function __construct(string $pattern, array $skipped = [])
    {
        $this->pattern = $pattern;
        $this->skipped = $skipped;
    }

    /**
     * @param Readable $input
     * @return \Traversable
     */
    public function lex(Readable $input): \Traversable
    {
        foreach ($this->exec($this->pattern, $input->getContents()) as $token) {
            if (! \in_array($token->name(), $this->skipped, true)) {
                yield $token;
            }
        }
    }

    /**
     * @param string $pattern
     * @param string $content
     * @return \Traversable|TokenInterface[]
     */
    protected function exec(string $pattern, string $content): \Traversable
    {
        $offset   = 0;
        $iterator = new RegexNamedGroupsIterator($pattern, $content);

        foreach ($iterator as $name => $context) {
            $token = $name === TokenInterface::UNKNOWN_TOKEN
                ? new Unknown($context[0], $offset)
                : new Token($name, $context, $offset);

            $offset += $token->bytes();

            yield $token;
        }

        yield new Eoi($offset);
    }
}
