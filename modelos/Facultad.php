<?php
declare(strict_types=1);

class Facultad extends Modelo
{
    protected static string $tabla = 'facultad';
    protected static array $campos = ['nombre', 'abreviatura'];
    public int $id;
    public string $nombre;
    public string $abreviatura;
    public int $estado;

    public static function buscarCualquiera(int $id): ?self
    {
        $consulta = Conexion::obtener()->prepare('SELECT * FROM facultad WHERE id = ?');
        $consulta->execute([$id]);
        $consulta->setFetchMode(PDO::FETCH_CLASS, self::class);
        return $consulta->fetch() ?: null;
    }
}
