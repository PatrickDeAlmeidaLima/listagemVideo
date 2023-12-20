<?php

// Substitua 'SUA_CHAVE_DE_API' e 'SEU_SEGREDO_DE_API' pela sua chave de API e segredo de API do DailyMotion
$api_key = 'c7503025672b9c5e8a1f';
$api_secret = '4be46874c4301834f31776cfd9f97119e893d4d3';

// $api_key = '355def6f8f94a5e07ad0';
// $api_key = '\3$SlG`Pe[3VB<A+ns8x7tl10xxx&zDo';

// URL do endpoint de token do DailyMotion
$token_url = 'https://api.dailymotion.com/oauth/token';

// Configuração da requisição cURL para obter o token de acesso
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $token_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, [
    'grant_type' => 'client_credentials',
    'client_id' => $api_key,
    'client_secret' => $api_secret,
    'scope' => '', // Pode ser deixado em branco ou definido para as permissões desejadas
]);

// Executa a requisição e obtém a resposta
$response = curl_exec($ch);

// Verifica se houve algum erro
if (curl_errno($ch)) {
    echo 'Erro ao obter o token de acesso: ' . curl_error($ch);
} else {
    // Decodifica a resposta JSON
    $token_info = json_decode($response, true);

    // Verifica se o token de acesso está presente
    if (isset($token_info['access_token'])) {
        $access_token = $token_info['access_token'];

        // URL para obter os vídeos com o código de incorporação
        $channel_name = 'correiowebcb';
        // $videos_url = "https://api.dailymotion.com/user/$channel_name/videos?fields=id,title,url,thumbnail_url,embed_url&limit=5&sort=recent";
        $videos_url = "https://api.dailymotion.com/user/$channel_name/videos?fields=id,title,url,thumbnail_url,embed_url&sort=recent";

        curl_setopt($ch, CURLOPT_URL, $videos_url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $access_token,
        ]);

        // Altera o método para GET
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

        // Executa a requisição para obter os vídeos com embed
        $videos_response = curl_exec($ch);

        // Exibe a resposta (vídeos com embed) ou trata os erros conforme necessário
        // echo $videos_response;

        // Paginação
        $videos = json_decode($videos_response, true);
        $total_videos = count($videos['list']);
        $videos_per_page = 5;
        $total_pages = ceil($total_videos / $videos_per_page);
        $current_page = isset($_GET['page']) ? $_GET['page'] : 1;
        $start = ($current_page - 1) * $videos_per_page;
        $videos_to_show = array_slice($videos['list'], $start, $videos_per_page);

        // Exibe os vídeos
        foreach ($videos_to_show as $video) {
            echo '<ul class="video-list">';
            echo '<li class="video-item">';
            echo '<h2 class="video-title">' . $video['title'] . '</h2>';
            echo '<iframe src="' . $video['embed_url'] . '"  width="560" height="315" frameborder="0" allowfullscreen class="video-embed"></iframe>';
            echo '</li>';
            echo '</ul">';
        }

        // paginacao
        echo '<div class="pagination">';
        for ($i = 1; $i <= $total_pages; $i++) {
            if ($i == $current_page) {
                echo '<span class="current">' . $i . '</span>';
            } else {
                echo '<a href="?page=' . $i . '">' . $i . '</a>';
            }
        }
        echo '</div>';
    } else {
        echo 'Erro ao obter o token de acesso. Resposta da API: ' . $response;
    }
}

// Fecha a conexão cURL
curl_close($ch);

// $response_array = json_decode($videos_response, true);

// if (isset($response_array['list']) && !empty($response_array['list'])) {
//     echo '<ul class="video-list">';
//     foreach ($response_array['list'] as $video) {
//         echo '<li class="video-item">';
//         echo '<h2 class="video-title">' . $video['title'] . '</h2>';
//         // echo '<a href="' . $video['url'] . '" target="_blank">';
//         // echo '<img src="' . $video['thumbnail_url'] . '" alt="' . $video['title'] . '" class="video-thumbnail">';
//         // echo '</a>';
//         echo '<iframe src="' . $video['embed_url'] . '" width="560" height="315" frameborder="0" allowfullscreen class="video-embed"></iframe>';
//         echo '</li>';
//     }
//     echo '</ul>';
// } else {
//     echo 'Nenhum vídeo encontrado.';
// }

?>

<style>
    .video-list {
        align-items: center;
        list-style-type: none;
        padding: 0;
        display: flex;
        flex-direction: column;
    }

    .video-item {
        max-width: 650px;
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-bottom: 20px;
        border: 1px solid #ddd;
        padding: 10px;
        border-radius: 5px;
    }

    .video-title {
        font-size: 1.2em;
        margin-top: 0;
    }

    .video-thumbnail {
        width: 100%;
        max-width: 300px;
        height: auto;
    }

    .video-embed {
        margin-top: 10px;
    }
</style>