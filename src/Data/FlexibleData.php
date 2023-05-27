<?php

declare(strict_types = 1);

namespace KingsonDe\Marshal\Data;

use OutOfBoundsException;

class FlexibleData implements DataStructure, \ArrayAccess, \Iterator {

    /**
     * @var array
     */
    private array $data;

    /**
     * @var int
     */
    private int $position = 0;

    public function __construct(array $data = []) {
        $this->data = $data;
    }

    /**
     * @inheritdoc
     */
    public function build(): ?array
    {
        return $this->data;
    }

    /**
     * @param int|string $key
     * @return mixed
     * @throws OutOfBoundsException
     */
    public function get(int|string $key): mixed
    {
        if (!array_key_exists($key, $this->data)) {
            throw new OutOfBoundsException("No value set for $key.");
        }

        return $this->find($key);
    }

    /**
     * @param int|string $key
     * @param mixed|null $defaultValue
     * @return mixed
     */
    public function find(int|string $key, mixed $defaultValue = null): mixed
    {
        if (!array_key_exists($key, $this->data)) {
            return $defaultValue;
        }

        if (\is_scalar($this->data[$key])) {
            return $this->data[$key];
        }

        if (\is_array($this->data[$key])) {
            return new FlexibleData($this->data[$key]);
        }

        return $this->data[$key];
    }

    /**
     * @inheritdoc
     */
    public function offsetExists($offset): bool {
        return isset($this->data[$offset]);
    }

    /**
     * @inheritdoc
     */
    public function &offsetGet($offset): mixed {
        return $this->data[$offset];
    }

    /**
     * @inheritdoc
     */
    public function offsetSet($offset, $value): void {
        $this->data[$offset] = $value;
    }

    /**
     * @inheritdoc
     */
    public function offsetUnset($offset): void {
        unset($this->data[$offset]);
    }

    /**
     * @inheritdoc
     */
    public function current(): mixed {
        return new FlexibleData($this->data[$this->position]);
    }

    /**
     * @inheritdoc
     */
    public function next(): void {
        ++$this->position;
    }

    /**
     * @inheritdoc
     */
    public function key(): mixed {
        return $this->position;
    }

    /**
     * @inheritdoc
     */
    public function valid(): bool {
        return isset($this->data[$this->position]);
    }

    /**
     * @inheritdoc
     */
    public function rewind(): void {
        $this->position = 0;
    }
}
