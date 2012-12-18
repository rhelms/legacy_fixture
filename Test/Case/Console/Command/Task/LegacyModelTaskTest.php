<?php
App::uses('Model', 'Model');
App::uses('ConsoleOutput', 'Console');
App::uses('ConsoleInput', 'Console');
App::uses('TemplateTask', 'Console/Command/Task');
App::uses('LegacyModelTask', 'LegacyFixture.Console/Command/Task');
App::uses('LegacyFixtureTask', 'LegacyFixture.Console/Command/Task');

class Article extends Model {

}

class BakeOdd extends Model {
	public $useTable = 'bake_odd';
}

class LegacyModelTaskTest extends CakeTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$out = $this->getMock('ConsoleOutput', array(), array(), '', false);
		$in = $this->getMock('ConsoleInput', array(), array(), '', false);

		$this->Task = $this->getMock('LegacyModelTask',
			array('in', 'err', 'createFile', '_stop', '_checkUnitTest'),
			array($out, $out, $in)
		);
		$this->_setupOtherMocks();
	}

/**
 * Setup a mock that has out mocked.  Normally this is not used as it makes $this->at() really tricky.
 *
 * @return void
 */
	protected function _useMockedOut() {
		$out = $this->getMock('ConsoleOutput', array(), array(), '', false);
		$in = $this->getMock('ConsoleInput', array(), array(), '', false);

		$this->Task = $this->getMock('LegacyModelTask',
			array('in', 'out', 'err', 'hr', 'createFile', '_stop', '_checkUnitTest'),
			array($out, $out, $in)
		);
		$this->_setupOtherMocks();
	}

/**
 * sets up the rest of the dependencies for Legacy Model Task
 *
 * @return void
 */
	protected function _setupOtherMocks() {
		$out = $this->getMock('ConsoleOutput', array(), array(), '', false);
		$in = $this->getMock('ConsoleInput', array(), array(), '', false);

		$this->Task->LegacyFixture = $this->getMock('LegacyFixtureTask', array(), array($out, $out, $in));
		$this->Task->Test = $this->getMock('LegacyFixtureTask', array(), array($out, $out, $in));
		$this->Task->Template = new TemplateTask($out, $out, $in);

		$this->Task->name = 'LegacyModel';
		$this->Task->interactive = true;
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		parent::tearDown();
		unset($this->Task);
	}

	public function testListAllInteractive() {
		$out = $this->getMock('ConsoleOutput', array(), array(), '', false);
		$in = $this->getMock('ConsoleInput', array(), array(), '', false);
		$this->Task = $this->getMock('LegacyModelTask',
			array('out', 'in', 'err', '_stop', '_checkUnitTest', 'getAllModels'),
			array($out, $out, $in)
		);
		$this->_setupOtherMocks();

		$this->Task->connection = 'test';
		$this->Task->path = '/my/path/';
		$this->Task->interactive = true;

		$this->Task->
			expects($this->once())->
			method('getAllModels')->
			will($this->returnValue(array(
				array('name' => 'Article', 'table' => 'articles'),
				array('name' => 'BakeOdd', 'table' => 'bake_odd'))));

		$this->Task->
			expects($this->at(1))->
			method('out')->
			with('Possible Models based on your app/Model directory:');

		$this->Task->
			expects($this->at(2))->
			method('out')->
			with('1. Article');

		$this->Task->
			expects($this->at(3))->
			method('out')->
			with('2. BakeOdd');

		$result = $this->Task->listAll();

		$this->assertEquals(array(
				array('articles', 'bake_odd'),
				array('Article', 'BakeOdd')
			),
			$result
		);

	}

	public function testGetAllModels() {
		$out = $this->getMock('ConsoleOutput', array(), array(), '', false);
		$in = $this->getMock('ConsoleInput', array(), array(), '', false);
		$this->Task = $this->getMock('LegacyModelTask',
			array('in', 'err', '_stop', '_checkUnitTest', 'getModelFilenames'),
			array($out, $out, $in)
		);
		$this->_setupOtherMocks();

		$this->Task->connection = 'test';
		$this->Task->path = '/my/path/';
		$this->Task->interactive = false;
		$this->Task->args = array('BakeOdd');

		$this->Task->expects($this->once())->method('getModelFilenames')->will($this->returnValue(array('Article', 'BakeOdd')));

		$models = $this->Task->getAllModels();

		$this->assertEquals(
			array(array('name' => 'Article', 'table' => 'articles'),
				array('name' => 'BakeOdd', 'table' => 'bake_odd')),
			$models
		);
	}
}
