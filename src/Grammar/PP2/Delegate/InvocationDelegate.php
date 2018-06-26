<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Compiler\Grammar\PP2\Delegate;

use Railt\Parser\Ast\Rule;
use Railt\Parser\Rule\Symbol;

/**
 * Class InvocationDelegate
 */
class InvocationDelegate extends BaseRuleDelegate
{
    public function getRule(): Symbol
    {
        $first = $this->getChild(0);

        switch ($first->getName()) {
            case 'T_INVOKE':
                return $this->getRule();
        }

        dd((string)$first);
    }

    private function getRule(): Symbol
    {

    }

    private function getToken(bool $skip)
    {

    }
}
