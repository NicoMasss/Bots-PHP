<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesquisar na Magalu</title>
    <style>
        body {
            text-align: center;
            font-family: Arial, sans-serif;
            margin-top: 50px;
        }

        input {
            width: 250px;
            padding: 8px;
            font-size: 16px;
        }

        button {
            padding: 8px 15px;
            font-size: 16px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <h2>Pesquisar na Magalu</h2>
    <form method="POST" action="magazine_luiza.php">
        <input type="text" name="pesquisa" placeholder="Digite sua pesquisa">
        <button type="submit">Pesquisar</button>
    </form>
</body>
</html>
