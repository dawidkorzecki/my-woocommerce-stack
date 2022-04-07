<?php
/**
 * Class PredefinedScenario
 *
 * @package WPDesk\FS\TableRate\Rule\PreconfiguredScenarios
 */

namespace WPDesk\FS\TableRate\Rule\PreconfiguredScenarios;

/**
 * Predefined scenario.ś
 */
class PredefinedScenario implements \JsonSerializable {

	/**
	 * @var string
	 */
	private $category;

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var string
	 */
	private $description;

	/**
	 * @var string
	 */
	private $documentation_url;

	/**
	 * @var string
	 */
	private $rules_json;

	/**
	 * @var bool
	 */
	private $available;

	/**
	 * @var string
	 */
	private $reason_for_unavailability;

	/**
	 * @var string
	 */
	private $requirements;

	/**
	 * PredefinedScenario constructor.
	 *
	 * @param string $category .
	 * @param string $name .
	 * @param string $description .
	 * @param string $documentation_url .
	 * @param string $rules_json .
	 * @param bool   $available .
	 * @param string $reason_for_unavailability .
	 * @param string $requirements .
	 */
	public function __construct( $category, $name, $description, $documentation_url, $rules_json, $available = true, $reason_for_unavailability = '', $requirements = '' ) {
		$this->category                  = $category;
		$this->name                      = $name;
		$this->description               = $description;
		$this->documentation_url         = $documentation_url;
		$this->rules_json                = $rules_json;
		$this->available                 = $available;
		$this->reason_for_unavailability = $reason_for_unavailability;
		$this->requirements              = $requirements;
	}

	/**
	 * @return string
	 */
	public function get_category() {
		return $this->category;
	}

	/**
	 * @return string
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function get_description() {
		return $this->description;
	}

	/**
	 * @return string
	 */
	public function get_documentation_url() {
		return $this->documentation_url;
	}

	/**
	 * @return string
	 */
	public function get_rules_json() {
		return $this->rules_json;
	}

	/**
	 * @return bool
	 */
	public function is_available(): bool {
		return $this->available;
	}

	/**
	 * @return string
	 */
	public function get_reason_for_unavailability(): string {
		return $this->reason_for_unavailability;
	}

	/**
	 * @return string
	 */
	public function get_requirements(): string {
		return $this->requirements;
	}

	/**
	 * @return array
	 */
	public function jsonSerialize(): array {
		return [
			'category'                  => $this->category,
			'name'                      => $this->name,
			'description'               => $this->description,
			'documentation_url'         => $this->documentation_url,
			'rules_json'                => $this->rules_json,
			'available'                 => $this->available,
			'reason_for_unavailability' => $this->reason_for_unavailability,
			'requirements'              => $this->requirements,
			'rules_count'               => $this->get_rules_count(),
		];
	}

	/**
	 * @return int
	 */
	private function get_rules_count() {
		$rules = json_decode( $this->rules_json, true );

		return count( $rules );
	}

}
