<?php
declare(strict_types=1);
?>
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold text-white">Visualizador de Base de Datos - CSDI ERP</h2>
            <p class="text-muted">Panel de programación y auditoría de esquemas en tiempo real.</p>
        </div>
    </div>

    <div class="row">
        <!-- Selector de Tablas -->
        <div class="col-md-3">
            <div class="card bg-dark text-white shadow">
                <div class="card-header fw-bold border-secondary">Tablas del Sistema</div>
                <div class="card-body p-2" style="max-height: 70vh; overflow-y: auto;">
                    <div class="list-group list-group-flush bg-dark">
                        <?php foreach (($tablas ?? []) as $t): ?>
                            <a href="?tabla=<?= htmlspecialchars($t) ?>" 
                               class="list-group-item list-group-item-action bg-dark text-white border-secondary <?= ($actual ?? '') === $t ? 'active' : '' ?>">
                                <?= htmlspecialchars($t) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detalle de Estructura y Registros -->
        <div class="col-md-9">
            <?php if (!empty($actual)): ?>
                <div class="card bg-dark text-white shadow mb-4">
                    <div class="card-header fw-bold border-secondary">Estructura de la tabla: <?= htmlspecialchars($actual) ?></div>
                    <div class="card-body table-responsive">
                        <table class="table table-dark table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>Campo</th>
                                    <th>Tipo</th>
                                    <th>Nulo</th>
                                    <th>Llave</th>
                                    <th>Default</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (($columnas ?? []) as $col): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($col['Field'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($col['Type'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($col['Null'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($col['Key'] ?? '') ?></td>
                                        <td><?= htmlspecialchars((string)($col['Default'] ?? 'NULL')) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card bg-dark text-white shadow">
                    <div class="card-header fw-bold border-secondary">Primeros Registros (Vista previa)</div>
                    <div class="card-body table-responsive">
                        <?php if (!empty($registros)): ?>
                            <table class="table table-dark table-striped table-sm">
                                <thead>
                                    <tr>
                                        <?php foreach (array_keys($registros[0]) as $campo): ?>
                                            <th><?= htmlspecialchars($campo) ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($registros as $fila): ?>
                                        <tr>
                                            <?php foreach ($fila as $valor): ?>
                                                <td><?= htmlspecialchars((string)($valor ?? '')) ?></td>
                                            <?php endforeach; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <p class="text-muted mb-0">La tabla no contiene registros actualmente.</p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-secondary text-center py-5">
                    <h4>Seleccioná una tabla a la izquierda para inspeccionar su esquema y datos.</h4>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>