<?php
declare(strict_types=1);

namespace App\Modules\Creator\Controllers;

use App\Core\BaseController;
use App\Modules\Creator\Generator\Creator;
use App\Modules\Creator\Generator\DatabaseViewerGenerator;
use Throwable;

final class Controller extends BaseController
{
    private Creator $creator;

    public function __construct()
    {
        $this->creator = new Creator();
    }

    public function index(): void
    {
        $this->requireAuth();

        try {
            $this->moduleView('Creator', 'index', [
                'title' => 'CSDI ERP Studio',
                'generators' => $this->creator->generators(),
                'result' => null,
                'csrf' => $this->csrf(),
            ]);
        } catch (Throwable $e) {
            $this->fail($e);
        }
    }

    public function database(): void
    {
        $this->requireAuth();

        try {
            $viewer = new DatabaseViewerGenerator($this->db);
            $tabla = trim((string) ($_GET['tabla'] ?? ''));

            $this->moduleView('Creator', 'database', [
                'title' => 'CSDI ERP Studio - Base de Datos',
                'tablas' => $viewer->obtenerTablas(),
                'actual' => $tabla,
                'columnas' => $tabla !== '' ? $viewer->obtenerEstructuraTabla($tabla) : [],
                'registros' => $tabla !== '' ? $viewer->obtenerDatosTabla($tabla) : [],
                'csrf' => $this->csrf(),
            ]);
        } catch (Throwable $e) {
            $this->fail($e);
        }
    }

    public function generate(): void
    {
        $this->requireAuth();

        try {
            $definition = [
                'module' => trim((string) ($_POST['module'] ?? '')),
                'entity' => trim((string) ($_POST['entity'] ?? '')),
                'table' => trim((string) ($_POST['table'] ?? '')),
                'icon' => trim((string) ($_POST['icon'] ?? 'bi-grid')),
                'fields' => isset($_POST['fields']) && is_array($_POST['fields']) ? $_POST['fields'] : [],
                'generators' => isset($_POST['generators']) && is_array($_POST['generators']) ? $_POST['generators'] : null,
                'builders' => isset($_POST['builders']) && is_array($_POST['builders']) ? $_POST['builders'] : null,
                'widgets' => isset($_POST['widgets']) && is_array($_POST['widgets']) ? $_POST['widgets'] : null,
            ];

            $result = $this->creator->generate($definition);

            $this->moduleView('Creator', 'index', [
                'title' => 'CSDI ERP Studio',
                'generators' => $this->creator->generators(),
                'result' => $result,
                'csrf' => $this->csrf(),
            ]);
        } catch (Throwable $e) {
            $this->moduleView('Creator', 'index', [
                'title' => 'CSDI ERP Studio',
                'generators' => $this->creator->generators(),
                'error' => $e->getMessage(),
                'csrf' => $this->csrf(),
            ]);
        }
    }
}
