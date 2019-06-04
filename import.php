<?php
require_once('config.php');
require_once('functions.php');

if ( get_var('api_key') !== API_KEY ) {
	http_response_code(401);
	echo "API KEY Invalid";
	exit(0);
}

$num_pages = 0;
$next_page=1;

$dbpass = null != DB_PASS ? ";password=".DB_PASS : '';
$pdo = new PDO("pgsql:host=".DB_HOST.";user=".DB_USER.";dbname=".DB_NAME.$dbpass);
$pdo->query("DELETE FROM games");

$opts = array(
	'http'=>array(
		'method'=>"GET",
		'header'=>"Accept-language: en-US\r\n" /*.
			  "Cookie: gog_lc=AR_USD_en-US\r\n" */// gog_lc sets the location to query the prices,
			  // GOG should stop supporting regional prices soon, but it's still working on some countries.
	)
);
$context = stream_context_create($opts);

do {
	$page_url = "https://www.gog.com/games/ajax/filtered?mediaType=game&page={$next_page}&price=discounted&sort=popularity";
	$obj = json_decode(file_get_contents($page_url, false, $context));

	if ( $num_pages === 0 ) {
		$num_pages = $obj->totalPages;

		echo "Total pages {$num_pages}\n<br/>";
	}

	$games = array();

	foreach ( $obj->products as $prod ) {
		try {
			$prep = $pdo->prepare('INSERT INTO games (id,game) VALUES (:id, :game)');
			$prep->bindValue(':id', $prod->id);
			$prep->bindValue(':game', json_encode($prod));
			$prep->execute();
			echo "{$prod->title} data is successfully logged<br/>\n";
		} catch (\PDOException $e) {
			echo $e->getMessage();
		}
	}

	$next_page++;
} while ( $next_page < $num_pages );
