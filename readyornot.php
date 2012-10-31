<?php

class ZKReady {
	private $zk;
	private $params = array(array(
                        'perms' => Zookeeper::PERM_ALL,
                        'scheme' => 'world',
                        'id' => 'anyone'
                        ));
	private $stages = 3; // Could come from ZK!

	public function __construct() {
		$this->zk = new Zookeeper("localhost:2181");
		if(!$this->zk->exists("/workflow")) {
			$this->zk->create("/workflow", '', $this->params);
		}
		if($this->zk->exists("/workflow/go")) {
			$this->zk->delete("/workflow/go");
		}
	}
	
	public function process() {
		$can_start = $this->zk->exists("/workflow/go", array($this, "execute"));
		if($can_start) {
			$this->execute(null, null, null);
			return;
		} 

		$this->zk->create("/workflow/node-" . uniqid(), '', $this->params, Zookeeper::EPHEMERAL);
		$chillens = $this->zk->getChildren("/workflow");
		if(count($chillens) >= $this->stages) {
		  // Note: no node value
			$this->zk->create("/workflow/go", '', $this->params);
		}
	}

	public function execute($type, $state, $path) {
		echo "Shut up and take my CPU cycles!\n";
		sleep(1);
		exit();
	}
}

$zkr = new ZKReady();
$zkr->process();
while(true) {
	sleep(100);
}
