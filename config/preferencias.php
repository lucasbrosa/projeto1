<?php

$preferencias = [];

// Verifique se o arquivo de preferências existe
if (file_exists('./config/preferencias.json')) {
    // Lê as preferências do arquivo JSON
    $preferencias_json = file_get_contents('./config/preferencias.json'); 
    $preferencias = json_decode($preferencias_json, true);
}



// Defina as preferências como variáveis globais para acesso em outras páginas
$GLOBALS['telefone_whatsapp'] = isset($preferencias['telefone']) ? $preferencias['telefone'] : '';
$GLOBALS['mensagem_whatsapp'] = isset($preferencias['mensagem']) ? $preferencias['mensagem'] : '';
$GLOBALS['descricao_padrao_produto'] = isset($preferencias['descricao']) ? $preferencias['descricao'] : 'A decoração será montada conforme imagens.';
$GLOBALS['cards_por_linha'] = isset($preferencias['cards_por_linha']) ? $preferencias['cards_por_linha'] : '4';
$GLOBALS['tamanho_fotos'] = isset($preferencias['tamanho_fotos']) ? $preferencias['tamanho_fotos'] : '100';
?>
