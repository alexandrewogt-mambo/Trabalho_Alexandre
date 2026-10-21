<?php
// Puxa o arquivo que conecta com o banco de dados
require 'conexao.php';
// Só deixa passar quem fez login (senão vai para a tela de login)
require 'auth.php';
// Funções para cuidar das capas dos livros
require 'capas.php';

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
    
        // Pega o ID que o banco deu ao livro novo
        $idNovoLivro = (int)$pdo->lastInsertId();

        // Salva o arquivo físico da capa (ela vai virar capas/ID.extensão)
        $erroCapa = salvar_capa($idNovoLivro, $_FILES['capa'] ?? null);
        
        // SE deu tudo certo no upload, descobre o nome real do arquivo gerado e atualiza a coluna 'capa' no banco
        if (!$erroCapa) {
            $arquivosSalvos = glob(__DIR__ . '/capas/' . $idNovoLivro . '.*');
            if (!empty($arquivosSalvos)) {
                $nomeFinalDoArquivo = 'capas/' . basename($arquivosSalvos[0]);
                
                // Grava o caminho exato na coluna 'capa' que criamos
                $sqlUpdateCapa = $pdo->prepare("UPDATE livros SET capa = :capa WHERE id = :id");
                $sqlUpdateCapa->execute([':capa' => $nomeFinalDoArquivo, ':id' => $idNovoLivro]);
            }
        }
        
        // Redireciona para a lista de livros mostrando um aviso
        $msg = $erroCapa ? 'Livro cadastrado, mas a capa não foi salva: ' . $erroCapa : 'Livro cadastrado com sucesso!';
        header('Location: index.php?msg=' . urlencode($msg)); 
        exit;
    
}

// Puxa o topo do site (cabeçalho, menu)
require 'topo.php';
?>

<!-- Formulário com os campos vazios para cadastrar um novo livro -->
<h2><img class="icone" src="img/livro.svg" alt="">Novo livro</h2>
<form method="post" class="ficha" enctype="multipart/form-data">
  <label>Título <input name="titulo" required maxlength="150"></label>
  <label>Autor <input name="autor" required maxlength="100"></label>
  <label>Ano <input name="ano" type="number"></label>
  <label>Gênero <input name="genero" maxlength="50"></label>
  <label>Quantidade <input name="quantidade" type="number" min="1" value="1" required></label>
  <label>Capa (opcional) <input name="capa" type="file" accept="image/jpeg,image/png,image/gif,image/webp"></label>
  <small>JPG, PNG, GIF ou WEBP, até 2 MB.</small>
  <button>Salvar</button>
</form>

<?php 
// Puxa o rodapé do site
require 'rodape.php'; 
?>
