<?php
App::uses('Model', 'Model');
App::uses('ConsoleOutput', 'Console');
App::uses('ConsoleInput', 'Console');
App::uses('TemplateTask', 'Console/Command/Task');
App::uses('DbConfigTask', 'Console/Command/Task');
App::uses('LegacyModelTask', 'LegacyFixture.Console/Command/Task');
App::uses('LegacyFixtureTask', 'LegacyFixture.Console/Command/Task');

class LegacyFixtureTaskTest extends CakeTestCase {

	public $fixtures = array('plugin.legacy_fixture.bake_odd', 'core.article', 'core.articles_tag');

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$out = $this->getMock('ConsoleOutput', array(), array(), '', false);
		$in = $this->getMock('ConsoleInput', array(), array(), '', false);

		$this->Task = $this->getMock('LegacyFixtureTask',
			array('in', 'err', 'createFile', '_stop', 'clear'),
			array($out, $out, $in)
		);
		$this->Task->LegacyModel = $this->getMock('LegacyModelTask',
			array('in', 'out', 'err', 'createFile', 'getName', 'getTable', 'listAll'),
			array($out, $out, $in)
		);
		$this->Task->Template = new TemplateTask($out, $out, $in);
		$this->Task->DbConfig = $this->getMock('DbConfigTask', array(), array($out, $out, $in));
		$this->Task->Template->initialize();
	}


