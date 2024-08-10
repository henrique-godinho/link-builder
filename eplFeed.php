<?php
$league = $_GET['league'];
$url = '';
$matchSet = [];

function addMatch(&$set, $matchName, $matchId) {
    if (!isset($set[$matchName])) {
        $set[$matchName] = $matchId;
    }
    else {
        $set[$matchName] = $matchId;
    }

    return $set;
}

switch ($league) {
    case 'epl':
        $url = 'https://feed.url/sport1';
        break;
    case 'bundesliga':
        $url = 'https://feed.url/sport2';
        break;
    case 'ligue1':
        $url = 'https://feed.url/sport3';
        break;
    case 'serie_a':
        $url = 'https://feed.url/sport4';
        break;
    case 'fa_cup':
        $url = 'https://feed.url/sport5';
        break;
    case 'efl':
        $url = 'https://feed.url/sport6';
        break;
    case 'ucl':
        $url = 'https://feed.url/sport7';
        break;
    case 'uel':
        $url = 'https://feed.url/sport8';
        break;
    case 'uefa_conference_league':
        $url = 'https://feed.url/sport9';
        break;
    case 'efl_championship':
        $url = 'https://feed.url/sport10';
        break;
    case 'euros_2024':
        $url = 'https://feed.url/sport11';
        break;

}

$curl = curl_init($url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($curl);

$data = json_decode($response, true);

if ($data != null) {
    if(isset($data['items']) && is_array($data['items'])) {
        foreach ($data['items'] as $item) {
            $matchSet = addMatch($matchSet, $item['match_name'], $item['match_id']);
        }
    }
}

$matchSetJson = json_encode($matchSet);

header('Content-Type: application/json');
echo $matchSetJson;

curl_close($curl);

?>