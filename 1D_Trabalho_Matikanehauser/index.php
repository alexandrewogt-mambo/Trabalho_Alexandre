<?php
// Puxa o arquivo que conecta com o banco de dados
require 'conexao.php';

// Pega o texto digitado na barra de busca (se não tiver nada, fica vazio)
$busca = $_GET['busca'] ?? '';

// Comando para buscar os livros e contar quantos estão emprestados no momento
$sql = "SELECT l.id, l.titulo, l.autor, l.ano, l.genero, l.quantidade,
               COUNT(e.id) AS emprestados
        FROM livros l
        LEFT JOIN emprestimos e
               ON e.livro_id = l.id AND e.data_devolucao IS NULL
        WHERE l.titulo LIKE :busca OR l.autor LIKE :busca2
        GROUP BY l.id, l.titulo, l.autor, l.ano, l.genero, l.quantidade
        ORDER BY l.titulo";

// Executa a busca no banco usando o termo digitado pelo usuário
$consulta = $pdo->prepare($sql);
$consulta->execute([':busca' => "%$busca%", ':busca2' => "%$busca%"]);
$livros = $consulta->fetchAll();

// Puxa o topo do site (cabeçalho, menu)
require 'topo.php';
?>

<!-- Formulário com a caixinha e o botão de buscar -->
<form method="get" class="busca">
  <input type="text" name="busca" value="<?= e($busca) ?>" placeholder="Buscar por título ou autor">
  <button>Buscar</button>
</form>

<!-- Mostra mensagens de sucesso ou erro (ex: livro excluído, editado) se existirem -->
<?php if (isset($_GET['msg'])): ?>
  <p class="aviso"><?= e($_GET['msg']) ?></p>
<?php endif; ?>

<!-- Tabela que lista os livros encontrados -->
<table>
  <tr><th>Título</th><th>Autor</th><th>Ano</th><th>Gênero</th><th>Disponíveis</th><th></th></tr>
  <?php foreach ($livros as $livro): ?>
    <tr>
      <td><?= e($livro['titulo']) ?></td>
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
</table>

<!-- Mostra na tela a quantidade total de linhas/livros listados -->
<p class="total"><?= count($livros) ?> livro(s) encontrado(s).</p>

<?php 
// Puxa o rodapé do site
require 'rodape.php'; 
?>
