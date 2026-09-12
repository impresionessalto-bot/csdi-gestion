<?php
/**
 * Vista Principal del ERP Studio (Creator)
 * Ubicación: App/Modules/Creator/Views/index.php
 */
?>

<!-- Hojas de Estilo Base y del Creator -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
<link rel="stylesheet" href="/assets/css/creator.css">
<link rel="stylesheet" href="/assets/css/wizard.css">
<link rel="stylesheet" href="/assets/css/preview.css">
<link rel="stylesheet" href="/assets/css/builder.css">

<div class="container-fluid creator-container py-4">
    <div class="row creator-header mb-4">
        <div class="col-12">
            <h2><i class="bi bi-ui-checks-grid"></i> CSDI ERP Studio</h2>
            <p class="text-muted">Constructor visual de módulos y gestión de componentes para el ERP.</p>
        </div>
    </div>

    <div class="row">
        <!-- Columna Principal: Wizard y Formulario -->
        <div class="col-lg-8 mb-4">
            <div class="creator-card p-4 shadow-sm bg-white rounded">
                <h4 class="mb-4"><i class="bi bi-plus-circle"></i> Generar Nuevo Módulo</h4>

                <!-- Indicadores del Wizard -->
                <div class="wizard-steps d-flex justify-content-between mb-4 position-relative">
                    <div class="wizard-step active text-center flex-fill" data-step="1">
                        <div class="step-number">1</div>
                        <span>Datos Básicos</span>
                    </div>
                    <div class="wizard-step text-center flex-fill" data-step="2">
                        <div class="step-number">2</div>
                        <span>Estructura / Campos</span>
                    </div>
                    <div class="wizard-step text-center flex-fill" data-step="3">
                        <div class="step-number">3</div>
                        <span>Generación</span>
                    </div>
                </div>

                <!-- Formulario Principal -->
                <form id="creator-form">
                    <!-- PASO 1: Datos Básicos -->
                    <div data-step-content="1">
                        <div class="mb-3">
                            <label for="module-name" class="form-label">Nombre del Módulo (PascalCase, ej: Articulos, Finanzas)</label>
                            <input type="text" class="form-control" id="module-name" name="module" required placeholder="Ej: Finanzas">
                        </div>
                        <div class="mb-3">
                            <label for="table-name" class="form-label">Nombre de la Tabla Base (en BD)</label>
                            <input type="text" class="form-control" id="table-name" name="table" required placeholder="Ej: creditos">
                        </div>
                        <div class="mb-3">
                            <label for="module-icon" class="form-label">Icono Bootstrap</label>
                            <input type="text" class="form-control" id="module-icon" name="icon" value="bi-grid" placeholder="Ej: bi-wallet2">
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-primary" data-wizard-next>Siguiente <i class="bi bi-arrow-right"></i></button>
                        </div>
                    </div>

                    <!-- PASO 2: Estructura de Campos -->
                    <div data-step-content="2" class="d-none">
                        <h5 class="mb-3">Configuración de Campos y Relaciones</h5>
                        <div class="builder-dropzone mb-3 p-4 text-center border rounded bg-light">
                            <p class="mb-0 text-muted"><i class="bi bi-tools"></i> Próximamente: Constructor interactivo de columnas y tipos de datos.</p>
                        </div>
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-secondary" data-wizard-prev><i class="bi bi-arrow-left"></i> Anterior</button>
                            <button type="button" class="btn btn-primary" data-wizard-next>Siguiente <i class="bi bi-arrow-right"></i></button>
                        </div>
                    </div>

                    <!-- PASO 3: Confirmación -->
                    <div data-step-content="3" class="d-none">
                        <h5 class="mb-3">Confirmación</h5>
                        <p class="text-muted">Se empaquetará la estructura completa de archivos en un paquete comprimido ZIP listo para Ferozo.</p>
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-secondary" data-wizard-prev><i class="bi bi-arrow-left"></i> Anterior</button>
                            <button type="submit" id="btn-generate-module" class="btn btn-success"><i class="bi bi-check-circle"></i> Descargar Módulo ZIP</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Columna Lateral -->
        <div class="col-lg-4">
            <div class="creator-card shadow-sm bg-white rounded">
                <div class="preview-mockup-header p-3 bg-dark text-white rounded-top">
                    <i class="bi bi-eye"></i> Vista Previa del Proyecto
                </div>
                <div class="preview-mockup-body p-3">
                    <p class="text-muted small">Estado de los generadores y componentes activos en el sistema.</p>
                    <ul class="list-group list-group-flush small">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Motor Creator <span class="badge bg-success">Operativo</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Template Engine <span class="badge bg-success">Operativo</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Generador ZIP <span class="badge bg-success">Operativo</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="/assets/js/creator/CreatorAjax.js"></script>
<script src="/assets/js/creator/CreatorWizard.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const wizard = new CreatorWizard('#creator-form', '.wizard-step');

        const form = document.querySelector('#creator-form');
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const submitBtn = document.querySelector('#btn-generate-module');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Empaquetando ZIP...';

            const formData = new FormData(form);

            try {
                const response = await fetch('/creator/generate', {
                    method: 'POST',
                    body: formData
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.message || 'Error al generar el módulo.');
                }

                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `${formData.get('module')}_module.zip`;
                document.body.appendChild(a);
                a.click();
                a.remove();
                window.URL.revokeObjectURL(url);

                alert('¡Módulo empaquetado y descargado con éxito!');
                window.location.reload();

            } catch (error) {
                alert('Error: ' + error.message);
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bi bi-check-circle"></i> Descargar Módulo ZIP';
            }
        });
    });
</script>