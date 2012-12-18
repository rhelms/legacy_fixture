<?php
App::uses('ConsoleOutput', 'Console');
App::uses('ConsoleInput', 'Console');
App::uses('BakeShell', 'Console/Command');
App::uses('DbConfigTask', 'Console/Command/Task');
App::uses('ProjectTask', 'Console/Command/Task');
App::uses('LegacyFixtureTask', 'LegacyFixture.Console/Command/Task');

class LegacyFixtureBakeShellTest extends CakeTestCase {

/**
 * setup test
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$out = $this->getMock('ConsoleOutput', array(), array(), '', false);
		$in = $this->getMock('ConsoleInput', array(), array(), '', false);

		$this->Shell = $this->getMock(
			'BakeShell',
			array('in', 'out', 'hr', 'err', 'createFile', '_stop', '_checkUnitTest'),
			array($out, $out, $in)
		);
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		parent::tearDown();
		unset($this->Dispatch, $this->Shell);
	}

/**
 * test LegacyFixtureBake.legacyFixture
 */
	public function testMain() {

		$this->markTestIncomplete('Can not unit test due to is_dir call in main(). Will wait and see if BakeShell::main() is ever until tested.');

		$this->Shell->LegacyFixture = $this->getMock('LegacyFixtureTask', array(), array(&$this->Dispatcher));
		$this->Shell->DbConfig = $this->getMock('DbConfigTask', array(), array(&$this->Dispatcher));
		$this->Shell->Project = $this->getMock('ProjectTask', array(), array(&$this->Dispatcher));

		$this->Shell->DbConfig->expects($this->once())
		->method('getConfig')
		->will($this->returnValue('test'));

		$this->Shell->LegacyFixture->expects($this->once())
		->method('execute');

		$this->Shell->connection = 'test';
		$this->Shell->params = array();
		$this->Shell->args = array();
		$this->Shell->main();

	}
}