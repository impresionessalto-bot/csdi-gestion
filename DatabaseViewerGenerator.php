<?php
declare(strict_types=1);

namespace App\Modules\Creator\Generator;

use PDO;
use RuntimeException;

final class DatabaseViewerGenerator extends AbstractGenerator
{
    public function __construct(private readonly PDO $db, ?TemplateResolver $resolver = null, ?TemplateEngine $engine = null)
    {
        parent::__construct($resolver, $engine);
    }

    public function name(): string
    {
        return 'DatabaseViewerGenerator';
    }

    public function generate(array $definition): void
    {
        $module = $this->validateModuleName((string) $this->required($definition, 'module'));
        $content = $this->render('schema.tpl.php', [
            'tablas' => $this->obtenerTablas(),
            'actual' => '',
            'columnas' => [],
            'registros' => [],
            'MODULE' => $module,
        ]);
        $this->write($this->file($definition, 'Views/database.php'), $content, (bool) $this->config()['overwrite']);
    }

    public function obtenerTablas(): array
    {
        $stmt = $this->db->query('SHOW TABLES');
        return $stmt ? $stmt->fetchAll(PDO::FETCH_COLUMN) : [];
    }

    public function obtenerEstructuraTabla(string $tabla): array
    {
        $tabla = $this->safeTable($tabla);
        $stmt = $this->db->query('DESCRIBE `' . $tabla . '`');
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    public function obtenerDatosTabla(string $tabla, int $limite = 50): array
    {
        $tabla = $this->safeTable($tabla);
        $limite = max(1, min(500, $limite));
        $stmt = $this->db->query('SELECT * FROM `' . $tabla . '` LIMIT ' . $limite);
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    public function renderPreview(?string $tabla = null, int $limite = 50): string
    {
        $tabla = trim((string) $tabla);
        $columnas = $tabla !== '' ? $this->obtenerEstructuraTabla($tabla) : [];
        $registros = $tabla !== '' ? $this->obtenerDatosTabla($tabla, $limite) : [];

        return $this->render('schema.tpl.php', [
            'tablas' => $this->obtenerTablas(),
            'actual' => $tabla,
            'columnas' => $columnas,
            'registros' => $registros,
        ]);
    }

    public function generar(array $data): string
    {
        return $this->renderPreview($data['tabla'] ?? null, (int) ($data['limite'] ?? 50));
    }

    private function safeTable(string $tabla): string
    {
        if (!preg_match('/^[A-Za-z0-9_]+$/', $tabla)) {
            throw new RuntimeException('Nombre de tabla inválido.');
        }
        return $tabla;
    }
}
