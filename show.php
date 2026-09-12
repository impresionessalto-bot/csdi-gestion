<h2>
<?= htmlspecialchars($cliente['nombre']) ?>
</h2>


<p>
Teléfono:
<?= htmlspecialchars($cliente['telefono'] ?? '') ?>
</p>


<p>
Email:
<?= htmlspecialchars($cliente['email'] ?? '') ?>
</p>