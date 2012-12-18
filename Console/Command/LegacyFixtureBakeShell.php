<?php
App::uses('AppShell', 'Console/Command');

class LegacyFixtureBakeShell extends AppShell {

/**
 * Contains tasks to load an instantiate
 *
 * @var array
 */
	public $tasks = array('Project', 'DbConfig', 'LegacyFixture.LegacyFixture');

/**
 * The connection being used.
 *
 * @var string
 */
	public $connection = 'default';

/**
 * Assign $this->connection to the active task if a connection param is set.
 *
 * @return void
 */
	public function startup() {
		parent::startup();
		Configure::write('debug', 2);
		Configure::write('Cache.disable', 1);

		$task = Inflector::classify($this->command);
		if (isset($this->{$task}) && !in_array($task, array('Project', 'DbConfig'))) {
			if (isset($this->params['connection'])) {
				$this->{$task}->connection = $this->params['connection'];
			}
		}
	}

/**
 * Override main() to handle action
 *
 * Only do bake for legacy fixtures
 *
 * @return mixed
 * @see BakeShell::main()
 */
	public function main() {
		if (!is_dir($this->DbConfig->path)) {
			$path = $this->Project->execute();
			if (!empty($path)) {
				$this->DbConfig->path = $path . 'Config' . DS;
			} else {
				return false;
			}
		}

		if (!config('database')) {
			$this->out(__d('cake_console', 'Your database configuration was not found. Take a moment to create one.'));
			$this->args = null;
			return $this->DbConfig->execute();
		}
		$this->hr();
		$this->LegacyFixture->execute();
		$this->hr();
		$this->main();
	}

	public function getOptionParser() {
		$parser = parent::getOptionParser();
		return $parser->description(__d('legacy_fixture_bake',
			'The Legacy Fixture Bake script generates fixtures based on existing models in your application.' .
			' If run with no command line arguments, Legacy Fixtire Bake guides the user through the fixture creation process.' .
			' You can customize the generation process by telling Bake where different parts of your application are using command line arguments.'
		))->addSubcommand('legacy_fixture', array(
			'help' => __d('legacy_fixture_bake', 'Bake a legacy fixture.'),
			'parser' => $this->LegacyFixture->getOptionParser()
		));
	}
}