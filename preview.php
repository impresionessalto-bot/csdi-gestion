<?php
declare(strict_types=1);

function h(mixed $value): string
{
    return htmlspecialchars(
        (string)$value,
        ENT_QUOTES,
        'UTF-8'
    );
}

$definition = $definition ?? [];

$module = $definition['module'] ?? '';
$table  = $definition['table'] ?? '';
$icon   = $definition['icon'] ?? 'bi-grid';

$fields = $definition['fields'] ?? [];
?>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h2 class="mb-0">

                <i class="bi bi-eye"></i>

                Vista previa

            </h2>

            <small class="text-muted">

                Revise la información antes de generar el módulo.

            </small>

        </div>

        <div>

            <a
                href="/creator/fields"
                class="btn btn-outline-secondary">

                <i class="bi bi-arrow-left"></i>

                Volver

            </a>

        </div>

    </div>





    <div class="row">

        <div class="col-lg-4">

            <div class="card shadow-sm mb-4">

                <div class="card-header">

                    Información

                </div>

                <div class="card-body">

                    <table class="table table-sm mb-0">

                        <tr>

                            <th width="120">

                                Módulo

                            </th>

                            <td>

                                <?= h($module) ?>

                            </td>

                        </tr>

                        <tr>

                            <th>

                                Tabla

                            </th>

                            <td>

                                <?= h($table) ?>

                            </td>

                        </tr>

                        <tr>

                            <th>

                                Icono

                            </th>

                            <td>

                                <i class="<?= h($icon) ?>"></i>

                                <?= h($icon) ?>

                            </td>

                        </tr>

                        <tr>

                            <th>

                                Campos

                            </th>

                            <td>

                                <?= count($fields) ?>

                            </td>

                        </tr>

                    </table>

                </div>

            </div>




            <div class="card shadow-sm">

                <div class="card-header">

                    Archivos que se crearán

                </div>

                <div class="card-body">

                    <ul class="list-group">

                        <li class="list-group-item">
                            ✅ Module.php
                        </li>

                        <li class="list-group-item">
                            ✅ Controller.php
                        </li>

                        <li class="list-group-item">
                            ✅ Service.php
                        </li>

                        <li class="list-group-item">
                            ✅ Repository.php
                        </li>

                        <li class="list-group-item">
                            ✅ Config/routes.php
                        </li>

                        <li class="list-group-item">
                            ✅ Views/index.php
                        </li>

                        <li class="list-group-item">
                            ✅ Views/form.php
                        </li>

                        <li class="list-group-item">
                            ✅ Views/show.php
                        </li>

                        <li class="list-group-item">
                            ✅ CSS
                        </li>

                        <li class="list-group-item">
                            ✅ JavaScript
                        </li>

                        <li class="list-group-item">
                            ✅ SQL
                        </li>

                    </ul>

                </div>

            </div>

        </div>





        <div class="col-lg-8">

            <div class="card shadow-sm">

                <div class="card-header">

                    Definición de Campos

                </div>

                <div class="table-responsive">

                    <table class="table table-bordered table-hover mb-0">

                        <thead class="table-light">

                        <tr>

                            <th>

                                Campo

                            </th>

                            <th>

                                Tipo

                            </th>

                            <th>

                                Longitud

                            </th>

                            <th>

                                PK

                            </th>

                            <th>

                                AI

                            </th>

                            <th>

                                Null

                            </th>

                            <th>

                                Visible

                            </th>

                            <th>

                                Buscar

                            </th>

                            <th>

                                Editar

                            </th>

                        </tr>

                        </thead>

                        <tbody>

                        <?php foreach ($fields as $field): ?>

                            <tr>

                                <td>

                                    <?= h($field['name'] ?? '') ?>

                                </td>

                                <td>

                                    <?= h($field['type'] ?? '') ?>

                                </td>

                                <td>

                                    <?= h($field['length'] ?? '') ?>

                                </td>

                                <td class="text-center">

                                    <?= !empty($field['pk']) ? '✔' : '' ?>

                                </td>

                                <td class="text-center">

                                    <?= !empty($field['ai']) ? '✔' : '' ?>

                                </td>

                                <td class="text-center">

                                    <?= !empty($field['nullable']) ? '✔' : '' ?>

                                </td>

                                <td class="text-center">

                                    <?= !empty($field['visible']) ? '✔' : '' ?>

                                </td>

                                <td class="text-center">

                                    <?= !empty($field['search']) ? '✔' : '' ?>

                                </td>

                                <td class="text-center">

                                    <?= !empty($field['edit']) ? '✔' : '' ?>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                        <?php if (empty($fields)): ?>

                            <tr>

                                <td
                                    colspan="9"
                                    class="text-center text-muted">

                                    No se definieron campos.

                                </td>

                            </tr>

                        <?php endif; ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>





    <div class="mt-4 d-flex justify-content-between">

        <a
            href="/creator/fields"
            class="btn btn-secondary">

            <i class="bi bi-arrow-left"></i>

            Volver

        </a>

        <form
            method="post"
            action="/creator/generate">

            <button
                class="btn btn-success btn-lg">

                <i class="bi bi-magic"></i>

                Generar Módulo

            </button>

        </form>

    </div>

</div>