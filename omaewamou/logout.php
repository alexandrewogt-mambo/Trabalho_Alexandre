<?php
// Puxa o arquivo que conecta com o banco de dados (ele também inicia a sessão)
require 'conexao.php';

// Apaga todos os dados da sessão e destrói ela (a pessoa deixa de estar logada)
$_SESSION = [];
session_destroy();

// Volta para a tela de login
header('Location: login.php');
exit;
