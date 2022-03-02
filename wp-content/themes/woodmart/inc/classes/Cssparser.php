<?php
/**
 * CSS parser class.
 *
 * @package xts
 */

namespace XTS;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

// phpcs:disable

/**
 * CSS parser class.
 *
 * @since 1.0.0
 */
class CSSParser extends Singleton {
	protected static $cssCounter  = 0;
	protected static $propCounter = 0;
	protected $cssData;

	function init() {
		$this->cssData = array();
	}

	public function ParseCSS( $css ) {
		$index                   = ++self::$cssCounter;
		$this->cssData[ $index ] = array(
			'all'    => array(),
			'screen' => array(),
			'print'  => array(),
		);
		return $this->ParseCode( $index, $css );
	}

	protected function ParseCode( $index, $css ) {
		$currentMedia = 'all';
		$mediaList    = array();
		$section      = false;
		$css          = trim( $css );
		if ( strlen( $css ) == 0 ) {
			return $index;
		}
		$css = preg_replace( '/\/\*.*\*\//Us', '', $css );
		while ( preg_match( '/^\s*(\@(media|import|local)([^\{\}]+)(\{)|([^\{\}]+)(\{)|([^\{\}]*)(\}))/Usi', $css, $match ) ) {
			if ( isset( $match[8] ) && ( $match[8] == '}' ) ) {
				if ( $section !== false ) {
					$code        = trim( $match[7] );
					$idx         = 0;
					$inQuote     = false;
					$property    = false;
					$codeLen     = strlen( $code );
					$parenthesis = array();
					while ( $idx < $codeLen ) {
						$c = isset( $code[ $idx ] ) ? $code[ $idx ] : '';
						$idx++;
						if ( $inQuote !== false ) {
							if ( $inQuote === $c ) {
								$inQuote = false;
							}
						} elseif ( ( $inQuote === false ) && ( $c == '(' ) ) {
							array_push( $parenthesis, '(' );
						} elseif ( ( $inQuote === false ) && ( $c == ')' ) ) {
							array_pop( $parenthesis );
						} elseif ( ( $c == '\'' ) || ( $c == '"' ) ) {
							$inQuote = $c;
						} elseif ( ( $property === false ) && ( $c == ':' ) ) {
							$property = trim( substr( $code, 0, $idx - 1 ) );
							if ( preg_match( '/^(.*)\[([0-9]*)\]$/Us', $property, $propMatch ) ) {
								$property             = $propMatch[1] . '[' . static::$propCounter . ']';
								static::$propCounter += 1;
							}
							$code = substr( $code, $idx );
							$idx  = 0;
						} elseif ( ( count( $parenthesis ) == 0 ) && ( $c == ';' ) ) {
							$value = trim( substr( $code, 0, $idx - 1 ) );
							$code  = substr( $code, $idx );
							$idx   = 0;
							$this->AddProperty( $index, $currentMedia, $section, $property, $value );
							$property = false;
						}
					}
					if ( ( $idx > 0 ) && ( $property !== false ) ) {
						$value = trim( $code );
						$this->AddProperty( $index, $currentMedia, $section, $property, $value );
					}
					$section = false;
				} elseif ( count( $mediaList ) > 0 ) {
					array_pop( $mediaList );
					if ( count( $mediaList ) > 0 ) {
						$currentMedia = end( $mediaList );
					} else {
						$currentMedia = 'all';
					}
				}
			} elseif ( isset( $match[6] ) && ( $match[6] == '{' ) ) {
				// Section
				$section = trim( $match[5] );
				if ( ! isset( $this->cssData[ $index ][ $currentMedia ][ $section ] ) ) {
					$this->cssData[ $index ][ $currentMedia ][ $section ] = array();
				}
			} elseif ( isset( $match[4] ) && ( $match[4] == '{' ) ) {
				if ( $match[2] == 'media' ) {
					// New media
					$media        = trim( $match[3] );
					$mediaList[]  = $media;
					$currentMedia = $media;
					if ( ! isset( $this->cssData[ $index ][ $currentMedia ] ) ) {
						$this->cssData[ $index ][ $currentMedia ] = array();
					}
				}
			}
			$stripCount = strlen( $match[0] );
			$css        = trim( substr( $css, $stripCount ) );
		}

		return $index;
	}

	public function AddProperty( $index, $media, $section, $property, $value ) {
		if ( ! isset( $this->cssData[ $index ] ) ) {
			$this->cssData[ $index ] = array(
				'all'    => array(),
				'screen' => array(),
				'print'  => array(),
			);
		}
		$media = trim( $media );
		if ( $media == '' ) {
			$media = 'all';
		}
		$section  = trim( $section );
		$property = trim( $property );
		if ( strlen( $property ) > 0 ) {
			$value = trim( $value );
			if ( $media == 'all' ) {
				$this->cssData[ $index ][ $media ][ $section ][ $property ] = $value;
				$keys = array_keys( $this->cssData[ $index ] );
				foreach ( $keys as $key ) {
					if ( ! isset( $this->cssData[ $index ][ $key ][ $section ] ) ) {
						$this->cssData[ $index ][ $key ][ $section ] = array();
					}
					$this->cssData[ $index ][ $key ][ $section ][ $property ] = $value;
				}
			} else {
				if ( ! isset( $this->cssData[ $index ][ $media ] ) ) {
					$this->cssData[ $index ][ $media ] = $this->cssData[ $index ]['all'];
				}
				if ( ! isset( $this->cssData[ $index ][ $media ][ $section ] ) ) {
					$this->cssData[ $index ][ $media ][ $section ] = array();
				}
				$this->cssData[ $index ][ $media ][ $section ][ $property ] = $value;
			}
		}
	}

	public function GetCSS( $index, $media = 'screen', $forbiddenKeys = array() ) {
		if ( isset( $this->cssData[ $index ] ) ) {
			if ( ! isset( $this->cssData[ $index ][ $media ] ) ) {
				$media = 'all';
				if ( ! isset( $this->cssData[ $index ][ $media ] ) ) {
					return false;
				}
			}

			if ( is_array( $this->cssData[ $index ][ $media ] ) ) {
				$result = '';
				foreach ( $this->cssData[ $index ][ $media ] as $section => $values ) {
					$result .= $section . ' {';
					$result .= "\n";
					if ( is_array( $values ) ) {
						foreach ( $values as $key => $value ) {
							$skipThis = false;
							foreach ( $forbiddenKeys as $fKey ) {
								if ( preg_match( '/' . $fKey . '/Usi', $key ) ) {
									$skipThis = true;
									break;
								}
							}
							if ( $skipThis ) {
								continue;
							}
							$result .= '  ';
							$key     = preg_replace( '/(\[[0-9]*\])$/Usi', '', $key );
							$result .= $key . ': ' . $value . ';';
							$result .= "\n";
						}
					}
					$result .= '}';
					$result .= "\n";
				}
				return $result;
			}
		}
		return false;
	}
}

