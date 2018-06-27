<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Compiler\Grammar\PP2\Delegate\Production;

use Railt\Parser\Ast\NodeInterface;
use Railt\Parser\Ast\RuleInterface;
use Railt\Parser\Rule\Repetition;
use Railt\Parser\Rule\Symbol;

/**
 * Class RepetitionDelegate
 */
class RepetitionDelegate extends BaseProductionDelegate
{
    /**
     * @return Symbol
     */
    public function create(): Symbol
    {
        [$from, $to] = $this->getInterval($this->first('RepetitionInterval'));

        return new Repetition($this->getId(), $from, $to, $this->getChildrenIds(), $this->symbolName);
    }

    /**
     * @param RuleInterface|NodeInterface $repetition
     * @return array
     */
    private function getInterval(RuleInterface $repetition): array
    {
        switch (true) {
            case (bool)$repetition->first('T_ZERO_OR_MORE'):
                return [0, Repetition::INF_MAX_VALUE];

            case (bool)$repetition->first('T_ONE_OR_MORE'):
                return [1, Repetition::INF_MAX_VALUE];

            case (bool)$repetition->first('T_ZERO_OR_ONE'):
                return [0, 1];

            case (bool)($repeat = $repetition->first('Repeat')):
                $value = (int)$repeat->first('T_NUMBER')->getValue();

                return [$value, $value];
                break;

            default:
                $from = $to = Repetition::INF_MAX_VALUE;

                if ($nodeFrom = $repetition->first('From')) {
                    $from = (int)$nodeFrom->first('T_NUMBER')->getValue();
                }

                if ($nodeTo = $repetition->first('To')) {
                    $to = (int)$nodeTo->first('T_NUMBER')->getValue();
                }

                return [$from, $to];
        }
    }
}
