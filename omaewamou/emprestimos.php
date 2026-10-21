<?php
// Puxa o arquivo que conecta com o banco de dados
require 'conexao.php';
// Só deixa passar quem fez login (senão vai para a tela de login)
require 'auth.php';

// SE o usuário enviou algum formulário (clicou em algum botão de ação):
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Se o botão clicado foi o de "Devolver":
    if (isset($_POST['devolver'])) {
        // Atualiza o banco colocando a data de hoje como a data de devolução
        $consulta = $pdo->prepare("UPDATE emprestimos SET data_devolucao = CURDATE() WHERE id = :id");
        $consulta->execute([':id' => (int)$_POST['devolver']]);
        $msg = 'Devolução registrada.';
      }
    // Se não for devolução, significa que é um novo empréstimo:
    else {
        $livroId = (int)$_POST['livro_id'];
        
        // Confere quantos exemplares desse livro ainda estão na prateleira
        // (quantidade total menos os que estão emprestados e ainda não foram devolvidos)
        $consulta = $pdo->prepare("SELECT l.quantidade - COUNT(e.id) AS disponiveis
                                   FROM livros l
                                   LEFT JOIN emprestimos e ON e.livro_id = l.id AND e.data_devolucao IS NULL
                                   WHERE l.id = :id
                                   GROUP BY l.id, l.quantidade");
        $consulta->execute([':id' => $livroId]);
        $disponiveis = $consulta->fetchColumn();   // devolve false se o livro não existir
        
        // Se o livro não existe, ou não tem nenhum exemplar disponível, NÃO registra o empréstimo
        if ($disponiveis === false) {
            $msg = 'Livro não encontrado.';
        } elseif ((int)$disponiveis <= 0) {
            $msg = 'Este livro está esgotado: todos os exemplares já estão emprestados.';
        } else {
            // Tem estoque: salva o novo empréstimo juntando o ID do livro, do aluno e a data de hoje
            $consulta = $pdo->prepare("INSERT INTO emprestimos (livro_id, aluno_id, data_emprestimo)
                                       VALUES (:livro, :aluno, CURDATE())");
            $consulta->execute([':livro' => $livroId, ':aluno' => (int)$_POST['aluno_id']]);
            $msg = 'Empréstimo registrado.';
        }
    }
    
    // Atualiza a página atualizando a lista e mostrando a mensagem de sucesso
    header('Location: emprestimos.php?msg=' . urlencode($msg));
    exit;
}

// Busca a lista de empréstimos juntando os nomes dos alunos e os títulos dos livros
$sql = "SELECT e.id, e.aluno_id, a.nome, a.turma, l.titulo, e.data_emprestimo, e.data_devolucao
        FROM emprestimos e
        JOIN alunos a ON a.id = e.aluno_id
        JOIN livros l ON l.id = e.livro_id
        ORDER BY e.data_devolucao IS NOT NULL, e.data_emprestimo DESC";
$emprestimos = $pdo->query($sql)->fetchAll();

// Busca todos os alunos para colocar na caixinha de seleção (select)
$alunos = $pdo->query("SELECT id, nome, turma FROM alunos ORDER BY nome")->fetchAll();

// Busca todos os livros para a caixinha de seleção, já com a quantidade disponível de cada um
$livros = $pdo->query("SELECT l.id, l.titulo, l.quantidade - COUNT(e.id) AS disponiveis
                       FROM livros l
                       LEFT JOIN emprestimos e ON e.livro_id = l.id AND e.data_devolucao IS NULL
                       GROUP BY l.id, l.titulo, l.quantidade
                       ORDER BY l.titulo")->fetchAll();

// Puxa o topo do site (cabeçalho, menu)
require 'topo.php';
?>

<h2><img class="icone" src="img/coracao.svg" alt="">Novo empréstimo</h2>
<!-- Mostra o aviso de sucesso na tela caso ele exista -->
<?php if (isset($_GET['msg'])): ?>
  <p class="aviso"><?= e($_GET['msg']) ?></p>
<?php endif; ?>

<!-- Formulário para criar um novo empréstimo -->
<form method="post" class="linha">
  <!-- Caixinha para escolher o aluno -->
  <select name="aluno_id" required>
    <option value="">Aluno…</option>
    <?php foreach ($alunos as $a): ?>
      <option value="<?= $a['id'] ?>"><?= e($a['nome']) ?> (<?= e($a['turma']) ?>)</option>
    <?php endforeach; ?>
  </select>
  
  <!-- Caixinha para escolher o livro (livros esgotados aparecem desativados) -->
  <select name="livro_id" required>
    <option value="">Livro…</option>
    <?php foreach ($livros as $l): ?>
      <?php if ($l['disponiveis'] > 0): ?>
        <option value="<?= $l['id'] ?>"><?= e($l['titulo']) ?> (<?= $l['disponiveis'] ?> disponível)</option>
      <?php else: ?>
        <option value="<?= $l['id'] ?>" disabled><?= e($l['titulo']) ?> (esgotado)</option>
      <?php endif; ?>
    <?php endforeach; ?>
  </select>
  <button>Emprestar</button>
</form>

<h2><img class="icone" src="img/estrela.svg" alt="">Empréstimos</h2>
<!-- Tabela que lista todos os empréstimos cadastrados -->
<table>
  <tr><th>Aluno</th><th>Turma</th><th>Livro</th><th>Retirado em</th><th>Situação</th></tr>
  <?php foreach ($emprestimos as $emp): ?>
    <tr>
      <!-- O nome do aluno é um link para o histórico dele -->
      <td><img class="icone" src="img/coracao.svg" alt=""><a href="aluno.php?id=<?= $emp['aluno_id'] ?>"><?= e($emp['nome']) ?></a></td>
      <td><?= e($emp['turma']) ?></td>
      <td><?= e($emp['titulo']) ?></td>
      <!-- Mostra a data do empréstimo formatada no padrão brasileiro (dia/mês/ano) -->
      <td><?= date('d/m/Y', strtotime($emp['data_emprestimo'])) ?></td>
      <td>
        <!-- Se o livro ainda não foi devolvido, mostra o botão para devolver -->
        <?php if ($emp['data_devolucao'] === null): ?>
          <form method="post">
            <button name="devolver" value="<?= $emp['id'] ?>">Registrar devolução</button>
          </form>
        <!-- Se já foi devolvido, mostra apenas o texto com a data da devolução -->
        <?php else: ?>
          Devolvido em <?= date('d/m/Y', strtotime($emp['data_devolucao'])) ?>
        <?php endif; ?>
      </td>
    </tr>
  <?php endforeach; ?>
</table>

<?php 
// Puxa o rodapé do site
require 'rodape.php'; 
?>
