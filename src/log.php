<?php
declare( strict_types = 1 );

class Log {
	public $stringify = 'stringify_print_r';

	public function __construct() {
	}

	public static function __callStatic( string $method, array $args ) {
		$log = new Log();
		$response = $log->send(
			to: $method,
			msg: $args[0]
		);

		return $response;
	}

	public function stringify_print_r( $msg ) {
		$out = print_r( $msg, true );
		return $out;
	}

	public function stringify_var_export( $msg ) {
		$out = var_export( $msg, true );
		return $out;
	}

	public function send( string $to, $msg ) {
		$response = [
			'error' => false,
			'msg' => ''
		];

		$make_string = $this->stringify;
		$msg = $this->$make_string( $msg );

		$to = "send_$to";
		$response['error'] = $this->$to( $msg );

		return $response;
	}

	public function send_php( $msg ) {
		return error_log( $msg, 0 );
	}
}
