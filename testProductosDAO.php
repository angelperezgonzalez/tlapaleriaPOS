<?php
require_once 'models/ProductoDAO.php';

$dao = new ProductoDAO();


// Agregar un producto de prueba
$dao->agregar("Martillo Test", 150.00, 10);

echo "<p>✅ Producto agregado.</p>";

$productos = $dao->obtenerTodos();

echo "<h2>📦 Lista de Productos</h2>";
foreach ($productos as $p) {
    echo "ID: {$p->id} | Nombre: {$p->nombre} | Precio: {$p->precio} | Stock: {$p->stock}<br>";
}
?>