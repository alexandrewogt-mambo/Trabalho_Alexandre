<?php
// Funções para cuidar das capas dos livros.
// As imagens ficam na pasta "capas/" e o nome do arquivo é o ID do livro (ex: capas/7.jpg).
// Assim não precisa mudar nada no banco de dados.

// Pasta onde as capas são guardadas
const PASTA_CAPAS = __DIR__ . '/capas';

// Tipos de imagem aceitos e a extensão de cada um
const TIPOS_CAPA = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp'];

// Tamanho máximo da imagem: 2 MB
const TAMANHO_MAX_CAPA = 2 * 1024 * 1024;

// Devolve o endereço da capa de um livro, ou null se ele não tiver capa
function capa_do_livro($id) {
    $achados = glob(PASTA_CAPAS . '/' . (int)$id . '.*');
    if ($achados) {
        // O "?v=" muda quando a capa é trocada, assim o navegador não mostra a imagem antiga
        return 'capas/' . basename($achados[0]) . '?v=' . filemtime($achados[0]);
    }
    return null;
}

// Apaga a capa de um livro (se existir)
function remover_capa($id) {
    foreach (glob(PASTA_CAPAS . '/' . (int)$id . '.*') ?: [] as $arquivo) {
        unlink($arquivo);
    }
}

// Salva a imagem enviada pelo formulário como capa do livro.
// Devolve '' se deu certo (ou se nenhum arquivo foi enviado) e um texto de erro se algo deu errado.
function salvar_capa($id, $arquivo) {
    // Nenhum arquivo escolhido: não é erro, só não tem nada para salvar
    if (!$arquivo || $arquivo['error'] === UPLOAD_ERR_NO_FILE) {
        return '';
    }

    // O PHP avisa se o envio falhou (arquivo grande demais para o servidor, por exemplo)
    if ($arquivo['error'] === UPLOAD_ERR_INI_SIZE || $arquivo['error'] === UPLOAD_ERR_FORM_SIZE) {
        return 'A imagem é grande demais (máximo 2 MB).';
    }
    if ($arquivo['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($arquivo['tmp_name'])) {
        return 'Não foi possível enviar a imagem.';
    }

    // Confere o tamanho
    if ($arquivo['size'] > TAMANHO_MAX_CAPA) {
        return 'A imagem é grande demais (máximo 2 MB).';
    }

    // Confere se o arquivo é mesmo uma imagem olhando o conteúdo dele
    // (não confia no nome nem na extensão que o usuário mandou)
    $info = @getimagesize($arquivo['tmp_name']);
    if (!$info || !isset(TIPOS_CAPA[$info['mime']])) {
        return 'O arquivo precisa ser uma imagem JPG, PNG, GIF ou WEBP.';
    }

    // Cria a pasta de capas se ela ainda não existir
    if (!is_dir(PASTA_CAPAS)) {
        mkdir(PASTA_CAPAS, 0755, true);
    }

    // Apaga a capa antiga e guarda a nova com o nome "ID.extensão"
    remover_capa($id);
    $destino = PASTA_CAPAS . '/' . (int)$id . '.' . TIPOS_CAPA[$info['mime']];
    if (!move_uploaded_file($arquivo['tmp_name'], $destino)) {
        return 'Não foi possível salvar a imagem na pasta capas.';
    }
    return '';
}
