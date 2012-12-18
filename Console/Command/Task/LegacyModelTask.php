<?php
App::uses('ModelTask', 'Console/Command/Task');
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');
App::uses('Hash', 'Utility');

class LegacyModelTask extends ModelTask {

/**
 * Outputs a list of possible models from the app.Models directory
 *
 * @param string $useDbConfig Optional, unused database configuration name
 * @return array Array of tables and model names
 * @see ModelTask::listAll()
 */
	public function listAll($useDbConfig = null) {
		$models = (array)$this->getAllModels();
		$this->_modelNames = array();
		$this->_tables = array();
		$count = count($models);
		for ($i = 0; $i < $count; $i++) {
			if ($models[$i]['table'] !== false) {
				$this->_tables[] = $models[$i]['table'];
				$this->_modelNames[] = $models[$i]['name'];
			}
		}

		if ($this->interactive === true) {
			$this->out(__d('cake_console', 'Possible Models based on your app/Model directory:'));
			$len = strlen($count + 1);
			for ($i = 0; $i < $count; $i++) {
				$this->out(sprintf("%${len}d. %s", $i + 1, $this->_modelNames[$i]));
			}
		}
		return array($this->_tables, $this->_modelNames);
	}

/**
 * Get model filenames from the file system
 *
 * @return multitype: Return an array of filenames representing models, with extensions removed
 */
	protected function getModelFilenames() {
		if (isset($this->plugin)) {
			$path = $this->_pluginPath($this->plugin) . 'Model';
		} else {
			$path = APP . 'Model';
		}

		$folder = new Folder($path);
		list($dirs, $filenames) = $folder->read(true, true, true); // sorted, exclude . and .., return full paths

		$filenames = array_filter($filenames, array($this, 'filterModelFiles'));
		array_walk($filenames, array($this, 'stripExt'));

		return $filenames;
	}

/**
 * An array_filter callback to only include files with a php extension
 *
 * @param string $filename The filename to filter
 * @return boolean Return true if the filename has a php extension
 */
	protected function filterModelFiles($filename) {
		$file = new File($filename);
		if ($file->ext() == 'php') {
			return true;
		}
		return false;
	}

/**
 * An array_walk callback to strip the extension from a file
 *
 * @param string $item Reference to the item to be altered
 * @param mixed $key Key of the item to be altered
 */
	protected function stripExt(&$item, $key) {
		$file = new File($item);
		$item = $file->name();
	}

/**
 * Get all models from the application or plugin Model path
 *
 * @return multitype:multitype:NULL Return array of array, with keys for model name and table
 */
	public function getAllModels() {
		$models = array();
		$filenames = $this->getModelFilenames();
		foreach ($filenames as $filename) {
			if (($model = ClassRegistry::init(Inflector::camelize($filename))) !== false) {
				$models[] = array('name' => $model->name, 'table' => $model->table);
				ClassRegistry::removeObject($filename);
			}
		}
		return $models;
	}
}