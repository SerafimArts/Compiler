<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Reflection\Builder\Support;

use Hoa\Compiler\Llk\TreeNode;
use Railt\Reflection\Base\Behavior\BaseDeprecations;
use Railt\Reflection\Base\Containers\BaseDirectivesContainer;
use Railt\Reflection\Builder\Directive\DirectiveInvocationBuilder;
use Railt\Reflection\Contracts\Behavior\Deprecatable;
use Railt\Reflection\Contracts\Behavior\Nameable;
use Railt\Reflection\Contracts\Containers\HasDirectives;
use Railt\Reflection\Contracts\Types\Directive\DirectiveInvocation;
use Railt\Reflection\Contracts\Types\TypeDefinition;
use Railt\Reflection\Exceptions\TypeConflictException;
use Railt\Reflection\Standard\Directives\Deprecation;

/**
 * Trait DirectivesBuilder
 */
trait DirectivesBuilder
{
    /**
     * @param TreeNode $ast
     * @return bool
     * @throws \Railt\Reflection\Exceptions\TypeConflictException
     */
    protected function compileDirectivesBuilder(TreeNode $ast): bool
    {
        if ($this instanceof HasDirectives && $ast->getId() === '#Directive') {
            /** @var BaseDirectivesContainer|Nameable $this */
            $relation = new DirectiveInvocationBuilder($ast, $this->getDocument(), $this);

            $this->checkDirectiveLocation($relation);

            $this->directives[$relation->getName()] = $relation;

            $this->checkTheDeprecationDirective($relation);

            return true;
        }

        return false;
    }

    /**
     * @param DirectiveInvocation $relation
     * @return void
     * @throws \Railt\Reflection\Exceptions\TypeConflictException
     */
    private function checkDirectiveLocation(DirectiveInvocation $relation): void
    {
        $directive = $relation->getDirective();

        if ($this instanceof TypeDefinition && ! $directive->isAllowedFor($this)) {
            $error = 'The usage of the @%s directive together with type %s<%s> is not allowed by the ' .
                'locations of this directive (%s).';

            $error = \sprintf(
                $error,
                $directive->getName(),
                $this->getTypeName(),
                $this->getName(),
                \implode(', ', $directive->getLocations())
            );

            throw new TypeConflictException($error);
        }
    }

    /**
     * @param DirectiveInvocation $directive
     * @return void
     */
    private function checkTheDeprecationDirective(DirectiveInvocation $directive): void
    {
        if ($this instanceof Deprecatable && $directive->getName() === Deprecation::TYPE_NAME) {
            /** @var BaseDeprecations|Deprecatable $this */
            $this->deprecationReason = $directive->hasArgument(Deprecation::REASON_ARGUMENT)
                ? $this->getDeprecationReasonValue($directive)
                : $this->getDeprecationReasonDefaultValue($directive);
        }
    }

    /**
     * @param DirectiveInvocation $directive
     * @return string
     */
    private function getDeprecationReasonValue(DirectiveInvocation $directive): string
    {
        return (string)$directive
            ->getArgument(Deprecation::REASON_ARGUMENT)
            ->getValue();
    }

    /**
     * @param DirectiveInvocation $directive
     * @return string
     */
    private function getDeprecationReasonDefaultValue(DirectiveInvocation $directive): string
    {
        return $directive->getDirective()
            ->getArgument(Deprecation::REASON_ARGUMENT)
            ->getDefaultValue();
    }
}
