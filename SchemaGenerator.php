<?php
declare(strict_types=1);

namespace App\Modules\Creator\Generator;

final class SchemaGenerator extends AbstractGenerator
{
    public function name(): string
    {
        return 'SchemaGenerator';
    }

    public function generate(array $definition): void
    {
        $moduleName = $this->required($definition, 'module');
        $moduleLower = strtolower($moduleName);
        $tableName = $definition['table'] ?? $moduleLower;
        
        $templateFile = 'schema.tpl';

        $defaultContent = "-- Schema SQL automático para el módulo " . $moduleName . "\n" .
            "CREATE TABLE IF NOT EXISTS `" . $tableName . "` (\n" .
            "    `id` INT AUTO_INCREMENT PRIMARY KEY,\n" .
            "    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,\n" .
            "    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP\n" .
            ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;\n";

        if (is_file($templateFile)) {
            $content = $this->render($templateFile, [
                'MODULE' => $moduleName,
                'LOWER'  => $moduleLower,
                'TABLE'  => $tableName,
            ]);
        } else {
            $content = $defaultContent;
        }

        $destination = $this->file($definition, 'Database/' . $moduleName . 'Schema.sql');

        $this->write(
            $destination,
            $content,
            $this->config()['overwrite']
        );
    }
}