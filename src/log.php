<?php
declare( strict_types = 1 );

class Log {
	public function __construct() { }

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
			case 'array':
				$out .= self::add_array( $data, $level );
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

	public static function add_array( $data, $level ) {
		$out = str_repeat( ' ', $level * 4 ) . "(array) [\n";
		$level++;

		foreach( $data as $k => $v ) {
			$out .= str_repeat( ' ', $level * 4 ) . "[$k] => "
				. ltrim( self::parse( $v, $level ) );
		}

		$level--;
		$out .= str_repeat( ' ', $level * 4 ) . "]\n";
		return $out;
	}
}
