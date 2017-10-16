<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Reflection\Standard\Directives;

use Railt\Reflection\Base\BaseArgument;
use Railt\Reflection\Base\BaseDirective;
use Railt\Reflection\Contracts\Behavior\Inputable;
use Railt\Reflection\Contracts\Document;
use Railt\Reflection\Contracts\Types\ArgumentType;
use Railt\Reflection\Contracts\Types\Directive\Location;
use Railt\Reflection\Contracts\Types\DirectiveType;
use Railt\Reflection\Contracts\Types\TypeDefinition;
use Railt\Reflection\Standard\Directives\Deprecation\Reason;
use Railt\Reflection\Standard\StandardType;

/**
 * Class Deprecation
 *
 * @see https://github.com/graphql/graphql-js/pull/384
 */
final class Deprecation extends BaseDirective implements StandardType
{
    /**
     * Deprecation directive name
     */
    public const TYPE_NAME = 'deprecated';

    /**
     * Deprecation reason argument
     */
    public const REASON_ARGUMENT = 'reason';

    /**
     * Deprecation constructor.
     * @param Document $document
     */
    public function __construct(Document $document)
    {
        $this->document          = $document;
        $this->name              = self::TYPE_NAME;
        $this->deprecationReason = self::RFC_IMPL_DESCRIPTION;
        $this->locations         = Location::TARGET_GRAPHQL_SDL;

        $argument = $this->createReasonArgument();
        $this->arguments[$argument->getName()] = $argument;
    }

    /**
     * @return ArgumentType
     */
    private function createReasonArgument(): ArgumentType
    {
        return new Reason($this->getDocument(), $this);
    }
}
