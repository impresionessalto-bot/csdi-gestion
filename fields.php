<?php
declare(strict_types=1);
?>

<div class="card shadow-sm mt-4">

    <div class="card-header d-flex justify-content-between align-items-center">

        <div>

            <i class="bi bi-table"></i>

            Campos del módulo

        </div>

        <button
            type="button"
            class="btn btn-success btn-sm"
            id="btnAddField">

            <i class="bi bi-plus-circle"></i>

            Agregar Campo

        </button>

    </div>

    <div class="card-body p-0">

        <table
            class="table table-bordered table-hover mb-0"
            id="creatorFields">

            <thead class="table-light">

            <tr>

                <th width="18%">Campo</th>

                <th width="14%">Tipo</th>

                <th width="10%">Long.</th>

                <th width="8%">Null</th>

                <th width="8%">PK</th>

                <th width="8%">AI</th>

                <th width="10%">Índice</th>

                <th width="10%">FK</th>

                <th width="14%">Acciones</th>

            </tr>

            </thead>

            <tbody>

            </tbody>

        </table>

    </div>

</div>

<template id="fieldRowTemplate">

<tr>

<td>

<input
class="form-control"
name="fields[][name]"
placeholder="nombre">

</td>

<td>

<select
class="form-select"
name="fields[][type]">

<option>VARCHAR</option>

<option>TEXT</option>

<option>INT</option>

<option>BIGINT</option>

<option>DECIMAL</option>

<option>BOOLEAN</option>

<option>DATE</option>

<option>DATETIME</option>

<option>TIME</option>

<option>JSON</option>

</select>

</td>

<td>

<input
class="form-control"
name="fields[][length]"
value="255">

</td>

<td class="text-center">

<input
type="checkbox"
name="fields[][nullable]">

</td>

<td class="text-center">

<input
type="checkbox"
name="fields[][pk]">

</td>

<td class="text-center">

<input
type="checkbox"
name="fields[][ai]">

</td>

<td>

<select
class="form-select"
name="fields[][index]">

<option value="">No</option>

<option value="INDEX">INDEX</option>

<option value="UNIQUE">UNIQUE</option>

</select>

</td>

<td>

<input
class="form-control"
name="fields[][fk]"
placeholder="tabla.id">

</td>

<td class="text-center">

<button
type="button"
class="btn btn-danger btnDeleteField">

<i class="bi bi-trash"></i>

</button>

</td>

</tr>

</template>