<?php
// Puxa o arquivo que conecta com o banco de dados
require 'conexao.php';
// Só deixa passar quem fez login (senão vai para a tela de login)
require 'auth.php';
// Funções para cuidar das capas dos livros
require 'capas.php';

// Pega o ID do livro enviado pela página (garante que seja um número)
$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);

// SE o usuário clicou em salvar (enviou o formulário):
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Prepara o comando para atualizar os dados do livro no banco
    $sql = "UPDATE livros
            SET titulo = :titulo, autor = :autor, ano = :ano, genero = :genero, quantidade = :quantidade
            WHERE id = :id";                  
    $consulta = $pdo->prepare($sql);
    
    // Troca as marcações pelos novos dados preenchidos no formulário e envia
    $consulta->execute([
        ':titulo'     => $_POST['titulo'],
        ':autor'      => $_POST['autor'],
        ':ano'        => $_POST['ano'] !== '' ? (int)$_POST['ano'] : null,
        ':genero'     => $_POST['genero'],
        ':quantidade' => (int)$_POST['quantidade'],
        ':id'         => $id,
    ]);
    
    // Se marcou a caixinha "remover capa", apaga a capa atual
    if (isset($_POST['remover_capa'])) {
        remover_capa($id);
    }
    
    // Se escolheu uma imagem nova, ela substitui a capa atual
    $erroCapa = salvar_capa($id, $_FILES['capa'] ?? null);
    
    // Redireciona o usuário de volta para a lista de livros com um aviso (de sucesso ou de problema com a capa)
    $msg = $erroCapa ? 'Livro atualizado, mas a capa não foi salva: ' . $erroCapa : 'Livro atualizado!';
    header('Location: index.php?msg=' . urlencode($msg));
    exit;
}

// Busca as informações atuais desse livro no banco de dados para poder mostrar na tela
$consulta = $pdo->prepare("SELECT * FROM livros WHERE id = :id");
$consulta->execute([':id' => $id]);
$livro = $consulta->fetch();

// Se o livro não existir no banco, para o código e mostra um aviso
if (!$livro) {
    die('Livro não encontrado.');
}

// Puxa o topo visual do site (menu, cabeçalho)
require 'topo.php';
?>

<!-- Formulário com os campos preenchidos com os dados atuais do livro -->
<h2><img class="icone" src="img/livro.svg" alt="">Editar livro</h2>
<form method="post" class="ficha" enctype="multipart/form-data">
  <!-- Campo invisível para guardar o ID do livro sendo editado -->
  <input type="hidden" name="id" value="<?= $livro['id'] ?>">
  
  <label>Título <input name="titulo" value="<?= e($livro['titulo']) ?>" required maxlength="150"></label>
  <label>Autor <input name="autor" value="<?= e($livro['autor']) ?>" required maxlength="100"></label>
  <label>Ano <input name="ano" type="number" value="<?= e($livro['ano']) ?>"></label>
  <label>Gênero <input name="genero" value="<?= e($livro['genero']) ?>" maxlength="50"></label>
  <label>Quantidade <input name="quantidade" type="number" min="1" value="<?= e($livro['quantidade']) ?>" required></label>
  
  <!-- Mostra a capa atual (ou a imagem padrão se o livro ainda não tiver capa) -->
  <?php $capaAtual = capa_do_livro($livro['id']); ?>
  <img class="capa capa-grande" src="<?= $capaAtual ? e($capaAtual) : 'img/sem-capa.svg' ?>" alt="Capa do livro">
  <label>Trocar capa <input name="capa" type="file" accept="image/jpeg,image/png,image/gif,image/webp"></label>
  <small>JPG, PNG, GIF ou WEBP, até 2 MB.</small>
  <?php if ($capaAtual): ?>
    <label class="opcao"><input type="checkbox" name="remover_capa" value="1"> Remover a capa atual</label>
  <?php endif; ?>
  
  <button>Salvar alterações</button>
</form>

<?php 
// Puxa o rodapé visual do site
require 'rodape.php'; 
?>
