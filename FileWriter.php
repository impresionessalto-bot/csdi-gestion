<?php
declare(strict_types=1);

namespace App\Modules\Creator\Generator;

use RuntimeException;

final class FileWriter
{
    /**
     * Escribe un archivo.
     */
    public function write(
        string $file,
        string $content,
        bool $overwrite = false
    ): void {

        $directory = dirname($file);

        if (!is_dir($directory)) {

            if (!mkdir($directory, 0777, true) && !is_dir($directory)) {

                throw new RuntimeException(
                    "No fue posible crear {$directory}"
                );

            }

        }

        if (is_file($file) && !$overwrite) {

            return;

        }

        if (file_put_contents($file, $content) === false) {

            throw new RuntimeException(
                "No fue posible escribir {$file}"
            );

        }

    }

    /**
     * Existe un archivo.
     */
    public function exists(string $file): bool
    {
        return is_file($file);
    }

    /**
     * Crear directorio.
     */
    public function makeDirectory(
        string $directory
    ): void {

        if (is_dir($directory)) {
            return;
        }

        if (!mkdir($directory, 0777, true)) {

            throw new RuntimeException(
                "No fue posible crear {$directory}"
            );

        }

    }

    /**
     * Eliminar archivo.
     */
    public function delete(
        string $file
    ): bool {

        if (!$this->exists($file)) {
            return false;
        }

        return unlink($file);

    }

    /**
     * Leer archivo.
     */
    public function read(
        string $file
    ): string {

        if (!$this->exists($file)) {

            throw new RuntimeException(
                "Archivo inexistente: {$file}"
            );

        }

        return (string) file_get_contents($file);

    }
}