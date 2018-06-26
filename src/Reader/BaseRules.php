<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Compiler\Reader;

use Railt\Compiler\Exception\GrammarException;
use Railt\Io\Readable;
use Railt\Parser\Ast\Delegate;
use Railt\Parser\Ast\Rule;
use Railt\Parser\Ast\RuleInterface;
use Railt\Parser\Rule\Production;
use Railt\Parser\Rule\Symbol;
use Railt\Parser\Rule\Terminal;

/**
 * Class BaseRules
 */
abstract class BaseRules implements ProvideRules
{
    /**
     * @var array
     */
    private $rules = [];

    /**
     * @param Symbol $rule
     */
    protected function add(Symbol $rule): void
    {
        $this->rules[] = $rule;
    }

    public function all(): array
    {
        throw new \LogicException(__METHOD__ . ' not implemented yet');
    }

    public function getDelegates(): iterable
    {
        throw new \LogicException(__METHOD__ . ' not implemented yet');
    }

    public function getFile(string $rule): Readable
    {
        throw new \LogicException(__METHOD__ . ' not implemented yet');
    }

    public function has(string $rule): bool
    {
        throw new \LogicException(__METHOD__ . ' not implemented yet');
    }

    public function isKeep(string $rule): bool
    {
        throw new \LogicException(__METHOD__ . ' not implemented yet');
    }
}
