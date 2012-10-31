<?php

class ZKWatcher {
  private $base_path = "/services/group";
  private $params = array(array(
			'perms' => Zookeeper::PERM_ALL,
			'scheme' => 'world',
			'id' => 'anyone'
			));
	private $zk;

	public function __construct() {
		$this->zk = new Zookeeper("localhost:2181");
		if(!$this->zk->exists($this->base_path)) {
			$this->zk->create($this->base_path, '', $this->params);
		}
	}

	public function children_cb($event, $key, $path) {
		echo "\nChildren for $path\n";
		$children = $this->zk->getChildren($path, array($this, 'children_cb'));
		var_dump($children);
	}

	public function wait() {
		while(true) {
			sleep(10);
		}
	}

}

$zkw = new ZKWatcher();
$zkw->children_cb(null, null, '/services/group');
$zkw->wait(); 
