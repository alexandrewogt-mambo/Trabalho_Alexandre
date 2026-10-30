<!DOCTYPE html>
<!-- Configura o idioma do site para português do Brasil -->
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <!-- Faz o site se ajustar ao tamanho da tela do celular -->
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Título que aparece na aba do navegador -->
  <title>Biblioteca da Escola</title>

  <?php
  // Define o tema do usuário; se não tiver, usa o lolicore
$tema = 'claro';

if (isset($_SESSION['usuario_id'])) {
    $consultaTema = $pdo->prepare("SELECT tema FROM usuarios WHERE id = :id");
    $consultaTema->execute([':id' => $_SESSION['usuario_id']]);
    $tema = $consultaTema->fetchColumn() ?: 'claro';
}

  // Busca o nome personalizado do perfil
  $nomeExibido = $_SESSION['usuario'] ?? '';

  if (isset($_SESSION['usuario_id'])) {
      $consultaNome = $pdo->prepare("SELECT nome FROM usuarios WHERE id = :id");
      $consultaNome->execute([':id' => $_SESSION['usuario_id']]);
      $nomeSalvo = $consultaNome->fetchColumn();

      if (!empty($nomeSalvo)) {
          $nomeExibido = $nomeSalvo;
      }
  }

  // Busca a foto do usuário logado
  $fotoUsuario = null;

  if (isset($_SESSION['usuario_id'])) {
      $consultaFoto = $pdo->prepare("SELECT foto FROM usuarios WHERE id = :id");
      $consultaFoto->execute([':id' => $_SESSION['usuario_id']]);
      $fotoUsuario = $consultaFoto->fetchColumn();
  }
  ?>

  <!-- Puxa o arquivo de estilos do site -->
  <link rel="stylesheet" href="estilo.css">
  <link rel="stylesheet" href="tema-<?= e($tema) ?>.css">
</head>

<body>

<header>
  <!-- Logotipo ou título principal no topo da página -->
  <h1><img class="logo" src="img/livro.svg" alt="">Biblioteca da Escola</h1>

  <!-- Menu de navegação; só aparece para quem está logado -->
  <?php if (isset($_SESSION['usuario'])): ?>
  <nav>
    <a href="index.php">Livros</a>
    <a href="novo.php">Novo livro</a>
    <a href="emprestimos.php">Empréstimos</a>
    <a href="alunos.php">Alunos</a>
    <a href="graficos.php">Gráficos</a>
    <a href="perfil.php">Meu perfil</a>
    <a href="trocar_senha.php">Trocar senha</a>

    <!-- Botão para alternar entre tema escuro e lolicore -->
    <form method="post" action="tema.php" class="botao-tema">
  <button name="tema" value="<?= $tema === 'escuro' ? 'claro' : 'escuro' ?>">
    <?= $tema === 'escuro' ? '☀️ Tema claro' : '🌙 Tema escuro' ?>
  </button>
</form>

    <!-- Foto de perfil -->
    <?php if (!empty($fotoUsuario)): ?>
      <a href="perfil.php" class="foto-menu">
        <img src="<?= e($fotoUsuario) ?>" alt="Foto de perfil">
      </a>
    <?php else: ?>
      <a href="perfil.php" class="foto-menu foto-vazia">
        <?= e(mb_substr($nomeExibido, 0, 1)) ?>
      </a>
    <?php endif; ?>

    <!-- Nome do usuário -->
    <span class="usuario">Olá, <?= htmlspecialchars($nomeExibido, ENT_QUOTES, 'UTF-8') ?></span>

    <a href="logout.php">Sair</a>
  </nav>
  <?php endif; ?>
</header>

<!-- Banner decorativo logo abaixo do cabeçalho -->
<img class="banner" src="img/banner-<?= e($tema) ?>.svg" alt="Banner da biblioteca" style="margin-top:10px">

<!-- Abre a área onde o conteúdo principal de cada página vai aparecer -->
<main>