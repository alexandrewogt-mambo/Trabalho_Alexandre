<?php
// Inicia a sessão (é ela que guarda quem está logado). Precisa vir antes de qualquer HTML.
session_start();

// Dados para conseguir entrar no banco de dados
$host  = 'localhost';   // Endereço do servidor
$banco = 'biblioteca';  // Nome do banco de dados
$user  = 'root';        // Usuário do banco
$senha = '';            // Senha do banco

try {
    // Tenta conectar ao banco de dados
    $pdo = new PDO("mysql:host=$host;dbname=$banco;charset=utf8mb4", $user, $senha);
    
    // Ativa os avisos de erro caso algo dê errado no banco
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);       
    
    // Organiza os dados que vêm do banco pelo nome das colunas
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); 
} catch (PDOException $erro) {
    // Se a conexão falhar, para o site e mostra um aviso de erro
    die('Não consegui conectar ao banco: ' . $erro->getMessage() .
        '<br>Confira se o MySQL está ligado no painel do XAMPP e se o banco.sql foi importado.');
}

// Função para limpar o texto antes de mostrar na tela (evita invasões)
function e($texto) {
    return htmlspecialchars((string)$texto, ENT_QUOTES, 'UTF-8');
}
