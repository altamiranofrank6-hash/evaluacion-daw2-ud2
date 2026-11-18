<?php
declare(strict_types=1);

class AdministracionController
{
    private const CATEGORIAS = ['AUXILIAR', 'ASOCIADO', 'PRINCIPAL'];
    private const DEDICACIONES = [
        'DEDICACION EXCLUSIVA-DE',
        'TIEMPO COMPLETO-TC',
        'TIEMPO PARCIAL-TP',
    ];

    public function mostrar(
        string $seccion,
        string $accion = 'listar',
        array $datos = [],
        array $errores = []
    ): void {
        $_SESSION['token'] ??= bin2hex(random_bytes(32));
        $modelo = $seccion === 'facultades' ? Facultad::class : Docente::class;

        if ($accion === 'editar' && !$datos) {
            $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
            $registro = $id ? $modelo::buscar($id) : null;

            if (!$registro) {
                http_response_code(404);
                exit('Registro no encontrado');
            }

            $datos = (array) $registro;
        }

        $registros = $seccion === 'facultades'
            ? Facultad::activos()
            : Docente::conFacultad();

        $facultades = Facultad::activos();

        if ($seccion === 'docentes' && !empty($datos['id_facultad'])) {
            $actual = Facultad::buscarCualquiera((int) $datos['id_facultad']);

            if ($actual && !$actual->estado) {
                $facultades[] = $actual;
            }
        }

        $categorias = self::CATEGORIAS;
        $dedicaciones = self::DEDICACIONES;
        require __DIR__ . '/../vistas/paginas/administracion.php';
    }

    public function guardar(string $seccion): void
    {
        $this->verificarToken();
        $modelo = $seccion === 'facultades' ? Facultad::class : Docente::class;
        $idTexto = (string) ($_POST['id'] ?? '');
        $id = $idTexto === ''
            ? null
            : filter_var($idTexto, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $existente = $id ? $modelo::buscar($id) : null;
        $errores = [];

        if ($id === false || ($id !== null && !$existente)) {
            $errores[] = 'El registro no existe.';
        }

        if ($seccion === 'facultades') {
            $datos = [
                'nombre' => trim((string) ($_POST['nombre'] ?? '')),
                'abreviatura' => trim((string) ($_POST['abreviatura'] ?? '')),
            ];
            $this->validarTexto($datos['nombre'], 120, 'nombre', $errores);
            $this->validarTexto($datos['abreviatura'], 20, 'abreviatura', $errores);
        } else {
            $datos = [
                'id_facultad' => filter_var(
                    $_POST['id_facultad'] ?? '',
                    FILTER_VALIDATE_INT,
                    ['options' => ['min_range' => 1]]
                ),
                'dni' => trim((string) ($_POST['dni'] ?? '')),
                'apellidos' => trim((string) ($_POST['apellidos'] ?? '')),
                'nombres' => trim((string) ($_POST['nombres'] ?? '')),
                'correo' => trim((string) ($_POST['correo'] ?? '')),
                'categoria' => (string) ($_POST['categoria'] ?? ''),
                'dedicacion' => (string) ($_POST['dedicacion'] ?? ''),
            ];
            $facultad = $datos['id_facultad']
                ? Facultad::buscarCualquiera($datos['id_facultad'])
                : null;

            if (
                !$facultad
                || (!$facultad->estado && (!$existente || $existente->id_facultad !== $facultad->id))
            ) {
                $errores[] = 'Selecciona una facultad válida.';
            }

            if (!preg_match('/^[0-9]{8}$/', $datos['dni'])) {
                $errores[] = 'El DNI debe contener ocho dígitos.';
            }

            $this->validarTexto($datos['apellidos'], 100, 'apellidos', $errores);
            $this->validarTexto($datos['nombres'], 100, 'nombres', $errores);

            if (mb_strlen($datos['correo']) > 150 || !filter_var($datos['correo'], FILTER_VALIDATE_EMAIL)) {
                $errores[] = 'Ingresa un correo válido.';
            }

            if (!in_array($datos['categoria'], self::CATEGORIAS, true)) {
                $errores[] = 'Selecciona una categoría válida.';
            }

            if (!in_array($datos['dedicacion'], self::DEDICACIONES, true)) {
                $errores[] = 'Selecciona una dedicación válida.';
            }
        }

        if ($errores) {
            $datos['id'] = $idTexto;
            $this->mostrar($seccion, 'listar', $datos, $errores);
            return;
        }

        $modelo::guardar($datos, $id);
        $this->redirigir($seccion);
    }

    public function eliminar(string $seccion): void
    {
        $this->verificarToken();
        $modelo = $seccion === 'facultades' ? Facultad::class : Docente::class;
        $id = filter_input(
            INPUT_POST,
            'id',
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1]]
        );

        if (!$id || !$modelo::buscar($id)) {
            http_response_code(404);
            exit('Registro no encontrado');
        }

        $modelo::desactivar($id);
        $this->redirigir($seccion);
    }

    private function validarTexto(string $valor, int $maximo, string $campo, array &$errores): void
    {
        if ($valor === '' || mb_strlen($valor) > $maximo) {
            $errores[] = 'Ingresa ' . $campo . ' con un máximo de ' . $maximo . ' caracteres.';
        }
    }

    private function verificarToken(): void
    {
        if (
            !isset($_SESSION['token'], $_POST['token'])
            || !hash_equals($_SESSION['token'], (string) $_POST['token'])
        ) {
            http_response_code(403);
            exit('Solicitud inválida');
        }
    }

    private function redirigir(string $seccion): void
    {
        header('Location: index.php?seccion=' . $seccion);
        exit;
    }
}
