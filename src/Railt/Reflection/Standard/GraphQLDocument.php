<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Reflection\Standard;

use Railt\Reflection\Base\BaseDocument;
use Railt\Reflection\Compiler\CompilerInterface;
use Railt\Reflection\Contracts\Definitions\Definition;
use Railt\Reflection\Contracts\Definitions\ScalarDefinition;
use Railt\Reflection\Standard\Directives\Deprecation;
use Railt\Reflection\Standard\Scalars\AnyType;
use Railt\Reflection\Standard\Scalars\BooleanType;
use Railt\Reflection\Standard\Scalars\DateTimeType;
use Railt\Reflection\Standard\Scalars\FloatType;
use Railt\Reflection\Standard\Scalars\IDType;
use Railt\Reflection\Standard\Scalars\IntType;
use Railt\Reflection\Standard\Scalars\StringType;

/**
 * This class contains a Document implementation for
 * the standard GraphQL library.
 */
class GraphQLDocument extends BaseDocument implements StandardType
{
    /**
     * The name of our document constant.
     */
    private const DOCUMENT_NAME = 'GraphQL Standard Library';

    /**
     * @var array
     */
    private $additionalTypes;

    /**
     * @var CompilerInterface
     */
    private $compiler;

    /**
     * GraphQLDocument constructor.
     * @param CompilerInterface $compiler
     * @param array $additionalTypes
     */
    public function __construct(CompilerInterface $compiler, array $additionalTypes = [])
    {
        $this->compiler = $compiler;
        $this->additionalTypes = $additionalTypes;
        $this->createStandardTypes();
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return self::DOCUMENT_NAME;
    }

    /**
     * Creation and registration of types.
     *
     * @return void
     */
    private function createStandardTypes(): void
    {
        foreach ($this->getStandardTypes() as $type) {
            /** @var Definition|mixed $instance */
            $instance = new $type($this);

            $this->types[$instance->getName()] = $instance;
        }
    }

    /**
     * Returns should return a list of all predefined GraphQL types.
     *
     * @return array|StandardType[]
     */
    private function getStandardTypes(): array
    {
        $standard = [
            // Scalars
            BooleanType::class,
            FloatType::class,
            IDType::class,
            IntType::class,
            StringType::class,
            AnyType::class,
            DateTimeType::class,

            // Directives
            Deprecation::class
        ];

        return \array_merge($standard, $this->additionalTypes);
    }
}
