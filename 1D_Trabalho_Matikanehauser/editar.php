<?php
// Puxa o arquivo que conecta com o banco de dados
require 'conexao.php';

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
    
    // Redireciona o usuário de volta para a lista de livros com um aviso de sucesso
    header('Location: index.php?msg=' . urlencode('Livro atualizado!'));
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
<h2>Editar livro</h2>
<form method="post" class="ficha">
  <!-- Campo invisível para guardar o ID do livro sendo editado -->
  <input type="hidden" name="id" value="<?= $livro['id'] ?>">
  
  <label>Título <input name="titulo" value="<?= e($livro['titulo']) ?>" required maxlength="150"></label>
  <label>Autor <input name="autor" value="<?= e($livro['autor']) ?>" required maxlength="100"></label>
  <label>Ano <input name="ano" type="number" value="<?= e($livro['ano']) ?>"></label>
  <label>Gênero <input name="genero" value="<?= e($livro['genero']) ?>" maxlength="50"></label>
  <label>Quantidade <input name="quantidade" type="number" min="1" value="<?= e($livro['quantidade']) ?>" required></label>
  
  <button>Salvar alterações</button>
</form>

<?php 
// Puxa o rodapé visual do site
require 'rodape.php'; 
?>
