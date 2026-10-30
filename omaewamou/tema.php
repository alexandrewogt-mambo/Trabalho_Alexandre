<?php
require 'conexao.php';
require 'auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $novoTema = ($_POST['tema'] ?? '') === 'escuro' ? 'escuro' : 'claro';

    $consulta = $pdo->prepare("UPDATE usuarios SET tema = :tema WHERE id = :id");
    $consulta->execute([
        ':tema' => $novoTema,
        ':id'   => $_SESSION['usuario_id']
    ]);
}

$voltar = $_SERVER['HTTP_REFERER'] ?? 'index.php';
header('Location: ' . $voltar);
exit;