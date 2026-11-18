<?php
function escapar(mixed $valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Administración universitaria</title>
    <link rel="stylesheet" href="public/css/estilos.css">
</head>
<body>
    <main>
        <h1>Administración universitaria</h1>

        <nav>
            <a href="index.php?seccion=facultades">Facultades</a>
            <a href="index.php?seccion=docentes">Docentes</a>
        </nav>

        <section>
            <h2>
                <?= !empty($datos['id']) ? 'Editar' : 'Registrar' ?>
                <?= $seccion === 'facultades' ? 'facultad' : 'docente' ?>
            </h2>

            <?php foreach ($errores as $error): ?>
                <p class="error"><?= escapar($error) ?></p>
            <?php endforeach; ?>

            <form method="post"
                  action="index.php?seccion=<?= escapar($seccion) ?>&amp;accion=guardar">
                <input type="hidden" name="token" value="<?= escapar($_SESSION['token']) ?>">
                <input type="hidden" name="id" value="<?= escapar($datos['id'] ?? '') ?>">

                <?php if ($seccion === 'facultades'): ?>
                    <label>Nombre
                        <input name="nombre"
                               maxlength="120"
                               required
                               value="<?= escapar($datos['nombre'] ?? '') ?>">
                    </label>
                    <label>Abreviatura
                        <input name="abreviatura"
                               maxlength="20"
                               required
                               value="<?= escapar($datos['abreviatura'] ?? '') ?>">
                    </label>
                <?php else: ?>
                    <label>Facultad
                        <select name="id_facultad" required>
                            <option value="">Seleccione una facultad</option>
                            <?php foreach ($facultades as $facultad): ?>
                                <option value="<?= $facultad->id ?>"
                                    <?= (string) ($datos['id_facultad'] ?? '') === (string) $facultad->id ? 'selected' : '' ?>>
                                    <?= escapar($facultad->nombre) ?><?= $facultad->estado ? '' : ' (inactiva)' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <label>DNI
                        <input name="dni"
                               maxlength="8"
                               pattern="[0-9]{8}"
                               required
                               value="<?= escapar($datos['dni'] ?? '') ?>">
                    </label>
                    <label>Apellidos
                        <input name="apellidos"
                               maxlength="100"
                               required
                               value="<?= escapar($datos['apellidos'] ?? '') ?>">
                    </label>
                    <label>Nombres
                        <input name="nombres"
                               maxlength="100"
                               required
                               value="<?= escapar($datos['nombres'] ?? '') ?>">
                    </label>
                    <label>Correo
                        <input type="email"
                               name="correo"
                               maxlength="150"
                               required
                               value="<?= escapar($datos['correo'] ?? '') ?>">
                    </label>
                    <label>Categoría
                        <select name="categoria" required>
                            <option value="">Seleccione una categoría</option>
                            <?php foreach ($categorias as $categoria): ?>
                                <option value="<?= escapar($categoria) ?>"
                                    <?= ($datos['categoria'] ?? '') === $categoria ? 'selected' : '' ?>>
                                    <?= escapar($categoria) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <label>Dedicación
                        <select name="dedicacion" required>
                            <option value="">Seleccione una dedicación</option>
                            <?php foreach ($dedicaciones as $dedicacion): ?>
                                <option value="<?= escapar($dedicacion) ?>"
                                    <?= ($datos['dedicacion'] ?? '') === $dedicacion ? 'selected' : '' ?>>
                                    <?= escapar($dedicacion) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                <?php endif; ?>

                <button type="submit">Guardar</button>
                <?php if (!empty($datos['id'])): ?>
                    <a href="index.php?seccion=<?= escapar($seccion) ?>">Cancelar</a>
                <?php endif; ?>
            </form>
        </section>

        <section>
            <h2><?= $seccion === 'facultades' ? 'Facultades activas' : 'Docentes activos' ?></h2>
            <?php if (!$registros): ?>
                <p>No hay registros activos.</p>
            <?php else: ?>
                <div class="tabla">
                    <table>
                        <thead>
                            <tr>
                                <?php if ($seccion === 'facultades'): ?>
                                    <th>Nombre</th>
                                    <th>Abreviatura</th>
                                <?php else: ?>
                                    <th>Facultad</th>
                                    <th>DNI</th>
                                    <th>Apellidos</th>
                                    <th>Nombres</th>
                                    <th>Correo</th>
                                    <th>Categoría</th>
                                    <th>Dedicación</th>
                                <?php endif; ?>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($registros as $registro): ?>
                                <tr>
                                    <?php if ($seccion === 'facultades'): ?>
                                        <td><?= escapar($registro->nombre) ?></td>
                                        <td><?= escapar($registro->abreviatura) ?></td>
                                    <?php else: ?>
                                        <td><?= escapar($registro->facultad) ?></td>
                                        <td><?= escapar($registro->dni) ?></td>
                                        <td><?= escapar($registro->apellidos) ?></td>
                                        <td><?= escapar($registro->nombres) ?></td>
                                        <td><?= escapar($registro->correo) ?></td>
                                        <td><?= escapar($registro->categoria) ?></td>
                                        <td><?= escapar($registro->dedicacion) ?></td>
                                    <?php endif; ?>
                                    <td>
                                        <a href="index.php?seccion=<?= escapar($seccion) ?>&amp;accion=editar&amp;id=<?= $registro->id ?>">
                                            Editar
                                        </a>

                                        <form class="inline"
                                              method="post"
                                              action="index.php?seccion=<?= escapar($seccion) ?>&amp;accion=eliminar">
                                            <input type="hidden" name="token" value="<?= escapar($_SESSION['token']) ?>">
                                            <input type="hidden" name="id" value="<?= $registro->id ?>">
                                            <button type="submit">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
