<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Compiler\Grammar\PP2\Delegate\Production;

use Railt\Io\Readable;
use Railt\Parser\Ast\Rule;
use Railt\Parser\Rule\Symbol;

/**
 * Class BaseRuleDelegate
 */
abstract class BaseProductionDelegate extends Rule
{
    /**
     * @var int
     */
    private static $lastId = 0;

    /**
     * @var Readable
     */
    private $file;

    /**
     * @var int
     */
    private $id;

    /**
     * @var Symbol
     */
    private $symbol;

    /**
     * @var string|null
     */
    private $symbolName;

    /**
     * @param string|null $name
     */
    public function setSymbolName(?string $name): void
    {
        $this->symbolName = $name;
    }

    /**
     * @return null|string
     */
    public function getSymbolName(): ?string
    {
        return $this->symbolName;
    }

    /**
     * @param Readable $file
     */
    public function setFile(Readable $file): void
    {
        $this->file = $file;

        foreach ($this->getChildren() as $child) {
            if ($child instanceof self) {
                $child->setFile($file);
            }
        }
    }

    /**
     * @return Readable
     */
    public function getFile(): Readable
    {
        return $this->file;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        if ($this->id === null) {
            $this->id = self::$lastId++;
        }

        return $this->id;
    }

    /**
     * @return Symbol
     */
    public function getRuleDefinition(): Symbol
    {
        if ($this->symbol === null) {
            $this->symbol = $this->create();
        }

        return $this->symbol;
    }

    /**
     * @return array
     */
    public function getChildrenIds(): array
    {
        $result = [];

        foreach ($this->getChildren() as $child) {
            if ($child instanceof self) {
                $result[] = $child->getId();
            }
        }

        return $result;
    }

    /**
     * @return Symbol
     */
    abstract protected function create(): Symbol;
}