/**
 * sets up the rest of the dependencies for Legacy Fixture Task
 *
 * @return void
 */
	protected function _setupOtherMocks() {
		$out = $this->getMock('ConsoleOutput', array(), array(), '', false);
		$in = $this->getMock('ConsoleInput', array(), array(), '', false);

		$this->Task->LegacyModel = $this->getMock('LegacyModelTask', array(), array($out, $out, $in));
		$this->Task->Template = new TemplateTask($out, $out, $in);

		$this->Task->name = 'LegacyFixture';
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

/**
 * Test all() for regular and odd table names
 */
	public function testAll() {
		$out = $this->getMock('ConsoleOutput', array(), array(), '', false);
		$in = $this->getMock('ConsoleInput', array(), array(), '', false);

		$this->Task = $this->getMock('LegacyFixtureTask',
			array('in', 'err', 'createFile', '_stop', 'clear', 'bake'),
			array($out, $out, $in)
		);

		$this->_setupOtherMocks();

		$this->Task->connection = 'test';
		$this->Task->path = '/my/path/';
		$this->Task->LegacyModel->
			expects($this->once())->
			method('listAll')->
			will($this->returnValue(array(
				array('articles', 'bake_odd'),
				array('Article', 'BakeOdd')
			)));

		$this->Task->expects($this->at(0))->
			method('bake')->
			with('Article');

		$this->Task->expects($this->at(1))->
			method('bake')->
			with('BakeOdd', 'bake_odd');

		$this->Task->all();

	}

/**
 * Test bake fixture all for regular and odd models
 */
	public function testExecuteIntoAll() {
		$this->Task->connection = 'test';
		$this->Task->path = '/my/path/';
		$this->Task->args = array('all');
		$this->Task->LegacyModel->expects($this->any())
		->method('listAll')
		->will($this->returnValue(array(
				array('articles', 'bake_odd'),
				array('Article', 'BakeOdd')
			)));

		$filename = '/my/path/ArticleFixture.php';
		$this->Task->expects($this->at(0))
		->method('createFile')
		->with($filename, $this->stringContains('class ArticleFixture'));

		$filename = '/my/path/BakeOddFixture.php';
		$this->Task->expects($this->at(1))
		->method('createFile')
		->with($filename, $this->stringContains('public $table = \'bake_odd\';'));

		$this->Task->execute();
	}

/**
 * Test bake fixture for the interactive shell and an odd table name
 */
	public function testExecuteInteractive() {
		$this->Task->connection = 'test';
		$this->Task->path = '/my/path/';

		$this->Task->expects($this->any())->method('in')->will($this->returnValue('y'));
		$this->Task->LegacyModel->expects($this->any())->method('getName')->will($this->returnValue('BakeOdd'));
		$this->Task->LegacyModel->expects($this->any())->method('getTable')
		->with('BakeOdd')
		->will($this->returnValue('bake_odd'));

		$filename = '/my/path/BakeOddFixture.php';
		$this->Task->expects($this->once())->method('createFile')
		->with($filename, $this->stringContains('public $table = \'bake_odd\';'));

		$this->Task->execute();
	}

/**
 * Test bake fixture <table> for a normal table name
 */
	public function testExecuteWithNamedTable() {
		$this->Task->connection = 'test';
		$this->Task->path = '/my/path/';
		$this->Task->args = array('articles');
		$this->Task->LegacyModel->expects($this->any())
		->method('listAll')
		->will($this->returnValue(array(
			array('articles', 'bake_odd'),
			array('Article', 'BakeOdd')
		)));
		$filename = '/my/path/ArticleFixture.php';

		$this->Task->expects($this->once())->method('createFile')
		->with($filename, $this->stringContains('class ArticleFixture'));
		$this->Task->execute();
	}

/**
 * Test bake fixture <Model> for a normal model name
 */
	public function testExecuteWithNamedModel() {
		$this->Task->connection = 'test';
		$this->Task->path = '/my/path/';
		$this->Task->args = array('Article');
		$this->Task->LegacyModel->expects($this->any())
		->method('listAll')
		->will($this->returnValue(array(
			array('articles', 'bake_odd'),
			array('Article', 'BakeOdd')
		)));
		$filename = '/my/path/ArticleFixture.php';

		$this->Task->expects($this->once())->method('createFile')
		->with($filename, $this->stringContains('class ArticleFixture'));
		$this->Task->execute();
	}

/**
 * Test bake fixture <table> for an odd table name
 */
	public function testExecuteWithOddNamedTable() {
		$this->Task->connection = 'test';
		$this->Task->path = '/my/path/';
		$this->Task->args = array('bake_odd');
		$this->Task->LegacyModel->expects($this->any())
		->method('listAll')
		->will($this->returnValue(array(
			array('articles', 'bake_odd'),
			array('Article', 'BakeOdd')
		)));
		$filename = '/my/path/BakeOddFixture.php';

		$this->Task->expects($this->once())->method('createFile')
		->with($filename, $this->stringContains('public $table = \'bake_odd\';'));
		$this->Task->execute();
	}

/**
 * Test bake fixture <Model> for an odd model name
 */
	public function testExecuteWithOddNamedModel() {
		$this->Task->connection = 'test';
		$this->Task->path = '/my/path/';
		$this->Task->args = array('BakeOdd');
		$this->Task->LegacyModel->expects($this->any())
		->method('listAll')
		->will($this->returnValue(array(
			array('articles', 'bake_odd'),
			array('Article', 'BakeOdd')
		)));
		$filename = '/my/path/BakeOddFixture.php';

		$this->Task->expects($this->once())->method('createFile')
		->with($filename, $this->stringContains('public $table = \'bake_odd\';'));
		$this->Task->execute();
	}

/**
 * Test for a model that does not exist on file, but does in database
 *
 * This is the default functionality for bake fixture <table>
 */
	public function testExecuteWithUnknownNamedModel() {
		$this->Task->connection = 'test';
		$this->Task->path = '/my/path/';
		$this->Task->args = array('articles_tags');
		$this->Task->LegacyModel->expects($this->any())
		->method('listAll')
		->will($this->returnValue(array(
			array('articles', 'bake_odd'),
			array('Article', 'BakeOdd')
		)));
		$filename = '/my/path/ArticlesTagFixture.php';

		$this->Task->expects($this->once())->method('createFile')
		->with($filename, $this->stringContains('class ArticlesTagFixture'));
		$this->Task->execute();
	}
}
