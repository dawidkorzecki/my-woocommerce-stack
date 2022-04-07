<?php
/**
 * Container
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

namespace WP_Grid_Builder\Includes;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Dependency Injection Container
 *
 * @class WP_Grid_Builder\Includes\Container
 * @since 1.0.0
 */
final class Container {

	/**
	 * Holds container instances.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @var array
	 */
	private static $instances = [];

	/**
	 * Optional namespace for abstract class names.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @var array
	 */
	private $namespace = '';

	/**
	 * Holds registered properties
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @var array
	 */
	private $properties = [];

	/**
	 * Holds registered classes
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @var array
	 */
	private $objects = [];

	/**
	 * Holds resolved class
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @var array
	 */
	private $resolved = [];

	/**
	 * Retrieve a specific instance of container
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $slug Slug of the container.
	 * @param string $namespace Optional Namespace.
	 * @return object Instance
	 */
	public static function instance( $slug, $namespace = '' ) {

		if ( ! is_string( $slug ) ) {
			return;
		}

		if ( ! isset( self::$instances[ $slug ] ) ) {

			$class = static::class;
			$class = new $class( $slug, $namespace );

			if ( ! isset( self::$instances[ $slug ] ) || self::$instances[ $slug ] !== $class ) {
				self::$instances[ $slug ] = $class;
			}
		}

		return self::$instances[ $slug ];

	}

	/**
	 * Constructor (altenrative to instance)
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $slug Slug of the container.
	 * @param string $namespace Optional Namespace.
	 */
	public function __construct( $slug = null, $namespace = '' ) {

		if ( empty( $slug ) || ! is_string( $slug ) ) {
			$slug = uniqid( 'wpgb_' );
		}

		$this->namespace = $namespace;
		self::$instances[ $slug ] = $this;

	}

	/**
	 * Destroy instance(s)
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $slugs Holds container slugs to unset.
	 */
	public function destroy( $slugs = null ) {

		if ( ! empty( $slugs ) ) {

			foreach ( (array) $slugs as $slug ) {

				if ( is_string( $slug ) && isset( static::$instances[ $slug ] ) ) {
					unset( static::$instances[ $slug ] );
				}
			}
		} else {
			self::$instances = [];
		}
	}

	/**
	 * Define a service
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $abstract Abstract class name.
	 * @param mixed  $concrete Concrete Class.
	 */
	public function set( $abstract, $concrete = null ) {

		if ( ! is_string( $abstract ) ) {
			return;
		}

		if ( null === $concrete ) {
			$concrete = $this->namespace . $abstract;
		}

		$this->objects[ $abstract ] = $concrete;

		return $this;

	}

	/**
	 * Define a property
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param mixed $key   Property key.
	 * @param mixed $value Property value.
	 */
	public function add( $key, $value = null ) {

		if ( ! is_string( $key ) ) {
			return;
		}

		$this->properties[ $key ] = $value;

		return $this;

	}

	/**
	 * Check if container exist
	 *
	 * @since 1.1.5
	 * @access public
	 *
	 * @param string $slug Slug of the container.
	 * @return boolean
	 */
	public static function has( $slug ) {

		return ! empty( self::$instances[ $slug ] );

	}

	/**
	 * Get property
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param mixed $key Property key.
	 */
	public function prop( $key ) {

		if ( ! is_string( $key ) || ! isset( $this->properties[ $key ] ) ) {
			return;
		}

		return $this->properties[ $key ];

	}

	/**
	 * Get and Instantiate class
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param mixed $abstract   Abstract class name.
	 * @param array $parameters Class parameters.
	 * @return mixed
	 * @throws \Exception Resolve errors.
	 */
	public function get( $abstract, $parameters = [] ) {

		if ( ! is_string( $abstract ) ) {
			return;
		}

		// If not registered.
		if ( ! isset( $this->objects[ $abstract ] ) ) {
			$this->set( $abstract );
		}

		// Get concrete class to resolve.
		$concrete = $this->objects[ $abstract ];

		// If not resolved.
		if ( ! isset( $this->resolved[ $concrete ] ) ) {
			$this->resolve( $concrete, $parameters );
		}

		// Return instantiated class object.
		return $this->resolved[ $concrete ];

	}

	/**
	 * Resolve single
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param mixed $concrete   Concrete class name.
	 * @param array $parameters Class Parameters.
	 * @return object
	 * @throws \Exception Resolve errors.
	 */
	public function resolve( $concrete, $parameters = '' ) {

		$reflector = new \ReflectionClass( $concrete );

		// If class is not instantiable.
		if ( ! $reflector->isInstantiable() ) {
			throw new \Exception( 'Class ' . $concrete . ' is not instantiable' );
		}

		// Get class constructor.
		$constructor = $reflector->getConstructor();

		if ( is_null( $constructor ) ) {
			// Get new instance from class.
			return $reflector->newInstance();
		}

		// Get default constructor parameters.
		if ( empty( $parameters ) ) {

			$parameters = $constructor->getParameters();
			$parameters = $this->get_dependencies( $parameters );

		}

		// Get new instance with dependencies resolved.
		$resolved = $reflector->newInstanceArgs( $parameters );
		$this->resolved[ $concrete ] = $resolved;

		return $resolved;

	}

	/**
	 * Get all dependencies resolved
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $parameters Class parameters.
	 * @return array
	 * @throws \Exception Dependy error.
	 */
	public function get_dependencies( $parameters ) {

		$dependencies = [];

		foreach ( $parameters as $parameter ) {

			// Get the type hinted class.
			$class = $this->get_class( $parameter );

			if ( null === $class ) {

				$value = $this->prop( $parameter->name );

				if ( $parameter->isDefaultValueAvailable() ) {
					// Get default value of parameter.
					$dependencies[] = $parameter->getDefaultValue();
				} elseif ( '' !== $value ) {
					// Get defined parameter from properties.
					$dependencies[] = $value;
				} else {
					throw new \Exception( 'Can not resolve class dependency ' . $parameter->name );
				}
			} else {

				if ( $this->namespace ) {
					$concrete = $class->getShortName();
				} else {
					$concrete = $class->getName();
				}

				// Get dependency resolved.
				$dependencies[] = $this->get( $concrete );
			}
		}

		return $dependencies;

	}

	/**
	 * Get the type hinted class.
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @param array $parameter Class parameter.
	 * @return null|object
	 */
	public function get_class( $parameter ) {

		if ( ! method_exists( $parameter, 'getType' ) ) {
			return $parameter->getClass();
		}

		$type = $parameter->getType();

		if ( ! $type || $type->isBuiltin() ) {
			return null;
		}

		// Because of ReflectionType::__toString() (PHP 7.0.X).
		if ( ! method_exists( $type, 'getName' ) ) {
			return $parameter->getClass();
		}

		return new \ReflectionClass( $type->getName() );

	}
}
