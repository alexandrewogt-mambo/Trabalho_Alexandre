<?php
// Puxa o arquivo que conecta com o banco de dados (ele também inicia a sessão)
require 'conexao.php';

// Se a pessoa já está logada, não precisa criar conta
if (isset($_SESSION['usuario'])) {
    header('Location: index.php');
    exit;
}

$erro = '';

// SE o usuário clicou em cadastrar (enviou o formulário):
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario   = trim($_POST['usuario'] ?? '');
    $senha     = $_POST['senha'] ?? '';
    $confirmar = $_POST['confirmar'] ?? '';

    // Confere cada regra e guarda o primeiro erro encontrado
    if (!preg_match('/^[A-Za-z0-9_.]{3,50}$/', $usuario)) {
        $erro = 'O usuário precisa ter de 3 a 50 caracteres, só com letras, números, _ ou ponto.';
    } elseif (strlen($senha) < 6) {
        $erro = 'A senha precisa ter pelo menos 6 caracteres.';
    } elseif ($senha !== $confirmar) {
        $erro = 'A confirmação não é igual à senha.';
    } else {
        // Confere se já existe alguém com esse nome de usuário
        $consulta = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE usuario = :usuario");
        $consulta->execute([':usuario' => $usuario]);
        
        if ($consulta->fetchColumn() > 0) {
            $erro = 'Esse usuário já existe. Escolha outro nome.';
        } else {
            // Tudo certo: guarda o usuário com o hash da senha (nunca a senha de verdade)
            $consulta = $pdo->prepare("INSERT INTO usuarios (usuario, senha) VALUES (:usuario, :senha)");
            $consulta->execute([
                ':usuario' => $usuario,
                ':senha'   => password_hash($senha, PASSWORD_DEFAULT),
            ]);
            
            // Manda para a tela de login com um aviso de sucesso
            header('Location: login.php?msg=' . urlencode('Conta criada! Agora é só entrar.'));
            exit;
        }
    }
}

// Puxa o topo do site (cabeçalho; sem menu porque ainda não entrou)
require 'topo.php';
?>

<h2><img class="icone" src="img/coracao.svg" alt="">Criar conta</h2>

<!-- Mostra o aviso de erro caso o cadastro tenha falhado -->
<?php if ($erro): ?>
  <p class="aviso"><?= e($erro) ?></p>
<?php endif; ?>

<!-- Formulário de cadastro -->
<form method="post" class="ficha">
  <label>Usuário <input name="usuario" value="<?= e($_POST['usuario'] ?? '') ?>" required minlength="3" maxlength="50" autofocus></label>
  <label>Senha <input name="senha" type="password" required minlength="6"></label>
  <label>Repita a senha <input name="confirmar" type="password" required minlength="6"></label>
  <button>Cadastrar</button>
</form>

<p>Já tem conta? <a href="login.php">Entrar</a></p>

<?php 
// Puxa o rodapé do site
require 'rodape.php'; 
?>
