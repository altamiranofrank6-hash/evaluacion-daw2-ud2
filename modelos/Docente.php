<?php
declare(strict_types=1);

class Docente extends Modelo
{
    protected static string $tabla = 'docente';
    protected static array $campos = [
        'id_facultad',
        'dni',
        'apellidos',
        'nombres',
        'correo',
        'categoria',
        'dedicacion',
    ];

    public int $id;
    public int $id_facultad;
    public string $dni;
    public string $apellidos;
    public string $nombres;
    public string $correo;
    public string $categoria;
    public string $dedicacion;
    public int $estado;
    public string $facultad;

    public static function conFacultad(): array
    {
        $sql = 'SELECT docente.*, facultad.nombre AS facultad
                FROM docente
                INNER JOIN facultad ON facultad.id = docente.id_facultad
                WHERE docente.estado = 1
                ORDER BY docente.id DESC';

        return Conexion::obtener()->query($sql)->fetchAll(PDO::FETCH_CLASS, self::class);
    }
}
