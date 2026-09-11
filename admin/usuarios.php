<?php
require_once __DIR__ . '/inc/layout.php';
requiereSesion();

$pdo = conexionBD();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $accion = $_POST['accion'] ?? '';

  if ($accion === 'guardar') {
    $id = (int)($_POST['id'] ?? 0);
    $usuario = trim($_POST['usuario'] ?? '');
    $contrasena = $_POST['contrasena'] ?? '';

    if ($usuario === '') {
      ponerMensaje('El nombre de usuario es obligatorio.', 'error');
      header('Location: /admin/usuarios.php?' . ($id ? "editar={$id}" : 'nuevo=1'));
      exit;
    }
    if (!$id && strlen($contrasena) < 8) {
      ponerMensaje('La contraseña debe tener al menos 8 caracteres.', 'error');
      header('Location: /admin/usuarios.php?nuevo=1');
      exit;
    }
    if ($contrasena !== '' && strlen($contrasena) < 8) {
      ponerMensaje('La nueva contraseña debe tener al menos 8 caracteres.', 'error');
      header('Location: /admin/usuarios.php?editar=' . $id);
      exit;
    }

    // Evitar nombres de usuario duplicados.
    $st = $pdo->prepare('SELECT id FROM usuarios_admin WHERE usuario = ? AND id <> ?');
    $st->execute([$usuario, $id]);
    if ($st->fetch()) {
      ponerMensaje('Ya existe otro usuario con ese nombre.', 'error');
      header('Location: /admin/usuarios.php?' . ($id ? "editar={$id}" : 'nuevo=1'));
      exit;
    }

    if ($id) {
      $nombreAnterior = usuarioPorId($id);
      if ($contrasena !== '') {
        $pdo->prepare('UPDATE usuarios_admin SET usuario=?, contrasena_hash=? WHERE id=?')
          ->execute([$usuario, password_hash($contrasena, PASSWORD_DEFAULT), $id]);
      } else {
        $pdo->prepare('UPDATE usuarios_admin SET usuario=? WHERE id=?')->execute([$usuario, $id]);
      }
      // Si editó su propio usuario, refresca el nombre mostrado en la sesión.
      if ($nombreAnterior !== null && $_SESSION['admin_usuario'] === $nombreAnterior) $_SESSION['admin_usuario'] = $usuario;
    } else {
      $pdo->prepare('INSERT INTO usuarios_admin (usuario, contrasena_hash) VALUES (?, ?)')
        ->execute([$usuario, password_hash($contrasena, PASSWORD_DEFAULT)]);
    }

    ponerMensaje('Usuario guardado correctamente.');
    header('Location: /admin/usuarios.php');
    exit;
  }

  if ($accion === 'eliminar') {
    $id = (int)($_POST['id'] ?? 0);
    $total = (int)$pdo->query('SELECT COUNT(*) FROM usuarios_admin')->fetchColumn();
    if ($total <= 1) {
      ponerMensaje('No puede eliminar el único usuario del panel.', 'error');
    } else {
      $pdo->prepare('DELETE FROM usuarios_admin WHERE id=?')->execute([$id]);
      ponerMensaje('Usuario eliminado.');
    }
    header('Location: /admin/usuarios.php');
    exit;
  }
}

function usuarioPorId(int $id): ?string {
  $st = conexionBD()->prepare('SELECT usuario FROM usuarios_admin WHERE id=?');
  $st->execute([$id]);
  $v = $st->fetchColumn();
  return $v === false ? null : $v;
}

/* ---------------- Vista ---------------- */
$editando = null;
if (isset($_GET['editar'])) {
  $st = $pdo->prepare('SELECT * FROM usuarios_admin WHERE id=?');
  $st->execute([(int)$_GET['editar']]);
  $editando = $st->fetch() ?: null;
}
$mostrarFormulario = $editando || isset($_GET['nuevo']);

panelAbrir($mostrarFormulario ? ($editando ? 'Editar usuario' : 'Nuevo usuario') : 'Usuarios', 'usuarios');

if ($mostrarFormulario): ?>
  <div class="panel__encabezado">
    <h1><?= $editando ? 'Editar usuario' : 'Nuevo usuario' ?></h1>
    <a class="btn btn--fantasma" href="/admin/usuarios.php"><?= icono('chevron') ?>Volver a la lista</a>
  </div>
  <div class="tarjeta" style="max-width:28rem">
    <form method="post">
      <input type="hidden" name="accion" value="guardar">
      <input type="hidden" name="id" value="<?= (int)($editando['id'] ?? 0) ?>">

      <div class="form__campo">
        <label for="usuario">Nombre de usuario *</label>
        <input id="usuario" name="usuario" type="text" required value="<?= esc($editando['usuario'] ?? '') ?>" autocomplete="off">
      </div>
      <div class="form__campo">
        <label for="contrasena"><?= $editando ? 'Nueva contraseña' : 'Contraseña *' ?></label>
        <div class="campo-contrasena">
          <input id="contrasena" name="contrasena" type="password" <?= $editando ? '' : 'required' ?> autocomplete="new-password" minlength="8">
          <button type="button" class="campo-contrasena__ojo" onclick="alternarContrasena(this)" tabindex="-1" aria-label="Mostrar contraseña"><?= icono('eye') ?></button>
        </div>
        <p class="form__ayuda"><?= $editando ? 'Deje este campo vacío para conservar la contraseña actual.' : 'Mínimo 8 caracteres.' ?></p>
      </div>

      <button class="btn btn--primario" type="submit"><?= icono('check') ?>Guardar usuario</button>
    </form>
  </div>
<?php else:
  $usuarios = $pdo->query('SELECT * FROM usuarios_admin ORDER BY creado_en ASC')->fetchAll();
  ?>
  <div class="panel__encabezado">
    <h1>Usuarios del panel</h1>
    <a class="btn btn--primario" href="/admin/usuarios.php?nuevo=1"><?= icono('check') ?>Agregar usuario</a>
  </div>
  <p style="color:#66625B;margin-top:-1rem">Personas que pueden entrar a este panel administrativo.</p>
  <div class="tarjeta">
    <div class="tabla-scroll">
    <table>
      <thead><tr><th>Usuario</th><th>Creado</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($usuarios as $u): ?>
        <tr>
          <td><strong><?= esc($u['usuario']) ?></strong><?= $u['usuario'] === usuarioActual() ? ' <span class="etiqueta etiqueta--si">Su sesión</span>' : '' ?></td>
          <td style="color:#77736B"><?= esc(date('d/m/Y', strtotime($u['creado_en']))) ?></td>
          <td class="tabla-acciones">
            <a class="btn btn--fantasma" href="/admin/usuarios.php?editar=<?= $u['id'] ?>">Editar</a>
            <?php if (count($usuarios) > 1): ?>
            <form method="post" onsubmit="return confirm('¿Eliminar el usuario «<?= esc(addslashes($u['usuario'])) ?>»? Ya no podrá iniciar sesión con esa cuenta.')" style="display:inline">
              <input type="hidden" name="accion" value="eliminar"><input type="hidden" name="id" value="<?= $u['id'] ?>">
              <button class="btn btn--peligro" type="submit">Eliminar</button>
            </form>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    </div>
  </div>
<?php endif;
panelCerrar();
