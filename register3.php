<?php

class ZKService {
  private $zk;
  private $base_path = "/services/group";
	private $params = array(array(
			'perms' => Zookeeper::PERM_ALL,
			'scheme' => 'world',
			'id' => 'anyone'
			));

	public function __construct() {
		$this->zk = new Zookeeper("localhost:2181");
		if(!$this->zk->exists($this->base_path)) {
			$this->zk->create($this->base_path, '', $this->params);
		} 
	}
	
	public function register($address) {
	  $this->zk->create($this->base_path . "/node-", $address, $this->params, Zookeeper::SEQUENCE|Zookeeper::EPHEMERAL);
	}
}

$zks = new ZKService();
$zks->register("tcp://localhost:" . rand(1025, 65534));
$count = 10;
while($count > 0) {
  echo $count--, "\n";
  sleep(1);
}