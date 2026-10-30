<?php
require 'conexao.php';
require 'auth.php';

// Total de livros cadastrados
$totalLivros = (int)$pdo->query("SELECT COUNT(*) FROM livros")->fetchColumn();

// Total de alunos cadastrados
$totalAlunos = (int)$pdo->query("SELECT COUNT(*) FROM alunos")->fetchColumn();

// Empréstimos ainda não devolvidos
$emprestados = (int)$pdo->query("SELECT COUNT(*) FROM emprestimos WHERE data_devolucao IS NULL")->fetchColumn();

// Empréstimos já devolvidos
$devolvidos = (int)$pdo->query("SELECT COUNT(*) FROM emprestimos WHERE data_devolucao IS NOT NULL")->fetchColumn();

// Empréstimos em aberto há mais de 7 dias = atrasados
$atrasados = (int)$pdo->query("SELECT COUNT(*) FROM emprestimos WHERE data_devolucao IS NULL AND data_emprestimo < DATE_SUB(CURDATE(), INTERVAL 7 DAY)")->fetchColumn();

// Exemplares disponíveis: quantidade total menos empréstimos em aberto
$disponiveis = (int)$pdo->query("SELECT COALESCE(SUM(l.quantidade), 0) - (
    SELECT COUNT(*) FROM emprestimos WHERE data_devolucao IS NULL
) FROM livros l")->fetchColumn();

// Maior valor, para calcular o tamanho das barras
$maior = max($totalLivros, $totalAlunos, $emprestados, $devolvidos, $atrasados, $disponiveis, 1);

// Função para calcular a porcentagem da barra
function largura($valor, $maior) {
    return round(($valor / $maior) * 100);
}

require 'topo.php';
?>

<h2>📊 Gráficos da biblioteca</h2>

<p class="resumo">
  Empréstimos em aberto há mais de 7 dias são considerados <b>atrasados</b>.
</p>

<div class="graficos">
  <div class="grafico">
    <div class="grafico-info">
      <span>📚 Livros cadastrados</span>
      <b><?= $totalLivros ?></b>
    </div>
    <div class="barra">
      <div class="barra-preenchida barra-livros" style="width: <?= largura($totalLivros, $maior) ?>%"></div>
    </div>
  </div>

  <div class="grafico">
    <div class="grafico-info">
      <span>💗 Alunos cadastrados</span>
      <b><?= $totalAlunos ?></b>
    </div>
    <div class="barra">
      <div class="barra-preenchida barra-alunos" style="width: <?= largura($totalAlunos, $maior) ?>%"></div>
    </div>
  </div>

  <div class="grafico">
    <div class="grafico-info">
      <span>📖 Empréstimos em aberto</span>
      <b><?= $emprestados ?></b>
    </div>
    <div class="barra">
      <div class="barra-preenchida barra-emprestados" style="width: <?= largura($emprestados, $maior) ?>%"></div>
    </div>
  </div>

  <div class="grafico">
    <div class="grafico-info">
      <span>✅ Empréstimos devolvidos</span>
      <b><?= $devolvidos ?></b>
    </div>
    <div class="barra">
      <div class="barra-preenchida barra-devolvidos" style="width: <?= largura($devolvidos, $maior) ?>%"></div>
    </div>
  </div>

  <div class="grafico">
    <div class="grafico-info">
      <span>⚠️ Empréstimos atrasados</span>
      <b><?= $atrasados ?></b>
    </div>
    <div class="barra">
      <div class="barra-preenchida barra-atrasados" style="width: <?= largura($atrasados, $maior) ?>%"></div>
    </div>
  </div>

  <div class="grafico">
    <div class="grafico-info">
      <span>📦 Exemplares disponíveis</span>
      <b><?= max(0, $disponiveis) ?></b>
    </div>
    <div class="barra">
      <div class="barra-preenchida barra-disponiveis" style="width: <?= largura(max(0, $disponiveis), $maior) ?>%"></div>
    </div>
  </div>
</div>

<?php require 'rodape.php'; ?>