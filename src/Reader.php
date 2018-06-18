<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Compiler;

use Railt\Compiler\Exception\IncludeNotFoundException;
use Railt\Compiler\Exception\UnrecognizedTokenException;
use Railt\Compiler\Reader\Parser;
use Railt\Io\File;
use Railt\Io\Readable;
use Railt\Lexer\LexerInterface;
use Railt\Lexer\Result\Eoi;
use Railt\Lexer\Result\Unknown;
use Railt\Lexer\TokenInterface;

/**
 * Class Reader
 */
class Reader
{
    /**
     * File extensions list
     */
    private const FILE_EXTENSIONS = [
        '',
        '.pp',
        '.pp2',
    ];

    /**
     * @var LexerInterface
     */
    private $lexer;

    /**
     * Reader constructor.
     */
    public function __construct()
    {
        $this->lexer = new Lexer();
    }

    /**
     * @param Readable $input
     * @return ParsingResult
     * @throws \Railt\Io\Exception\NotReadableException
     * @throws \Railt\Io\Exception\ExternalFileException
     */
    public function read(Readable $input): ParsingResult
    {
        $parser = new Parser();

        /** @var Readable $file */
        foreach ($this->lex($input) as $file => $token) {
            $parser->process($file, $token);
        }

        return $parser->getResult();
    }

    /**
     * @param Readable $input
     * @return \Traversable
     * @throws \Railt\Io\Exception\NotReadableException
     * @throws \Railt\Io\Exception\ExternalFileException
     */
    private function lex(Readable $input): \Traversable
    {
        $tokens = $this->lexer->lex($input);

        foreach ($tokens as $token) {
            if ($token instanceof Unknown) {
                $error = \sprintf('Unrecognized token "%s" (%s)', $token->value(), $token->name());
                throw (new UnrecognizedTokenException($error))->throwsIn($input, $token->offset());
            }

            if ($token instanceof Eoi) {
                continue;
            }

            if ($token->name() === Lexer::T_INCLUDE) {
                yield from $this->lex($this->include($input, $token));
                continue;
            }

            yield $input => $token;
        }
    }

    /**
     * @param Readable $from
     * @param TokenInterface $token
     * @return Readable
     * @throws \Railt\Io\Exception\NotReadableException
     * @throws \Railt\Io\Exception\ExternalFileException
     */
    private function include(Readable $from, TokenInterface $token): Readable
    {
        $path = \trim($token->value(1), " \t\n\r\0\x0B\"'");

        foreach (self::FILE_EXTENSIONS as $extension) {
            $file = \dirname($from->getPathname()) . '/' . $path . $extension;

            if (\is_file($file)) {
                return File::fromPathname($file);
            }
        }

        $error = \sprintf('Could not read external grammar file "%s"', $path);
        throw (new IncludeNotFoundException($error))->throwsIn($from, $token->offset());
    }
}
