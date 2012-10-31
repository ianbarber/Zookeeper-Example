<?php

class ZKLeader {
	private $zk;
	private $id;
	private $params = array(array(
			'perms' => Zookeeper::PERM_ALL,
			'scheme' => 'world',
			'id' => 'anyone'
			));
	private $is_leader = false;

	public function __construct() {
		$this->zk = new Zookeeper("localhost:2181");
		if(!$this->zk->exists("/writers")) {
			$this->zk->create("/writers", '', $this->params);
		}
	}

	public function elect() {
		// First we create ourselves an ephemeral node with a sequence
		$node = $this->zk->create("/writers/node-", '', $this->params, Zookeeper::SEQUENCE|Zookeeper::EPHEMERAL); 
		list($path, $this->id) = explode("node-", $node); 
		$this->check_my_status();
	}

	private function check_my_status() {
		echo "Checking my status...\n";
		$children = $this->zk->getChildren("/writers");
		// Check for existance of node with lower id
		$best_id = -1;
		foreach($children as $c) {
			list($path, $id) = explode("node-", $c);
			if(intval($id) < intval($this->id) && intval($id) > intval($best_id)) {
				$best_id = $id;
			}
		}

		if($best_id == -1) {
			// If no node, I am the writer! go ahead
			echo "I am the leader!\n";
			$this->is_leader = true;
		} else {
			// Register the callback and keep waiting
			if(!$this->zk->exists("/writers/node-" . $best_id, array($this, 'exists_cb'))) {
				$this->check_my_status();
			}
		}
	}

	public function exists_cb($type, $state, $path) {
		// The next person has died!
		$this->check_my_status();
	}

	public function doWork() {
	  while(true) {
  	  if($this->is_leader) {
  	    for($i = 0; $i<15; $i++) {
    	    sleep(1);
    			echo rand(1, 100), "\n";
    	  }
    	  echo "Mein lieben!\n";
  			die();
  		} else {
		    sleep(1);
		    echo "Waiting...\n";
  		}
	  }
	}
}


sleep(rand(3, 6));
echo "Starting...\n";
$zkl = new ZKLeader();
$zkl->elect();
$zkl->doWork();
