<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Reflection\Compiler;

use Railt\Reflection\Contracts\Behavior\Nameable;
use Railt\Reflection\Contracts\Document;
use Railt\Reflection\Contracts\Types\NamedTypeDefinition;
use Railt\Reflection\Contracts\Types\TypeDefinition;
use Railt\Reflection\Exceptions\TypeConflictException;

/**
 * Class Repository
 */
class Repository implements Dictionary, \Countable, \IteratorAggregate
{
    /**
     * Pattern for type override exception messages.
     */
    private const REDEFINITION_ERROR = 'Cannot declare "%s", because the name is already in use in "%s"';

    /**
     * First level types storage.
     * A set of types where in the format:
     * <code>
     *  [
     *      ...
     *      {TypeName} =>  NamedTypeInterface::class,
     *      {ClassName} => TypeInterface::class,
     *      ...
     *  ]
     * </code>
     *
     * @var array
     */
    private $l1cache = [];

    /**
     * Second level types storage.
     * A set of types where the key is the Document in the format:
     * <code>
     *  [
     *      ...
     *      {DocumentUniqueId} => [
     *          {TypeName} =>  NamedTypeInterface::class,
     *          {ClassName} => TypeInterface::class,
     *          ...
     *      ],
     *      ...
     *  ]
     * </code>
     *
     * @var array
     */
    private $l2cache = [];

    /**
     * @param TypeDefinition $type
     * @param bool $force
     * @return Dictionary
     * @throws TypeConflictException
     */
    public function register(TypeDefinition $type, bool $force = false): Dictionary
    {
        $id = $this->getDocumentIdentifier($type->getDocument());

        if ($type instanceof NamedTypeDefinition) {
            $this->registerNamedType($id, $type, $force);
        } else {
            $this->registerAnonymousType($id, $type, $force);
        }

        return $this;
    }

    /**
     * @param Document $document
     * @return string
     */
    private function getDocumentIdentifier(Document $document): string
    {
        $identifier = $document->getUniqueId();

        if (! \array_key_exists($identifier, $this->l2cache)) {
            $this->l2cache[$identifier] = [];
        }

        return $identifier;
    }

    /**
     * @param string $key
     * @param NamedTypeDefinition $type
     * @param bool $force
     * @return void
     * @throws TypeConflictException
     */
    private function registerNamedType(string $key, NamedTypeDefinition $type, bool $force): void
    {
        if (! $force) {
            $this->verifyNamedTypeConsistency($type);
        }

        $this->l1cache[$type->getName()] = $this->l2cache[$key][$this->getTypeIdentifier($type)] = $type;
    }

    /**
     * @param NamedTypeDefinition $type
     * @return void
     * @throws TypeConflictException
     */
    private function verifyNamedTypeConsistency(NamedTypeDefinition $type): void
    {
        $registered = $this->l1cache[$type->getName()] ?? null;

        if ($registered instanceof NamedTypeDefinition) {
            $this->throwRedefinitionException($registered, $type);
        }
    }

    /**
     * @param TypeDefinition $registered
     * @param TypeDefinition $type
     * @return void
     * @throws TypeConflictException
     */
    private function throwRedefinitionException(TypeDefinition $registered, TypeDefinition $type): void
    {
        $what = $type->getTypeName();
        if ($type instanceof Nameable) {
            $what = \sprintf('%s<%s>', $type->getTypeName(), $type->getName());
        }

        $doc = $registered->getDocument();
        $into = \sprintf('%s<%s>', $doc->getTypeName(), $doc->getName());

        $error = \sprintf(self::REDEFINITION_ERROR, $what, $into);
        throw new TypeConflictException($error);
    }

    /**
     * @param string $key
     * @param TypeDefinition $type
     * @param bool $force
     * @return void
     * @throws TypeConflictException
     */
    private function registerAnonymousType(string $key, TypeDefinition $type, bool $force): void
    {
        if (! $force) {
            $this->verifyAnonymousTypeConsistency($type);
        }

        $this->l1cache[$key] = $this->l2cache[$key][$this->getTypeIdentifier($type)] = $type;
    }

    /**
     * @param TypeDefinition $type
     * @return void
     * @throws TypeConflictException
     */
    private function verifyAnonymousTypeConsistency(TypeDefinition $type): void
    {
        $registered = $this->l1cache[\get_class($type)] ?? null;

        if ($registered !== null && ! ($registered instanceof NamedTypeDefinition)) {
            $this->throwRedefinitionException($registered, $type);
        }
    }

    /**
     * @param Document|null $document
     * @return array
     */
    public function all(Document $document = null): array
    {
        if ($document === null) {
            return \array_values($this->l1cache);
        }

        return $this->l2cache[$this->getDocumentIdentifier($document)];
    }

    /**
     * @param string $name
     * @param Document|null $document
     * @return bool
     */
    public function has(string $name, Document $document = null): bool
    {
        return $this->get($name, $document) instanceof TypeDefinition;
    }

    /**
     * @param string $name
     * @param Document|null $document
     * @return null|TypeDefinition
     */
    public function get(string $name, Document $document = null): ?TypeDefinition
    {
        if ($document === null) {
            return $this->l1cache[$name] ?? null;
        }

        $id = $this->getDocumentIdentifier($document);

        return $this->l2cache[$id][$name] ?? null;
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return \count($this->l1cache);
    }

    /**
     * @return \Traversable
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->l1cache);
    }

    /**
     * @param TypeDefinition $type
     * @return string
     */
    public function getTypeIdentifier(TypeDefinition $type): string
    {
        if ($type instanceof Nameable) {
            return $type->getName();
        }

        return \get_class($type);
    }
}
