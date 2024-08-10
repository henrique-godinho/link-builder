<?php

if (isset($_GET['id'])) {
    $matchId = $_GET['id'];
} else {
    $matchId = "Null";
}

$league = $_GET['league'];


$matchDetails = [];

function matchDetails (&$set, $item) {
    $set[] = $item;
}

$url = '';

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

if($data != null) {
    if(isset($data['items']) && is_array($data['items'])) {
        foreach($data['items'] as $item) {
            if ($item['match_id'] == $matchId) {
                matchDetails($matchDetails, $item);
            }
        }
    } else {
        echo "No match found";
    }
}

$marketDetails = [];

foreach ($matchDetails as $item) {
    $marketId = $item['market_id'];
    $marketName = $item['market'];

    // Initialize an array for the market ID if it doesn't exist
    if (!isset($marketDetails[$marketId])) {
        $marketDetails[$marketId] = [
            'marketName' => $marketName,
            'contracts' => []
        ];
    }

    // Extract contract-related data
    foreach ($item as $key => $value) {
        if (preg_match('/^contracts\.\d+\.(id|name|fractional_price)$/', $key, $matches)) {
            $dataType = $matches[1];

            // Initialize the $contractData array for each contract
            if (!isset($contractData[$dataType])) {
                $contractData[$dataType] = $value;
            }

            // If all "id", "name", and "fractional_price" are set, add to contracts array
            if (isset($contractData['id']) && isset($contractData['name']) && isset($contractData['fractional_price'])) {
                $marketDetails[$marketId]['contracts'][] = $contractData;
                $contractData = []; // Reset $contractData for the next contract
            }
        }
    }
}

// Convert the marketDetails array to JSON
$marketDetailsJson = json_encode($marketDetails);

// Pass $marketDetailsJson to your JavaScript
header('Content-Type: application/json');
echo $marketDetailsJson;

?>