<?php
declare( strict_types = 1 );

class Log {
	public static $format = 'better_print_r';
	public static $available_formats = [
		'better_print_r',
		'print_r'
	];

	public function __construct() { }

	public static function php( $data ) {
		$parser = self::get_format();

		$out = self::$parser( $data );
		error_log( $out );
	}

	public static function file( $data, $file ) {
		$parser = self::get_format();

		$out = '[' . date( 'd-M-Y H:i:s e' ) . '] ';
		$out .= self::$parser( $data );
		error_log( $out, 3, $file );
	}

	public static function get_format() {
		$parser = self::$format;

		if ( !in_array( $parser, self::$available_formats, true ) ) {
			$parser = 'better_print_r';
		}

		return $parser;
	}

	public static function print_r( $data ) {
		$out = print_r( $data, true );
		return $out;
	}

	public static function better_print_r( $data, $level = 0 ) {
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
			case 'object':
				$out .= self::add_object( $data, $level );
				break;
		}

		return $out;
	}

	public static function add_simple( $type, $data, $level ) {
		if ( $type === 'boolean' ) {
			if ( $data === true ) {
				$data = 'true';
			} else {
				$data = 'false';
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
			$out .= str_repeat( ' ', $level * 4 ) . "$k => "
				. ltrim( self::better_print_r( $v, $level ) );
		}

		$level--;
		$out .= str_repeat( ' ', $level * 4 ) . "]\n";
		return $out;
	}

	public static function add_object( $data, $level ) {
		$out = str_repeat( ' ', $level * 4 ) . "(object) " . get_class( $data ) . " {\n";
		$level++;
		$reflect = new ReflectionObject( $data );

		foreach ( $reflect->getProperties() as $k => $v ) {
			$name = $v->getName();

			if ( $v->isStatic() ) {
				$name .= ':static';
			}
			if ( $v->isReadOnly() ) {
				$name .= ':readonly';
			}

			if ( $v->isPrivate() ) {
				$name .= ':private';
			} elseif ( $v->isProtected() ) {
				$name .= ':protected';
			} elseif ( $v->isPublic() ) {
				$name .= ':public';
			}

			$out .= str_repeat( ' ', $level * 4 ) . "$name => "
				. ltrim( self::better_print_r( $v->getValue( $data ), $level ) );
		}

		$level--;
		$out .= str_repeat( ' ', $level * 4 ) . "}\n";
		return $out;
	}
}
