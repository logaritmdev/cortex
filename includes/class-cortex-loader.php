<?php

/**
 * Register all actions and filters for the plugin.
 * Maintain a list of all hooks that are registered throughout
 * the plugin, and register them with the WordPress API. Call the
 * run function to execute the list of actions and filters.
 * @class Cortex_Loader
 * @since 0.1.0
 */
class Cortex_Loader {

	//--------------------------------------------------------------------------
	// Properties
	//--------------------------------------------------------------------------

	/**
	 * The array of actions registered with WordPress.
	 * @property actions
	 * @since 0.1.0
	 */
	protected $actions;

	/**
	 * The array of filters registered with WordPress.
	 * @property filters
	 * @since 0.1.0
	 */
	protected $filters;

	//--------------------------------------------------------------------------
	// Methods
	//--------------------------------------------------------------------------

	/**
	 * Initialize the collections used to maintain the actions and filters.
	 * @constructor
	 * @since 0.1.0
	 */
	public function __construct() {
		$this->actions = array();
		$this->filters = array();
	}

	/**
	 * Add a new action to the collection to be registered with WordPress.
	 * @method add_action
	 * @since 0.1.0
	 */
	public function add_action($hook, $component, $callback, $priority = 10, $accepted_args = 1) {
		$this->actions = $this->add($this->actions, $hook, $component, $callback, $priority, $accepted_args);
	}

	/**
	 * Add a new filter to the collection to be registered with WordPress.
	 * @method add_filter
	 * @since 0.1.0
	 */
	public function add_filter($hook, $component, $callback, $priority = 10, $accepted_args = 1) {
		$this->filters = $this->add($this->filters, $hook, $component, $callback, $priority, $accepted_args);
	}

	/**
	 * A utility function that is used to register the actions.
	 * @method add
	 * @since 0.1.0
	 */
	private function add($hooks, $hook, $component, $callback, $priority, $accepted_args) {

		$hooks[] = array(
			'hook'          => $hook,
			'component'     => $component,
			'callback'      => $callback,
			'priority'      => $priority,
			'accepted_args' => $accepted_args
		);

		return $hooks;
	}

	/**
	 * Register the filters and actions with WordPress.
	 * @method run
	 * @since 0.1.0
	 */
	public function run() {

		foreach ($this->filters as $hook) {
			add_filter($hook['hook'], array($hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args']);
		}

		foreach ($this->actions as $hook ) {
			add_action( $hook['hook'], array($hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args']);
		}
	}
}
