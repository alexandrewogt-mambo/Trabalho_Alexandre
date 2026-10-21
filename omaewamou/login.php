<?php
// Puxa o arquivo que conecta com o banco de dados (ele também inicia a sessão)
require 'conexao.php';

// Se a pessoa já está logada, não precisa ver a tela de login
if (isset($_SESSION['usuario'])) {
    header('Location: index.php');
    exit;
}

$erro = '';

// SE o usuário clicou em entrar (enviou o formulário):
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $senha   = $_POST['senha'] ?? '';

    // Procura o usuário no banco de dados
    $consulta = $pdo->prepare("SELECT id, usuario, senha FROM usuarios WHERE usuario = :usuario");
    $consulta->execute([':usuario' => $usuario]);
    $linha = $consulta->fetch();

    // Compara a senha digitada com o hash guardado no banco.
    // Se o usuário não existir, compara com um hash falso só para demorar o mesmo tempo.
    $hash = $linha['senha'] ?? '$2y$10$NvDhii9k4FmRV5MaCdEqDew2iikNr8NaJnt0Na9hjfyDPMCCsHHNe';
    if (password_verify($senha, $hash) && $linha) {
        // Troca o ID da sessão (evita que alguém reaproveite uma sessão antiga)
        session_regenerate_id(true);
        $_SESSION['usuario']    = $linha['usuario'];
        $_SESSION['usuario_id'] = (int)$linha['id'];
        header('Location: index.php');
        exit;
    }

    // Mensagem igual para usuário errado ou senha errada (não revela qual dos dois falhou)
    $erro = 'Usuário ou senha incorretos.';
}

// Puxa o topo do site (cabeçalho; sem menu porque ainda não entrou)
require 'topo.php';
?>

<h2><img class="icone" src="img/coracao.svg" alt="">Entrar</h2>

<!-- Mostra o aviso de erro caso o login tenha falhado -->
<?php if ($erro): ?>
  <p class="aviso"><?= e($erro) ?></p>
<?php endif; ?>

<!-- Mostra o aviso de sucesso (ex: conta criada) se ele existir -->
<?php if (isset($_GET['msg'])): ?>
  <p class="aviso"><?= e($_GET['msg']) ?></p>
<?php endif; ?>

<!-- Formulário de login -->
<form method="post" class="ficha">
  <label>Usuário <input name="usuario" value="<?= e($_POST['usuario'] ?? '') ?>" required maxlength="50" autofocus></label>
  <label>Senha <input name="senha" type="password" required></label>
  <button>Entrar</button>
</form>

<p>Ainda não tem conta? <a href="cadastro.php">Cadastre-se</a></p>

<?php 
// Puxa o rodapé do site
require 'rodape.php'; 
?>
