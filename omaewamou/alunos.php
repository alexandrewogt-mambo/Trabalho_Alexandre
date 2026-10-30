<?php
// Puxa o arquivo que conecta com o banco de dados
require 'conexao.php';
// Só deixa passar quem fez login (senão vai para a tela de login)
require 'auth.php';

// Busca todos os alunos, contando quantos livros cada um já pegou e quantos estão com ele agora
$sql = "SELECT a.id, a.nome, a.turma,
               COUNT(e.id) AS total,
               COUNT(CASE WHEN e.data_devolucao IS NULL THEN e.id END) AS abertos
        FROM alunos a
        LEFT JOIN emprestimos e ON e.aluno_id = a.id
        GROUP BY a.id, a.nome, a.turma
        ORDER BY a.nome";
$alunos = $pdo->query($sql)->fetchAll();

// Puxa o topo do site (cabeçalho, menu)
require 'topo.php';
?>

<h2><img class="icone" src="img/coracao.svg" alt="">Alunos</h2>

<!-- Tabela com todos os alunos e um link para o histórico de cada um -->
<table>
  <tr><th>Nome</th><th>Turma</th><th>Livros já pegos</th><th>Com ele agora</th><th></th></tr>
  <?php foreach ($alunos as $a): ?>
    <tr>
      <td><?= e($a['nome']) ?></td>
      <td><?= e($a['turma']) ?></td>
      <td><?= $a['total'] ?></td>
      <td><?= $a['abertos'] ?></td>
      <td><a href="aluno.php?id=<?= $a['id'] ?>">Ver histórico</a></td>
    </tr>
  <?php endforeach; ?>
  <!-- Se não houver nenhum aluno cadastrado, avisa na tabela -->
  <?php if (!$alunos): ?>
    <tr><td colspan="5">Nenhum aluno cadastrado.</td></tr>
  <?php endif; ?>
</table>

<p class="total"><?= count($alunos) ?> aluno(s).</p>

<?php 
// Puxa o rodapé do site
require 'rodape.php'; 
?>
