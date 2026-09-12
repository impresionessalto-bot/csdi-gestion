<?php
declare(strict_types=1);

namespace App\Modules\Creator\Generator;

use RuntimeException;
use Throwable;

final class Creator
{
    private GeneratorRegistry $registry;

    public function __construct(?GeneratorRegistry $registry = null)
    {
        $this->registry = $registry ?? new GeneratorRegistry();
        if ($this->registry->count() === 0) {
            $this->registerDefaults();
        }
    }

    private function registerDefaults(): void
    {
        foreach ([
            new ModuleGenerator(),
            new ControllerGenerator(),
            new ServiceGenerator(),
            new RepositoryGenerator(),
            new RouteGenerator(),
            new ViewGenerator(),
            new CrudBuilder(),
            new BuilderGenerator(),
            new DashboardGenerator(),
            new WidgetGenerator(),
            new SqlGenerator(),
            new MigrationGenerator(),
            new SchemaGenerator(),
            new ApiGenerator(),
            new AiBuilderGenerator(),
            new AssetGenerator(),
            new PermissionGenerator(),
            new MenuGenerator(),
        ] as $generator) {
            $this->registry->register($generator);
        }
    }

    public function register(AbstractGenerator $generator): self
    {
        $this->registry->replace($generator);
        return $this;
    }

    public function generate(array $definition): array
    {
        $this->validate($definition);

        $selected = $definition['generators'] ?? null;
        $selected = is_array($selected) && $selected !== []
            ? array_fill_keys(array_map('strval', $selected), true)
            : null;

        $result = [
            'module' => $definition['module'],
            'status' => 'ok',
            'generated' => [],
            'skipped' => [],
            'errors' => [],
        ];

        foreach ($this->registry->all() as $generator) {
            if ($selected !== null && !isset($selected[$generator->name()])) {
                $result['skipped'][] = $generator->name();
                continue;
            }

            try {
                $generator->generate($definition);
                $result['generated'][] = $generator->name();
            } catch (Throwable $e) {
                $result['errors'][] = [
                    'generator' => $generator->name(),
                    'message' => $e->getMessage(),
                ];
                $result['status'] = 'partial';
            }
        }

        return $result;
    }

    public function generators(): array
    {
        return $this->registry->all();
    }

    public function count(): int
    {
        return $this->registry->count();
    }

    public function clear(): self
    {
        $this->registry->clear();
        return $this;
    }

    private function validate(array $definition): void
    {
        if (empty($definition['module'])) {
            throw new RuntimeException('Debe indicar el nombre del módulo.');
        }

        if (empty($definition['table'])) {
            throw new RuntimeException('Debe indicar la tabla.');
        }

        if (!isset($definition['fields']) || !is_array($definition['fields'])) {
            throw new RuntimeException('Debe definir los campos.');
        }
    }
}
