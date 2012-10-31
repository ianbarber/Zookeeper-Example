<?php

class ZKService {
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
	
	public function register($service_name, $address) {
	  $this->zk->create("/services/" . $service_name, $address, $this->params);
	}
  
}

$zks = new ZKService();
$zks->register("counter", "tcp://localhost:1337");