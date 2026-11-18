<?php
declare(strict_types=1);

$controlador = new AdministracionController();
$seccion = $_GET['seccion'] ?? 'facultades';
$accion = $_GET['accion'] ?? 'listar';
$metodo = $_SERVER['REQUEST_METHOD'];

if (!in_array($seccion, ['facultades', 'docentes'], true)) {
    http_response_code(404);
    exit('Página no encontrada');
}

if ($metodo === 'GET' && in_array($accion, ['listar', 'editar'], true)) {
    $controlador->mostrar($seccion, $accion);
} elseif ($metodo === 'POST' && $accion === 'guardar') {
    $controlador->guardar($seccion);
} elseif ($metodo === 'POST' && $accion === 'eliminar') {
    $controlador->eliminar($seccion);
} else {
    http_response_code(404);
    echo 'Ruta no encontrada';
}
