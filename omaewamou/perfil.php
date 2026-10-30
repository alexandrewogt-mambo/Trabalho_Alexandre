<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require 'conexao.php';
require 'auth.php';

$id = $_SESSION['usuario_id'];
$erro = '';
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $bio  = trim($_POST['bio'] ?? '');
    $tema = $_POST['tema'] ?? 'lolicore';

    $foto = null;

    if (!empty($_FILES['foto']['name'])) {
        $tipos = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp'
        ];

        $info = @getimagesize($_FILES['foto']['tmp_name']);

        if (!$info || !isset($tipos[$info['mime']])) {
            $erro = 'A foto precisa ser JPG, PNG ou WEBP.';
        } elseif ($_FILES['foto']['size'] > 2 * 1024 * 1024) {
            $erro = 'A foto pode ter no máximo 2 MB.';
        } else {
            if (!is_dir(__DIR__ . '/perfis')) {
                mkdir(__DIR__ . '/perfis', 0755, true);
            }

            foreach (glob(__DIR__ . "/perfis/$id.*") ?: [] as $arquivo) {
                unlink($arquivo);
            }

            $nomeArquivo = $id . '.' . $tipos[$info['mime']];
            move_uploaded_file($_FILES['foto']['tmp_name'], __DIR__ . '/perfis/' . $nomeArquivo);

            $foto = 'perfis/' . $nomeArquivo;
        }
    }

    if (!$erro) {
        $sql = "UPDATE usuarios
                SET nome = :nome,
                    bio = :bio,
                    tema = :tema" .
                ($foto ? ", foto = :foto" : "") . "
                WHERE id = :id";

        $consulta = $pdo->prepare($sql);

        $dados = [
            ':nome' => $nome,
            ':bio'  => $bio,
            ':tema' => $tema,
            ':id'   => $id
        ];

        if ($foto) {
            $dados[':foto'] = $foto;
        }

        $consulta->execute($dados);

        $msg = 'Perfil atualizado com sucesso!';
    }
}

$consulta = $pdo->prepare("SELECT * FROM usuarios WHERE id = :id");
$consulta->execute([':id' => $id]);
$usuario = $consulta->fetch();

require 'topo.php';
?>

<h2>💗 Meu perfil</h2>

<?php if ($msg): ?>
  <p class="aviso"><?= e($msg) ?></p>
<?php endif; ?>

<?php if ($erro): ?>
  <p class="aviso"><?= e($erro) ?></p>
<?php endif; ?>

<form method="post" enctype="multipart/form-data" class="ficha">
  <label>Nome
    <input name="nome" value="<?= e($usuario['nome'] ?? '') ?>" maxlength="100">
  </label>

  <label>Bio
    <textarea name="bio" maxlength="255"><?= e($usuario['bio'] ?? '') ?></textarea>
  </label>

  <label>Tema
    <select name="tema">
      <option value="lolicore" <?= ($usuario['tema'] ?? '') === 'lolicore' ? 'selected' : '' ?>>Lolicore</option>
      <option value="claro" <?= ($usuario['tema'] ?? '') === 'claro' ? 'selected' : '' ?>>Claro</option>
      <option value="escuro" <?= ($usuario['tema'] ?? '') === 'escuro' ? 'selected' : '' ?>>Escuro</option>
    </select>
  </label>

  <label>Foto de perfil
    <input type="file" name="foto" accept="image/jpeg,image/png,image/webp">
  </label>

  <?php if (!empty($usuario['foto'])): ?>
    <img src="<?= e($usuario['foto']) ?>" alt="Foto de perfil" class="foto-perfil">
  <?php endif; ?>

  <button>Salvar perfil</button>
</form>

<?php require 'rodape.php'; ?>