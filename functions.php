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
	return '?' . http_build_query( $query_arr );
}
