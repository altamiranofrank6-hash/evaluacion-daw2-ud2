<?php
declare(strict_types=1);

session_start();
require __DIR__ . '/configuracion/Conexion.php';
require __DIR__ . '/orm/Modelo.php';
require __DIR__ . '/modelos/Facultad.php';
require __DIR__ . '/modelos/Docente.php';
require __DIR__ . '/controladores/AdministracionController.php';
require __DIR__ . '/rutas/web.php';
