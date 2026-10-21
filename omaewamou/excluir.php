<?php
// Puxa o arquivo que conecta com o banco de dados
require 'conexao.php';
// Só deixa passar quem fez login (senão vai para a tela de login)
require 'auth.php';
// Funções para cuidar das capas dos livros
require 'capas.php';

// Bloqueia o acesso direto pela barra de endereço (só aceita vindo do botão de excluir)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { 
    header('Location: index.php');
    exit;
}

try {
    // Tenta deletar o livro do banco usando o ID dele
    $consulta = $pdo->prepare("DELETE FROM livros WHERE id = :id");
    $consulta->execute([':id' => (int)$_POST['id']]);
    // O livro foi apagado, então apaga também a capa dele
    remover_capa((int)$_POST['id']);
    $msg = 'Livro excluído.';
} catch (PDOException $erro) {
    // Se o banco não deixar apagar (por segurança), define esta mensagem de aviso
    $msg = 'Este livro tem empréstimos registrados e não pode ser excluído.';
}

// Redireciona para a página inicial mostrando a mensagem do resultado
header('Location: index.php?msg=' . urlencode($msg));
exit;
