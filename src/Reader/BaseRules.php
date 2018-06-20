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
use Railt\Parser\Rule\Production;
use Railt\Parser\Rule\Symbol;
use Railt\Parser\Rule\Terminal;

/**
 * Class BaseRules
 */
abstract class BaseRules implements ProvideRules
{
    /**
     * @var array|Symbol[]
     */
    private $rules = [];

    /**
     * @var array
     */
    private $mappings = [];

    /**
     * @var array
     */
    private $delegates = [];

    /**
     * @var array|Readable[]
     */
    private $files = [];

    /**
     * @param Symbol $symbol
     */
    protected function add(Symbol $symbol): void
    {
        $this->rules[$symbol->getId()] = $symbol;

        $providesName = $symbol instanceof Terminal ||
            ($symbol instanceof Production && $symbol->getName());

        if ($providesName) {
            $this->mappings[$symbol->getName()] = $symbol->getId();
        }
    }

    /**
     * @param string $rule
     * @param string $delegate
     * @throws GrammarException
     */
    protected function addDelegate(string $rule, string $delegate): void
    {
        if (! \class_exists($delegate)) {
            $error = 'Could not found delegate class "%s"';
            throw new GrammarException(\sprintf($error, $delegate));
        }

        if (! \is_subclass_of($delegate, Delegate::class)) {
            $error = 'Delegate should be an instance of %s, but %s given';
            throw new GrammarException(\sprintf($error, Delegate::class, $delegate));
        }

        $this->delegates[$rule] = $delegate;
    }

    /**
     * @param string $rule
     * @param Readable $grammar
     */
    protected function addFile(string $rule, Readable $grammar): void
    {
        $this->files[$rule] = $grammar;
    }

    /**
     * @return array
     */
    public function all(): array
    {
        return dd($this->rules);
    }

    /**
     * @param string $rule
     * @return bool
     */
    public function has(string $rule): bool
    {
        return \array_key_exists($rule, $this->rules);
    }

    /**
     * @return iterable
     */
    public function getDelegates(): iterable
    {
        return $this->delegates;
    }

    /**
     * @param string $rule
     * @return Readable
     */
    public function getFile(string $rule): Readable
    {
        return $this->files[$rule];
    }
}
