<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Compiler\Reader\Resolver\Builder;

use Railt\Parser\Rule\Symbol;
use Railt\Parser\Rule\Token;

/**
 * Class TokenBuilder
 */
class TokenBuilder extends Builder
{
    /**
     * @var bool
     */
    private $keep;

    /**
     * TokenBuilder constructor.
     * @param int $id
     * @param string $name
     * @param bool $keep
     */
    public function __construct(int $id, string $name, bool $keep)
    {
        parent::__construct($id, $name);
        $this->keep = $keep;
    }

    /**
     * @return Symbol
     */
    public function build(): Symbol
    {
        return new Token($this->getId(), $this->getName(), $this->keep);
    }
}
