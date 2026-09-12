<?php
declare(strict_types=1);

namespace App\Modules\Creator\Generator;

use RuntimeException;

final class GeneratorRegistry
{
    /** @var array<string, AbstractGenerator> */
    private array $items = [];

    public function register(AbstractGenerator $generator): self
    {
        $name = $generator->name();
        if (isset($this->items[$name])) {
            throw new RuntimeException("El generador '{$name}' ya está registrado.");
        }
        $this->items[$name] = $generator;
        return $this;
    }

    public function replace(AbstractGenerator $generator): self
    {
        $this->items[$generator->name()] = $generator;
        return $this;
    }

    public function get(string $name): ?AbstractGenerator
    {
        return $this->items[$name] ?? null;
    }

    /** @return array<int, AbstractGenerator> */
    public function all(): array
    {
        return array_values($this->items);
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function clear(): self
    {
        $this->items = [];
        return $this;
    }
}
