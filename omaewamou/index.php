<?php
// Puxa o arquivo que conecta com o banco de dados
require 'conexao.php';
// Só deixa passar quem fez login (senão vai para a tela de login)
require 'auth.php';
// Funções para cuidar das capas dos livros
require 'capas.php';

// Pega o texto digitado na barra de busca e o gênero escolhido (se não tiver nada, fica vazio)
$busca  = $_GET['busca'] ?? '';
$genero = $_GET['genero'] ?? '';

// Quantos livros aparecem por página e qual é a página atual (mínimo 1)
$porPagina = 10;
$pagina    = max(1, (int)($_GET['pagina'] ?? 1));

// Busca os gêneros que existem no banco para montar a caixinha de filtro
$generos = $pdo->query("SELECT DISTINCT genero FROM livros
                        WHERE genero IS NOT NULL AND genero <> ''
                        ORDER BY genero")->fetchAll(PDO::FETCH_COLUMN);

// Monta a condição do filtro: busca por título/autor e, se escolheu, também o gênero
$where  = "(l.titulo LIKE :busca OR l.autor LIKE :busca2)";
$params = [':busca' => "%$busca%", ':busca2' => "%$busca%"];
if ($genero !== '') {
    $where .= " AND l.genero = :genero";
    $params[':genero'] = $genero;
}

// Conta quantos livros existem com esse filtro (para saber quantas páginas vai ter)
$consulta = $pdo->prepare("SELECT COUNT(*) FROM livros l WHERE $where");
$consulta->execute($params);
$totalLivros  = (int)$consulta->fetchColumn();
$totalPaginas = max(1, (int)ceil($totalLivros / $porPagina));

// Se pediram uma página que não existe, volta para a última
if ($pagina > $totalPaginas) { $pagina = $totalPaginas; }

// Calcula a partir de qual livro começar (página 1 começa no 0, página 2 no 10...)
$inicio = ($pagina - 1) * $porPagina;

// Comando atualizado para trazer a capa diretamente da tabela
$sql = "SELECT l.id, l.titulo, l.autor, l.ano, l.genero, l.quantidade, l.capa,
               COUNT(e.id) AS emprestados
        FROM livros l
        LEFT JOIN emprestimos e
               ON e.livro_id = l.id AND e.data_devolucao IS NULL
        WHERE $where
        GROUP BY l.id, l.titulo, l.autor, l.ano, l.genero, l.quantidade, l.capa
        ORDER BY l.titulo
        LIMIT $porPagina OFFSET $inicio";

// Executa a busca no banco usando os filtros escolhidos pelo usuário
$consulta = $pdo->prepare($sql);
$consulta->execute($params);
$livros = $consulta->fetchAll();

// Função que monta o endereço de uma página, mantendo a busca e o gênero escolhidos
function link_pagina($n, $busca, $genero) {
    return 'index.php?' . http_build_query(['busca' => $busca, 'genero' => $genero, 'pagina' => $n]);
}

// Puxa o topo do site (cabeçalho, menu)
require 'topo.php';
?>

<!-- Formulário com a caixinha de busca, o filtro de gênero e o botão de buscar -->
<form method="get" class="busca">
  <input type="text" name="busca" value="<?= e($busca) ?>" placeholder="Buscar por título ou autor">
  <select name="genero">
    <option value="">Todos os gêneros</option>
    <?php foreach ($generos as $g): ?>
      <option value="<?= e($g) ?>" <?= $g === $genero ? 'selected' : '' ?>><?= e($g) ?></option>
    <?php endforeach; ?>
  </select>
  <button>Buscar</button>
  <!-- Só mostra o link de limpar se tiver algum filtro ativo -->
  <?php if ($busca !== '' || $genero !== ''): ?>
    <a href="index.php">Limpar filtros</a>
  <?php endif; ?>
</form>

<!-- Mostra mensagens de sucesso ou erro (ex: livro excluído, editado) se existirem -->
<?php if (isset($_GET['msg'])): ?>
  <p class="aviso"><?= e($_GET['msg']) ?></p>
<?php endif; ?>

<!-- Tabela que lista os livros encontrados -->
<table>
  <tr><th>Capa</th><th>Título</th><th>Autor</th><th>Ano</th><th>Gênero</th><th>Disponíveis</th><th></th></tr>
  <?php foreach ($livros as $livro): ?>
    <tr>
            <!-- Lendo o caminho salvo no banco de dados e usando img/sem-capa.svg se estiver vazio -->
            <td>
        <img class="capa" src="<?= !empty($livro['capa']) ? e($livro['capa']) : 'img/sem-capa.svg' ?>" alt="Capa de <?= e($livro['titulo']) ?>" style="width: 50px; height: auto;">
      </td>
      <td><img class="icone" src="img/livro.svg" alt=""><?= e($livro['titulo']) ?></td>
      <td><?= e($livro['autor']) ?></td>
      <td><?= e($livro['ano']) ?></td>
      <td><?= e($livro['genero']) ?></td>
      <!-- Faz a conta de quantos livros restam na prateleira (Total menos Emprestados) -->
      <td><?= $livro['quantidade'] - $livro['emprestados'] ?> de <?= $livro['quantidade'] ?></td>
      
      <!-- Botões de ação para cada livro -->
      <td class="acoes">
        <!-- Link que leva para a tela de editar enviando o ID do livro -->
        <a href="editar.php?id=<?= $livro['id'] ?>">Editar</a>
        
        <!-- Formulário para excluir o livro (pede confirmação na tela antes de ir) -->
        <form method="post" action="excluir.php" onsubmit="return confirm('Excluir este livro?')">
          <input type="hidden" name="id" value="<?= $livro['id'] ?>">
          <button class="perigo">Excluir</button>
        </form>
      </td>
    </tr>
  <?php endforeach; ?>
  <!-- Se a busca não achou nenhum livro, avisa em vez de mostrar a tabela vazia -->
  <?php if (!$livros): ?>
    <tr><td colspan="7">Nenhum livro encontrado.</td></tr>
  <?php endif; ?>
</table>

<!-- Mostra na tela a quantidade total de livros encontrados (de todas as páginas) -->
<p class="total"><?= $totalLivros ?> livro(s) encontrado(s).</p>

<!-- Botões de paginação: só aparecem se tiver mais de uma página -->
<?php if ($totalPaginas > 1): ?>
  <div class="paginacao">
    <!-- Botão "Anterior": fica apagado na primeira página -->
    <?php if ($pagina > 1): ?>
      <a href="<?= e(link_pagina($pagina - 1, $busca, $genero)) ?>">&laquo; Anterior</a>
    <?php else: ?>
      <span class="off">&laquo; Anterior</span>
    <?php endif; ?>

    <!-- Um botão para cada página; a atual fica destacada -->
    <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
      <?php if ($i === $pagina): ?>
        <span class="atual"><?= $i ?></span>
      <?php else: ?>
        <a href="<?= e(link_pagina($i, $busca, $genero)) ?>"><?= $i ?></a>
      <?php endif; ?>
    <?php endfor; ?>

    <!-- Botão "Próxima": fica apagado na última página -->
    <?php if ($pagina < $totalPaginas): ?>
      <a href="<?= e(link_pagina($pagina + 1, $busca, $genero)) ?>">Próxima &raquo;</a>
    <?php else: ?>
      <span class="off">Próxima &raquo;</span>
    <?php endif; ?>
  </div>
<?php endif; ?>

<?php 
// Puxa o rodapé do site
require 'rodape.php'; 
?>
