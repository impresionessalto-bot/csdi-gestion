<?php
declare(strict_types=1);

namespace App\Modules\Counter\Controllers;

use App\Core\BaseController;
use App\Modules\Counter\Services\Service;
use App\Modules\Counter\DTOs\RegisterReadingDTO;
use Throwable;

final class Controller extends BaseController
{
    public function __construct(
        private readonly Service $service
    ) {
    }

    public function index(): void
    {
        $this->requireAuth();
        try {
            $data = $this->service->index();
            $this->view('index', $data, 'layouts/app');
        } catch (Throwable $e) {
            $this->fail($e);
        }
    }

    public function clientes(): void
    {
        $this->requireAuth();
        try {
            $localidadId = (int)($_GET['localidad_id'] ?? 0);
            $clientes = $this->service->clientes($localidadId);

            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => true, 'clientes' => $clientes], JSON_UNESCAPED_UNICODE);
            exit;
        } catch (Throwable $e) {
            header('Content-Type: application/json; charset=utf-8');
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            exit;
        }
    }

    public function equipos(): void
    {
        $this->requireAuth();
        try {
            $clienteId = (int)($_GET['cliente_id'] ?? 0);
            $equipos = $this->service->equipos($clienteId);

            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => true, 'equipos' => $equipos], JSON_UNESCAPED_UNICODE);
            exit;
        } catch (Throwable $e) {
            header('Content-Type: application/json; charset=utf-8');
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            exit;
        }
    }

    public function ultimo(): void
    {
        $this->requireAuth();
        try {
            $equipoId = (int)($_GET['equipo_id'] ?? 0);
            $lectura = $this->service->lastReading($equipoId);

            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => true, 'data' => $lectura], JSON_UNESCAPED_UNICODE);
            exit;
        } catch (Throwable $e) {
            header('Content-Type: application/json; charset=utf-8');
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            exit;
        }
    }

    public function historial(int $equipoId): void
    {
        $this->requireAuth();
        try {
            $historial = $this->service->historial($equipoId);

            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => true, 'data' => $historial], JSON_UNESCAPED_UNICODE);
            exit;
        } catch (Throwable $e) {
            header('Content-Type: application/json; charset=utf-8');
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            exit;
        }
    }

    public function guardar(): void
    {
        $this->requireAuth();
        try {
            $clienteId = (int)($_POST['cliente_id'] ?? 0);
            $equipoId = (int)($_POST['equipo_id'] ?? 0);
            $contadorActual = (int)($_POST['contador_actual'] ?? 0);
            $contadorColor = (int)($_POST['contador_actual_color'] ?? 0);
            $fecha = $_POST['fecha'] ?? date('Y-m-d');

            // Mapeo adaptado de forma exacta al constructor de tu RegisterReadingDTO
            $dto = new RegisterReadingDTO($clienteId, $equipoId, $contadorActual, $contadorColor, $fecha);

            $accion = $_POST['accion_tecnica'] ?? 'LECTURA';
            $motivo = $_POST['motivo_movimiento'] ?? '';

            if ($accion === 'RETIRO') {
                $this->service->retirarEquipo($dto->equipo_id, $motivo, $dto);
            } elseif ($accion === 'CAMBIO') {
                $equipoEntranteId = (int)($_POST['equipo_entrante_id'] ?? 0);
                $this->service->cambiarEquipo($dto->equipo_id, $equipoEntranteId, $motivo, $dto);
            } else {
                $this->service->registerReading($dto);
            }

            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => true, 'message' => 'Operación procesada con éxito.']);
            exit;
        } catch (Throwable $e) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            exit;
        }
    }

    public function exportarPdf(): void
    {
        $this->requireAuth();
        try {
            $filtros = [
                'desde' => $_GET['desde'] ?? null,
                'hasta' => $_GET['hasta'] ?? null
            ];
            $this->service->exportarPdfLecturas($filtros);
        } catch (Throwable $e) {
            $this->fail($e);
        }
    }

    public function exportarExcel(): void
    {
        $this->requireAuth();
        try {
            $filtros = [
                'desde' => $_GET['desde'] ?? null,
                'hasta' => $_GET['hasta'] ?? null
            ];
            $this->service->exportarExcelLecturas($filtros);
        } catch (Throwable $e) {
            $this->fail($e);
        }
    }
}