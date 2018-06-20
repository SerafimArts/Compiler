<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Compiler;

use Railt\Compiler\Reader\GrammarInterface;
use Railt\Compiler\Reader\Result;
use Railt\Io\Readable;

/**
 * Class Reader
 */
class Reader implements GrammarInterface
{
    /**
     * @var GrammarInterface
     */
    private $grammar;

    /**
     * Reader constructor.
     * @param string $language
     */
    public function __construct(string $language)
    {
        \assert(\is_subclass_of($language, GrammarInterface::class));

        $this->grammar = new $language;
    }

    /**
     * @param Readable $input
     * @return GrammarInterface
     */
    public function add(Readable $input): GrammarInterface
    {
        return $this->grammar->add($input);
    }

    /**
     * @return Result
     */
    public function make(): Result
    {
        return $this->grammar->make();
    }
}
