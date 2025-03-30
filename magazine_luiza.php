<?php

if (isset($_POST['pesquisa'])) {
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
        $dataHtml = mb_strpos($dataRaw, "\x1f\x8b") === 0 ? gzdecode($dataRaw) : $dataRaw; //verificação de Gzip

        preg_match('/static\/(\S{0,30})\/_buildManifest/', $dataHtml, $code);

        return $code[1];
    }

    function getItem($browser, $code) {
        $pesquisa = $_POST['pesquisa'];
        $pesquisa = str_replace(" ", "%2B", $pesquisa);
        $respostaRaw = $browser->get('https://www.magazineluiza.com.br/_next/data/' . $code . '/busca/'. $pesquisa . '.json?path1=' . $pesquisa);

        $resposta = mb_strpos($respostaRaw, "\x1f\x8b") === 0 ? gzdecode($respostaRaw) : $respostaRaw;

        preg_match_all('/"title":"([^"]+)".*?"image":"([^"]+)".*?"path":"([^"]+).*?"bestPrice":"([^"]+)"/', $resposta, $resultados, PREG_SET_ORDER);

        return $resultados;
    }

    function mostrarProduto($resultados) {
        $produtos = [];
        foreach ($resultados as $resultado) {
            $nomeProduto = $resultado[1];
            $preco = $resultado[4];
            $linkImagem = $resultado[2];
            $linkProduto = "https://www.magazineluiza.com.br/" . $resultado[3];

            $produto = [
                "nomeProduto" => $nomeProduto,
                "preco" => $preco,
                "linkImagem" => $linkImagem,
                "linkProduto" => $linkProduto
            ];

        $produtos[] = $produto;
    }
        $produtosJson = json_encode($produtos);
        
        // var_dump($produtos); para debug
        echo $produtosJson;
    }

    $browser = new SimpleBrowser();
    configurarHeaders($browser);
    $code = getCode($browser);
    $resultados = getItem($browser, $code);
    mostrarProduto($resultados);
};
?>