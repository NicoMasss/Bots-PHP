<?php

if (isset($_POST['pesquisa'])) {
    require_once 'simpletest/browser.php';

    function configurarHeaders($browser) {
        $headers = [
            'Accept: application/json',
            'Accept-Encoding: gzip, deflate, br',
            'Accept-Language: pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36'
        ];
        foreach ($headers as $header) {
            $browser->addHeader($header);
        }
    }

    function getCode($browser) {
        try {
            $browser->get('https://www.magazineluiza.com.br/');
            $dataRaw = $browser->getContent();
            $dataHtml = mb_strpos($dataRaw, "\x1f\x8b") === 0 ? gzdecode($dataRaw) : $dataRaw;

            if (preg_match('/static\/([^\/]+)\/_buildManifest/', $dataHtml, $code)) {
                return $code[1];
            }
            return null;
        } catch (Exception $e) {
            return null;
        }
    }

    function getItem($browser, $code, $pesquisa) {
        if (!$code) return [];

        try {
            $pesquisa = urlencode($pesquisa);
            $url = "https://www.magazineluiza.com.br/_next/data/$code/busca/$pesquisa.json?path1=$pesquisa";
            $respostaRaw = $browser->get($url);
            $resposta = mb_strpos($respostaRaw, "\x1f\x8b") === 0 ? gzdecode($respostaRaw) : $respostaRaw;

            preg_match_all('/"title":"(.*?)".*?"image":"(.*?)".*?"path":"(.*?)".*?"bestPrice":"(.*?)"/', 
                $resposta, $resultados, PREG_SET_ORDER);

            return $resultados;
        } catch (Exception $e) {
            return [];
        }
    }

    function mostrarProduto($resultados) {
        if (empty($resultados)) {
            echo json_encode(["erro" => "Nenhum produto encontrado."], JSON_UNESCAPED_UNICODE);
            return;
        }

        $produtos = array_map(function ($resultado) {
            return [
                "nomeProduto" => $resultado[1],
                "preco" => $resultado[4],
                "linkImagem" => $resultado[2],
                "linkProduto" => "https://www.magazineluiza.com.br/" . $resultado[3]
            ];
        }, $resultados);

        echo json_encode($produtos, JSON_UNESCAPED_UNICODE);
    }

    if (empty($_POST['pesquisa']) || trim($_POST['pesquisa']) === '') {
        echo json_encode(["erro" => "O campo de pesquisa está vazio."], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $browser = new SimpleBrowser();
    configurarHeaders($browser);
    $code = getCode($browser);
    $pesquisa = $_POST['pesquisa'];
    $resultados = getItem($browser, $code, $pesquisa);
    mostrarProduto($resultados);
}
