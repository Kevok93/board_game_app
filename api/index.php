<?php

$verb = $_SERVER['REQUEST_METHOD'];

$uri = $_SERVER['REQUEST_URI'];


$url_parts = parse_url($uri);
$path = $url_parts["path"];
$parameters = $url_parts["query"];

$prefix = "api";
$ind = strpos($path, $prefix);
$request = substr($path, $ind + strlen($prefix));

//echo $request."\n";
//echo $verb."\n";
//echo json_encode($_REQUEST)."\n";
if ($request == "/games") {
    if ($verb == "GET") {
        $dbhandle = new PDO("sqlite:bgg.sqlite") or die("Failed to open");
        if (!$dbhandle) die ($error);
        $players = isset($_REQUEST["players"])
            ? " AND minplayers <= ".$_REQUEST["players"].
              " AND maxplayers >= ".$_REQUEST["players"]
            : " ";
        $minplayers = isset($_REQUEST["minplayers"])
            ? " AND minplayers >= ".$_REQUEST["minplayers"]." "
            : " ";
        $maxplayers = isset($_REQUEST["maxplayers"])
            ? " AND maxplayers <= ".$_REQUEST["maxplayers"]." "
            : " ";
        $playtime_mod = isset($_REQUEST["playtime_mod"])
            ? $_REQUEST["playtime_mod"]
            : 15;
        $playtime = isset($_REQUEST["playtime"])
            ? " AND abs(playingtime - ".$_REQUEST["playtime"].") <= $playtime_mod "
            : " ";
        $minplaytime = isset($_REQUEST["minplaytime"])
            ? " AND playingtime >= ".$_REQUEST["minplaytime"]." "
            : " ";
        $maxplaytime = isset($_REQUEST["maxplaytime"])
            ? " AND playingtime <= ".$_REQUEST["maxplaytime"]." "
            : " ";
        if (isset($_REQUEST["order"])) {
            switch ($_REQUEST["order"]) {
                case "rank": 		$order = " ORDER BY rank "; break;
                case "playtime":	$order = " ORDER BY playingtime "; break;
                case "alpha":		$order = " ORDER BY objectname "; break;
                default:			$order = " ORDER BY random() "; break;
            }
        } else $order = " ORDER BY random() ";
        $descending = isset($_REQUEST["desc"])
            ? " DESC "
            : " ";
        $count		= isset($_REQUEST["result_count"])
            ? " LIMIT 0, ".$_REQUEST["result_count"]." "
            : " LIMIT 0, 10 ";

        $query = "
            SELECT DISTINCT
            objectname as game,
            rank,
            maxplayers,
            minplayers,
            playingtime
            FROM games
            WHERE 1=1
            $players
            $minplayers
            $maxplayers
            $playtime
            $minplaytime
            $maxplaytime
            $order
            $descending
            $count
        ";
        //echo $query."\n";
        $statement = $dbhandle->prepare($query);

        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        header('HTTP/1.1 200 OK');
        header('Content-Type: application/json');
        header('Kevok: Query:'.$query);
        echo json_encode($results);

    } else {

        header('HTTP/1.1 404 Not Found');

    }
} else {

    header('HTTP/1.1 404 Not Found');

}

?>
