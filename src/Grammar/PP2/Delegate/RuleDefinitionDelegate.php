<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Compiler\Grammar\PP2\Delegate;

use Railt\Compiler\Grammar\PP2\Delegate\Production\ConcatenationDelegate;
use Railt\Parser\Ast\NodeInterface;
use Railt\Parser\Ast\Rule;
use Railt\Parser\Ast\RuleInterface;

/**
 * Class RuleDefinitionDelegate
 */
class RuleDefinitionDelegate extends Rule
{
    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->getProduction()->getId();
    }

    /**
     * @return string
     */
    public function getRuleName(): string
    {
        return $this->name()->first('T_NAME')->getValue();
    }

    /**
     * @return null|string
     */
    public function getDelegate(): ?string
    {
        $delegate = $this->name()->first('Delegate');

        return $delegate ? $delegate->first('T_NAME')->getValue() : null;
    }

    /**
     * @return RuleInterface|NodeInterface
     */
    private function name(): RuleInterface
    {
        return $this->first('Name');
    }

    /**
     * @return bool
     */
    public function isKept(): bool
    {
        return $this->name()->first('ShouldKeep') !== null;
    }

    /**
     * @return ConcatenationDelegate
     */
    public function getProduction(): ConcatenationDelegate
    {
        /** @var ConcatenationDelegate $production */
        $production = $this->first('Production');

        if (! $production->getSymbolName() && $this->isKept()) {
            $production->setSymbolName($this->getRuleName());
        }

        return $production;
    }
}
