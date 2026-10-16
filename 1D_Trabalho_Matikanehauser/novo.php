<?php
// Puxa o arquivo que conecta com o banco de dados
require 'conexao.php';

// SE o usuário preencheu e enviou o formulário (clicou em salvar):
if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
    // Prepara o comando para inserir o novo livro na tabela
    $sql = "INSERT INTO livros (titulo, autor, ano, genero, quantidade)
            VALUES (:titulo, :autor, :ano, :genero, :quantidade)";
    $consulta = $pdo->prepare($sql);
    
    // Troca as marcações pelos dados digitados e salva de verdade no banco
    $consulta->execute([
        ':titulo'     => $_POST['titulo'],
        ':autor'      => $_POST['autor'],
        ':ano'        => $_POST['ano'] !== '' ? (int)$_POST['ano'] : null,
        ':genero'     => $_POST['genero'],
        ':quantidade' => (int)$_POST['quantidade'],
    ]);
    
    // Redireciona para a lista de livros mostrando um aviso de sucesso
    header('Location: index.php?msg=' . urlencode('Livro cadastrado!')); 
    exit;
}

// Puxa o topo do site (cabeçalho, menu)
require 'topo.php';
?>

<!-- Formulário com os campos vazios para cadastrar um novo livro -->
<h2>Novo livro</h2>
<form method="post" class="ficha">
  <label>Título <input name="titulo" required maxlength="150"></label>
  <label>Autor <input name="autor" required maxlength="100"></label>
  <label>Ano <input name="ano" type="number"></label>
  <label>Gênero <input name="genero" maxlength="50"></label>
  <label>Quantidade <input name="quantidade" type="number" min="1" value="1" required></label>
  <button>Salvar</button>
</form>

<?php 
// Puxa o rodapé do site
require 'rodape.php'; 
?>
