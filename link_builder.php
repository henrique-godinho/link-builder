<?php

// Check if data is sent
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the data
    $formData = $_POST;

    //Add the following leagues:

    $url = "https://landing.page/sports/?";
    $universal_link_email = "https://link.email/go?\$desktop_url=";
    $universal_link_push =  "midnite://link.push/go?\$desktop_url=";
    $matchId = $formData['match_id'];
    $bet_type = "btype=";
    $bid = "";
    $btag = "";
    $btag_a;
    $btag_b;
    $btag_c;
    $utm_source;
    $utm_campaign;
    $utm_medium;
    $utm_term;
    $parameters = [];

    foreach($formData['contract_ids'] as $id) {
        $bid .= "bid=" . $matchId . "," . $id . "&";

    }

    $bid = rtrim($bid, "&");

    $parameters[] = $bid;

    foreach($formData['formData'] as $item) {

        if (in_array($item['name'], ['btag-a', 'btag-b', 'btag-c'])) {
            if(!preg_match('/^[a-zA-Z0-9_-]*$/', $item['value']) || strlen($item['value']) > 255) {
                echo 'Invalid input! Only alphanumeric characters, underscores, and hyphens under 255 characters are allowed. Received: ' . $item['value'];
                exit;
            }
        }

        if(in_array($item['name'], ['utm_source', 'utm_campaign', 'utm_medium', 'utm_term'])) {
            if(!preg_match('/^[a-zA-Z0-9_-]*$/', $item['value']) || strlen($item['value']) > 255) {
                echo 'Invalid input! Only alphanumeric characters, underscores, and hyphens under 255 characters are allowed. Received: ' . $item['value'];
                exit;
            }   
        }

        if ($item['value'] != "") {

            if ($item['name'] === "btype") {
                $bet_type .= $item['value'];
                $parameters[] = $bet_type;

            } elseif ($item['name'] === "btag-a") {
                $btag_a = "a_" . $item['value'];
                $btag .= "btag=" . $btag_a;

            } elseif ($item['name'] === "btag-b") {
                $btag_b = "b_" . $item['value'];
                $btag .= $btag_b;

            } elseif ($item['name'] === "btag-c") {
                $btag_c = "c_" . $item['value'];
                $btag .= $btag_c;

            } elseif ($item['name'] === "utm_source") {
                $utm_source = "utm_source=" . $item['value'];
                $parameters[] = $utm_source;

            } elseif ($item['name'] === "utm_campaign") {
                $utm_campaign = "utm_campaign=" . $item['value'];
                $parameters[] = $utm_campaign;

            } elseif ($item['name'] === "utm_medium") {
                $utm_medium = "utm_medium=" . $item['value'];
                $parameters[] = $utm_medium;

            } elseif ($item['name'] === "utm_term") {
                $utm_term = "utm_term=" . $item['value'];
                $parameters[] = $utm_term;
            }
            
        } else {

            continue;
        }
    }

    // $btag .= $btag_a . $btag_b . $btag_c;
    if ($btag != "") {
        $parameters[] = $btag;
    }
   
    $finalURL = urlbuilder($url, $parameters);
    $universal_link_email .= urlencode($finalURL);
    $universal_link_push .= urlencode($finalURL);

    // header('Content-Type: application/json');
    header('X-Content-Type-Options: nosniff');
    // echo json_encode($finalURL, JSON_UNESCAPED_SLASHES);
    echo '<textarea rows="20" cols="100">' .

    'Web' . '->' . " " . $finalURL  . "\n" .
    "\n" .

    'universal link email' . '->' . " " . $universal_link_email . "\n" .
    "\n" .

    'universal link push' . '->' . " " . $universal_link_push  . "\n" .
    "\n" .
    
    '</textarea>';

    
} else {
    // Handle other request methods or show an error
    echo 'Invalid request method';
}

function urlbuilder($url, array $parameters) {
    foreach($parameters as $param) {
        if($param != "") {
            $url .= $param . "&";
        }

    }
    $url = rtrim($url, "&");
    return $url;
}