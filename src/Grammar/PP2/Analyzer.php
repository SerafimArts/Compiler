<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Compiler\Grammar\PP2;

use Railt\Parser\Rule\Production;
use Railt\Parser\Rule\Symbol;
use Railt\Parser\Rule\Terminal;

/**
 * Class Analyzer
 */
class Analyzer
{
    /**
     * A list of parsed rules
     *
     * @var array|Symbol[]|Terminal[]|Production[]
     */
    private $parsed;

    /**
     * A list of kept rule names.
     *
     * @var array
     */
    private $keep;

    /**
     * Analyzer constructor.
     * @param array $parsed
     * @param array $keep
     */
    public function __construct(array $parsed, array $keep)
    {
        $this->parsed = $parsed;
        $this->keep   = $keep;
    }

    public function add(string $rule, iterable $tokens): void
    {
    }

    /**
     * @return array
     */
    public function getResult(): array
    {
        return $this->parsed;
    }
}
