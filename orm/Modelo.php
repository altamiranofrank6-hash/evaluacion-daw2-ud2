<?php
declare(strict_types=1);

abstract class Modelo
{
    protected static string $tabla;
    protected static array $campos;

    public static function activos(): array
    {
        $sql = 'SELECT * FROM ' . static::$tabla . '
                WHERE estado = 1
                ORDER BY id DESC';

        $consulta = Conexion::obtener()->query($sql);

        return $consulta->fetchAll(PDO::FETCH_CLASS, static::class);
    }

    public static function buscar(int $id): ?static
    {
        $sql = 'SELECT * FROM ' . static::$tabla . '
                WHERE id = ? AND estado = 1';

        $consulta = Conexion::obtener()->prepare($sql);
        $consulta->execute([$id]);
        $consulta->setFetchMode(PDO::FETCH_CLASS, static::class);

        return $consulta->fetch() ?: null;
    }

    public static function guardar(array $datos, ?int $id = null): void
    {
        $valores = array_map(fn ($campo) => $datos[$campo], static::$campos);

        if ($id === null) {
            $marcadores = implode(', ', array_fill(0, count(static::$campos), '?'));
            $sql = 'INSERT INTO ' . static::$tabla
                . ' (' . implode(', ', static::$campos) . ')'
                . ' VALUES (' . $marcadores . ')';
        } else {
            $asignaciones = array_map(fn ($campo) => $campo . ' = ?', static::$campos);
            $sql = 'UPDATE ' . static::$tabla
                . ' SET ' . implode(', ', $asignaciones)
                . ' WHERE id = ? AND estado = 1';

            $valores[] = $id;
        }

        Conexion::obtener()->prepare($sql)->execute($valores);
    }

    public static function desactivar(int $id): void
    {
        $sql = 'UPDATE ' . static::$tabla . '
                SET estado = 0
                WHERE id = ? AND estado = 1';

        $consulta = Conexion::obtener()->prepare($sql);
        $consulta->execute([$id]);
    }
}
