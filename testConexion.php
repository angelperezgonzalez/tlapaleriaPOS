<?php
require_once 'database/DBConnection.php';

$db = DBConnection::getInstance()->getConnection();

if ($db) {
    echo "✅ Conexión exitosa a la base de datos.";
} else {
    echo "❌ Error de conexión.";
}
?>