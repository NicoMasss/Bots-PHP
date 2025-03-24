<?php

require_once 'simpletest/browser.php';

function configurarHeaders($browser) {
    $browser->addHeader('Accept: application/json');
    $browser->addHeader('Accept-Encoding: gzip, deflate, br');
    $browser->addHeader('Accept-Language: pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7');
    $browser->addHeader('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36');
}

function getCode($browser) {
    $browser->get('https://www.magazineluiza.com.br/');

    $dataRaw = $browser->getContent();
    $dataHtml = gzdecode($dataRaw);

    preg_match('/static\/(\S{0,30})\/_buildManifest/', $dataHtml, $code);

    return $code[1];
}

function getItem($browser, $code) {
    $respostaRaw = $browser->get('https://www.magazineluiza.com.br/_next/data/' . $code . '/busca/computadores.json?path1=computadores');
    $resposta = gzdecode($respostaRaw);
    
    preg_match_all('/"title":"([^"]+)".*?"price":"([^"]+)".*?"image":"([^"]+)".*?"url":"([^"]+)/', $resposta, $resultados, PREG_SET_ORDER);

    return $resultados;
}

function mostrarProduto($resultados) {
    $produtos = [];
    foreach ($resultados as $resultado) {
        $nomeProduto = $resultado[1];
        $preco = $resultado[2];
        $linkImagem = $resultado[3];
        $linkProduto = $resultado[4];

        $produto = [
            "nomeProduto" => $nomeProduto,
            "preco" => $preco,
            "linkImagem" => $linkImagem,
            "linkProduto" => $linkProduto
        ];

        $produtos[] = $produto;
    }

    $output = json_encode($produtos, JSON_PRETTY_PRINT);
    $outputFormatado = "<pre>" . $output . "</pre>";

    var_dump($outputFormatado);   
}

$browser = new SimpleBrowser();
configurarHeaders($browser);
$code = getCode($browser);
$resultados = getItem($browser, $code);
mostrarProduto($resultados);
?>