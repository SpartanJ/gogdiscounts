<?php
function get_var( $name, $def = NULL ) {
	if ( isset( $_GET[$name] ) && !empty( $_GET[$name] ) )
		return $_GET[$name];
	return $def;
}

function get_url( $order_type, $order_dir ) {
	$order_dir = $order_dir == 'DESC' ? 'ASC' : 'DESC';
	parse_str( $_SERVER['QUERY_STRING'], $query_arr );
	$query_arr['order_type'] = $order_type;
	$query_arr['order_dir'] = $order_dir;
	$query_arr['location'] = get_var('location');
	return '?' . http_build_query( $query_arr );
}

function get_table_name() {
	$loc = get_var('location');
	return $loc ? "games_" . $loc : "games";
}

function get_gog_loc() {
	$loc = get_var('location');
	return $loc == "ar" ? "AR_USD_en-US" : "US_USD_en-US";
}
