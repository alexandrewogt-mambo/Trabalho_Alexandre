<!DOCTYPE html>
<!-- Configura o idioma do site para português do Brasil -->
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <!-- Faz o site se ajustar ao tamanho da tela do celular -->
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Título que aparece na aba do navegador -->
  <title>Biblioteca da Escola</title>
  <!-- Puxa o arquivo de estilos (CSS) para dar o visual do site -->
  <link rel="stylesheet" href="estilo.css">
</head>
<body>

<header>
  <!-- Logotipo ou título principal no topo da página -->
  <h1><img class="logo" src="img/livro.svg" alt="">Biblioteca da Escola</h1>
  <!-- Menu de navegação para clicar e mudar de página -->
  <!-- O menu só aparece para quem está logado -->
  <?php if (isset($_SESSION['usuario'])): ?>
  <nav>
    <a href="index.php">Livros</a>
    <a href="novo.php">Novo livro</a>
    <a href="emprestimos.php">Empréstimos</a>
    <a href="alunos.php">Alunos</a>
    <a href="trocar_senha.php">Trocar senha</a>
    <span class="usuario">Olá, <?= htmlspecialchars($_SESSION['usuario'], ENT_QUOTES, 'UTF-8') ?></span>
    <a href="logout.php">Sair</a>
  </nav>
  <?php endif; ?>
</header>

<!-- Banner decorativo logo abaixo do cabeçalho -->
<img class="banner" src="img/banner.svg" alt="" style="margin-top:10px">

<!-- Abre a área onde o conteúdo principal de cada página vai aparecer -->
<main>
