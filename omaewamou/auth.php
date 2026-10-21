<?php
// Guarda do site: coloque "require 'auth.php';" no começo de toda página que só
// quem fez login pode ver. A sessão já foi iniciada no conexao.php.

// Se não tem usuário guardado na sessão, manda para a tela de login e para o código
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}
