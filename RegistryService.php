<?php
declare(strict_types=1);

namespace App\Modules\Creator\Generator;

final class RegistryService
{
    public function __construct(private readonly GeneratorRegistry $registry)
    {
    }

    public function names(): array
    {
        return array_map(
            static fn (AbstractGenerator $generator): string => $generator->name(),
            $this->registry->all()
        );
    }

    public function all(): array
    {
        return $this->registry->all();
    }
}