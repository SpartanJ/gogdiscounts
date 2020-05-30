<?php
require_once('config.php');
require_once('functions.php');

$dbpass = null !== DB_PASS ? "password=".DB_PASS : '';
$dbconn = pg_connect("host=".DB_HOST." user=".DB_USER." {$dbpass} dbname=".DB_NAME." options='--client_encoding=UTF8'");

$filter = array();
if ( $is_game = get_var('is_game') != NULL ) 	$filter[] = "(game->>'isGame')::boolean IS TRUE";
if ( $title = get_var('title') ) {				$title = pg_escape_string($dbconn, $title); $filter[] = "(game->>'title') ILIKE '%{$title}%'"; }
if ( $min_discount = get_var('min_discount' ) )	$filter[] = "(game->'price'->>'discountPercentage')::int >= " . (string)intval( $min_discount );
if ( $rating = get_var('rating' ) )				$filter[] = "(game->>'rating')::int >= " . (string)intval( $rating );
if ( $price_from = get_var('price_from') )		$filter[] = "(game->'price'->>'amount')::float >= " . (string)floatval( $price_from );
if ( $price_to = get_var('price_to') )			$filter[] = "(game->'price'->>'amount')::float <= " . (string)floatval( $price_to );
$filters = implode( " AND ", $filter );
if ( !empty( $filters ) ) $filters = "WHERE {$filters}";

$order_types = array( 'title' => "game->'title'", 'price' => "(game->'price'->>'amount')::float", 'discount' => "game->'price'->'discountPercentage'", 'rating' => "game->'rating'" );
$order_type = $order_types[ get_var( 'order_type', 'discount' ) ];
$order_dir = get_var( 'order_dir', 'DESC' );
$sql = "SELECT * FROM games {$filters} ORDER BY {$order_type} {$order_dir}, game->'rating' DESC";
$arr = pg_fetch_all(pg_query($dbconn, $sql));
?>
<!DOCTYPE html>
<html>
<head>
	<title>GOG Discounts</title>
	<link rel="shortcut icon" href="https://www.gog.com/favicon.ico?3">
	<link rel="stylesheet" href="https://unpkg.com/purecss@1.0.0/build/pure-min.css" integrity="sha384-nn4HPE8lTHyVtfCBi5yW9d20FjT8BJwUXyWZT9InLYax14RDjBj46LmSztkmNP9w" crossorigin="anonymous">
	<link rel="stylesheet" href="https://unpkg.com/purecss@1.0.0/build/grids-responsive-min.css">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<style>
		a { color: #007EBD; text-decoration: none; }
		a:hover { text-decoration: underline; }
		.pure-text-center { text-align:center; }
		.pure-form-stacked button {
			margin-top: 8px;
		}
		@media (max-width: 1000px) {
			.pure-form-stacked input {
				margin: 4px auto!important;
			}
			
			.pure-form-stacked button {
				margin-left: 20px;
			}
		}
		@media (prefers-color-scheme: dark) {
			body {
				background-color: #313537;
				color: #ccc;
			}
			
			.pure-table-horizontal td,
			.pure-table-horizontal th {
				border-bottom: 0;
			}
			
			.pure-table thead {
				background-color: #383D40;
				color: #ccc;
				border-bottom: 1px solid #5e5e5e;
			}
			
			.pure-table {
				border: 1px solid #5e5e5e;
			}
			
			a {
				color: #83b4e0;
			}
			
			thead a {
				color: #8fc5f5;
			}
			
			input {
				background-color: #31363A !important;
				box-shadow: inset 0 1px 3px #1b1b1b !important;
				border-color: #666 !important;
				color: #ccc !important;
			}
			
			.pure-form legend {
				border-bottom: 1px solid #666!important;
			}
			
			.pure-button-primary {
				background-color: #535353;
			}
			
			.pure-button-secondary {
				color: #ccc;
				background-color: #434343;
			}
			::placeholder {
			  color: #ccc;
			  opacity: 0.9;
			}
		}
	</style>
	<script>
		function filter_form_reset() {
			document.getElementById('rating').value = '';
			document.getElementById('min_discount').value = '';
			document.getElementById('price_from').value = '';
			document.getElementById('price_to').value = '';
			document.getElementById('title').value = '';
		}
	</script>
</head>
<body>
	<div class="pure-g">
		<div class="pure-u-1 pure-text-center">
			<h1>GOG Discounts</h1>
		</div>
		<div class="pure-u-1">
			<div class="pure-u-lg-1-5"></div>

			<div class="pure-u-1 pure-u-lg-3-5">
				<form id="filter_form" class="pure-form pure-form-stacked">
					<fieldset>
						<legend>Search filters:</legend>

						<div class="pure-g pure-text-center">

							<div class="pure-u-1 pure-u-lg-1-5">
								<label for="rating">Rating bigger or eq than...</label>
								<input type="number" id="rating" name="rating" class="pure-u-23-24" placeholder="Rating bigger or eq than..." value="<?=$rating?>">
							</div>

							<div class="pure-u-1 pure-u-lg-1-5">
								<label for="discount">Minimum discount...</label>
								<input type="number" id="min_discount" name="min_discount" class="pure-u-23-24" placeholder="Minimum discount..." value="<?=$min_discount?>">
							</div>

							<div class="pure-u-1 pure-u-lg-1-5">
								<label for="price_from">Price from...</label>
								<input type="number" id="price_from" name="price_from" class="pure-u-23-24" placeholder="Price from..." value="<?=$price_from?>" step="0.01" min="0" max="1000" lang="en-150">
							</div>

							<div class="pure-u-1 pure-u-lg-1-5">
								<label for="price_to">Price to...</label>
								<input type="number" id="price_to" name="price_to" class="pure-u-23-24" placeholder="Price to..." value="<?=$price_to?>" step="0.01" min="0" max="1000" lang="en-150">
							</div>

							<div class="pure-u-1 pure-u-lg-1-5">
								<label for="price_to">Title...</label>
								<input type="text" id="title" name="title" class="pure-u-23-24" placeholder="Title..." value="<?=$title?>">
							</div>
						</div>

						<button type="submit" class="pure-button pure-button-primary">Filter</button>

						<button type="submit" class="pure-button pure-button-secondary" onclick="filter_form_reset();">Clear</button>

					</fieldset>
				</form>

				<table width="100%" class="pure-table pure-table-horizontal">
					<thead>
						<tr>
							<td><a href="<?=get_url('title', $order_dir)?>">Title</a></td>
							<td><a href="<?=get_url('discount', $order_dir)?>">Discount</a></td>
							<td><a href="<?=get_url('rating', $order_dir)?>">Rating</a></td>
							<td><a href="<?=get_url('price', $order_dir)?>">Price</a></td>
						</tr>
					</thead>
					<tbody>
				<?
				if ( !empty($arr) ) { foreach ( $arr as $r ) {
					$json = json_decode($r['game']);
					echo "				<tr>
						<td><a target='_blank' href='https://www.gog.com{$json->url}'>{$json->title}</a>" . "</td>
						<td>{$json->price->discountPercentage}</td>
						<td>{$json->rating}</td>
						<td>{$json->price->amount}</td>
					</tr>";
				}}
				?>
					</tbody>
				</table>
			</div>
			<div class="pure-u-lg-1-5"></div>
		</div>
	</div>
</body>
</html>