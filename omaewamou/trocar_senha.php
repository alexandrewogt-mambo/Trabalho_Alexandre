<?php
// Puxa o arquivo que conecta com o banco de dados e confere se está logado
require 'conexao.php';
require 'auth.php';

$msg  = '';
$erro = '';

// SE o usuário enviou o formulário de troca de senha:
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $atual     = $_POST['atual'] ?? '';
    $nova      = $_POST['nova'] ?? '';
    $confirmar = $_POST['confirmar'] ?? '';

    // Busca o hash da senha atual do usuário logado
    $consulta = $pdo->prepare("SELECT senha FROM usuarios WHERE id = :id");
    $consulta->execute([':id' => $_SESSION['usuario_id']]);
    $hashAtual = $consulta->fetchColumn();

    // Confere cada regra e mostra o primeiro erro encontrado
    if (!$hashAtual || !password_verify($atual, $hashAtual)) {
        $erro = 'A senha atual está incorreta.';
    } elseif (strlen($nova) < 6) {
        $erro = 'A nova senha precisa ter pelo menos 6 caracteres.';
    } elseif ($nova !== $confirmar) {
        $erro = 'A confirmação não é igual à nova senha.';
    } else {
        // Tudo certo: guarda o hash da nova senha no banco
        $consulta = $pdo->prepare("UPDATE usuarios SET senha = :senha WHERE id = :id");
        $consulta->execute([
            ':senha' => password_hash($nova, PASSWORD_DEFAULT),
            ':id'    => $_SESSION['usuario_id'],
        ]);
        $msg = 'Senha alterada com sucesso!';
    }
}

// Puxa o topo do site (cabeçalho, menu)
require 'topo.php';
?>

<h2><img class="icone" src="img/coracao.svg" alt="">Trocar senha</h2>

<!-- Mostra o aviso de sucesso ou de erro -->
<?php if ($msg): ?><p class="aviso"><?= e($msg) ?></p><?php endif; ?>
<?php if ($erro): ?><p class="aviso"><?= e($erro) ?></p><?php endif; ?>

<form method="post" class="ficha">
  <label>Senha atual <input name="atual" type="password" required></label>
  <label>Nova senha <input name="nova" type="password" required minlength="6"></label>
  <label>Repita a nova senha <input name="confirmar" type="password" required minlength="6"></label>
  <button>Salvar</button>
</form>

<?php 
// Puxa o rodapé do site
require 'rodape.php'; 
?>
