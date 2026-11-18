<?php
declare(strict_types=1);

class Conexion
{
    public static function obtener(): PDO
    {
        static $conexion = null;

        if ($conexion === null) {
            $conexion = new PDO(
                'mysql:host=127.0.0.1;port=58475;dbname=evaluacion_daw2;charset=utf8mb4',
                'root',
                '',
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_EMULATE_PREPARES => false]
            );
        }

        return $conexion;
    }
}
