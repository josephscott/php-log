<?php
declare( strict_types = 1 );

class Log {
	public $stringify = 'stringify_print_r';

	public function __construct() {
	}

	public static function php( $data ) {
		$out = self::parse( $data );
		error_log( $out );
	}

	public static function parse( $data, $level = 0 ) {
		$out = '';

		$type = strtolower( gettype( $data ) );
		switch( $type ) {
			case 'null':
			case 'boolean':
			case 'integer':
			case 'double':
			case 'string':
			case 'resource':
				$out .= self::add_simple( $type, $data, $level ); 
				break;
		}

		return $out;
	}

	public static function add_simple( $type, $data, $level ) {
		if ( $type === 'boolean' ) {
			$data = 'false';
			if ( $data === true ) {
				$data = 'true';
			}
		}

		if ( $type === 'resource' ) {
			$resource = get_resource_type( $data );
			$type .= ", $resource";
		}

		$out = str_repeat( ' ', $level * 4 ) . "($type) $data\n";
		return $out;
	}
}
