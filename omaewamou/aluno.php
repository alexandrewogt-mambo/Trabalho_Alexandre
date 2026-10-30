<?php
// Puxa o arquivo que conecta com o banco de dados
require 'conexao.php';
// Só deixa passar quem fez login (senão vai para a tela de login)
require 'auth.php';

// Pega o ID do aluno enviado pela página (garante que seja um número)
$id = (int)($_GET['id'] ?? 0);

// Busca os dados do aluno no banco de dados
$consulta = $pdo->prepare("SELECT * FROM alunos WHERE id = :id");
$consulta->execute([':id' => $id]);
$aluno = $consulta->fetch();

// Se o aluno não existir, para o código e mostra um aviso
if (!$aluno) {
    die('Aluno não encontrado.');
}

// Busca todos os empréstimos desse aluno (os que ainda não foram devolvidos aparecem primeiro)
$consulta = $pdo->prepare("SELECT l.titulo, l.autor, e.data_emprestimo, e.data_devolucao
                           FROM emprestimos e
                           JOIN livros l ON l.id = e.livro_id
                           WHERE e.aluno_id = :id
                           ORDER BY e.data_devolucao IS NOT NULL, e.data_emprestimo DESC");
$consulta->execute([':id' => $id]);
$historico = $consulta->fetchAll();

// Conta quantos livros ainda estão com o aluno (sem data de devolução)
$abertos = 0;
foreach ($historico as $h) {
    if ($h['data_devolucao'] === null) { $abertos++; }
}

// Puxa o topo do site (cabeçalho, menu)
require 'topo.php';
?>

<h2><img class="icone" src="img/coracao.svg" alt="">Histórico de <?= e($aluno['nome']) ?></h2>

<!-- Resumo com a turma e os totais do aluno -->
<p class="resumo">
  Turma: <b><?= e($aluno['turma']) ?></b> ·
  Livros já pegos: <b><?= count($historico) ?></b> ·
  Com ele agora: <b><?= $abertos ?></b>
</p>

<!-- Tabela com todos os livros que o aluno já pegou -->
<table>
  <tr><th>Livro</th><th>Autor</th><th>Retirado em</th><th>Situação</th></tr>
  <?php foreach ($historico as $h): ?>
    <tr>
      <td><img class="icone" src="img/livro.svg" alt=""><?= e($h['titulo']) ?></td>
      <td><?= e($h['autor']) ?></td>
      <td><?= date('d/m/Y', strtotime($h['data_emprestimo'])) ?></td>
      <td>
        <!-- Se ainda não devolveu, mostra "Em aberto"; se devolveu, mostra a data -->
        <?php if ($h['data_devolucao'] === null): ?>
          <b>Em aberto</b>
        <?php else: ?>
          Devolvido em <?= date('d/m/Y', strtotime($h['data_devolucao'])) ?>
        <?php endif; ?>
      </td>
    </tr>
  <?php endforeach; ?>
  <!-- Se o aluno nunca pegou nada, avisa na tabela -->
  <?php if (!$historico): ?>
    <tr><td colspan="4">Este aluno ainda não pegou nenhum livro.</td></tr>
  <?php endif; ?>
</table>

<p><a href="alunos.php">&laquo; Voltar para a lista de alunos</a></p>

<?php 
// Puxa o rodapé do site
require 'rodape.php'; 
?>
