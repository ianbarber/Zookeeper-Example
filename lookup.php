<?php

class ZKLookup {
  private $zk;
	private $params = array(array(
			'perms' => Zookeeper::PERM_ALL,
			'scheme' => 'world',
			'id' => 'anyone'
			));

	public function __construct() {
		$this->zk = new Zookeeper("localhost:2181");
		if(!$this->zk->exists("/services")) {
			$this->zk->create("/services", '', $this->params);
		}
	}
	
	public function lookup($service_name) {
	  $path = "/services/" . $service_name;
	  if(!$this->zk->exists($path)) {
	    return false;
	  } else {
	    return $this->zk->get($path);
	  }
	}
  
}

$zks = new ZKLookup();
var_dump($zks->lookup("counter"));