<?php
App::uses('FixtureTask', 'Console/Command/Task');

class LegacyFixtureTask extends FixtureTask {

/**
 * Tasks to be loaded by this Task
 *
 * @var array
 */
	public $tasks = array('DbConfig', 'LegacyFixture.LegacyModel', 'Template');

/**
 * Bake All the Fixtures at once.  Will only bake fixtures for models that exist.
 *
 * @return void
 */
	public function all() {
		list($tables, $models) = $this->LegacyModel->listAll(null);
		foreach ($tables as $i => $table) {
			if ($table == Inflector::tableize($models[$i])) {
				$this->bake($models[$i]);
			} else {
				$this->bake($models[$i], $table);
			}
		}
	}

/**
 * Bypass FixtureTask::execute, and detect if the supplied argument
 * is for a table or Model, and bake accordingly
 *
 * @see FixtureTask::execute()
 */
	public function execute() {
		BakeTask::execute();
		if (empty($this->args)) {
			$this->_interactive();
		}

		if (isset($this->args[0])) {
			$this->interactive = false;
			if (!isset($this->connection)) {
				$this->connection = 'default';
			}
			if (strtolower($this->args[0]) == 'all') {
				return $this->all();
			}
			list($tables, $models) = $this->LegacyModel->listAll(null);
			if (($key = array_search($this->args[0], $tables)) !== false) {
				if ($this->args[0] == Inflector::tableize($models[$key])) {
					$this->bake($models[$key]);
				} else {
					$this->bake($models[$key], $tables[$key]);
				}
			} elseif (($key = array_search($this->args[0], $models)) !== false) {
				if ($tables[$key] == Inflector::tableize($this->args[0])) {
					$this->bake($models[$key]);
				} else {
					$this->bake($models[$key], $tables[$key]);
				}
			} else {
				$model = $this->_modelName($this->args[0]);
				$this->bake($model);
			}
		}
	}
/**
 * Interactive baking function
 *
 * @return void
 */
	protected function _interactive() {
		$this->DbConfig->interactive = $this->LegacyModel->interactive = $this->interactive = true;
		$this->hr();
		$this->out(__d('cake_console', "Bake Fixture\nPath: %s", $this->getPath()));
		$this->hr();

		if (!isset($this->connection)) {
			$this->connection = $this->DbConfig->getConfig();
		}
		$modelName = $this->LegacyModel->getName($this->connection);
		$useTable = $this->LegacyModel->getTable($modelName, $this->connection);
		$importOptions = $this->importOptions($modelName);
		$this->bake($modelName, $useTable, $importOptions);
	}

	/**
	 * get the option parser.
	 *
	 * @return void
	 */
	public function getOptionParser() {
		// bypass the Fixture options
		$parser = Shell::getOptionParser();
		return $parser->description(
			__d('legacy_fixture_bake', 'Generate legacy fixtures for use with the test suite. You can use `bake legacy_fixture all` to bake all fixtures for existing models.')
		)->addArgument('name', array(
			'help' => __d('cake_console', 'Name of the fixture to bake. Can use Plugin.name to bake plugin fixtures.')
		))->addOption('count', array(
			'help' => __d('cake_console', 'When using generated data, the number of records to include in the fixture(s).'),
			'short' => 'n',
			'default' => 10
		))->addOption('connection', array(
			'help' => __d('cake_console', 'Which database configuration to use for baking.'),
			'short' => 'c',
			'default' => 'default'
		))->addOption('plugin', array(
			'help' => __d('cake_console', 'CamelCased name of the plugin to bake fixtures for.'),
			'short' => 'p',
		))->addOption('records', array(
			'help' => __d('cake_console', 'Used with --count and <name>/all commands to pull [n] records from the live tables, where [n] is either --count or the default of 10'),
			'short' => 'r',
			'boolean' => true
		))->epilog(__d('cake_console', 'Omitting all arguments and options will enter into an interactive mode.'));
	}

}