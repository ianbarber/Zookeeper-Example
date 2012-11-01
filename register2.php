<?php

class ZKService {
  private $zk;
  private $base_path = "/services";
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
	
	public function register($service_name, $address) {
	  $this->zk->create($this->base_path . $service_name, $address, $this->params, Zookeeper::EPHEMERAL);
	}
	
	public function cleanup($service_name) {
	  $path = $this->base_path . $service_name;
	  if($this->zk->exists($path)) {
	    $this->zk->delete($path);
	  }
	}
  
}

$zks = new ZKService();
$zks->cleanup("/counter");
$zks->register("/counter", "tcp://otherhost:1338");
$count = 10;
while($count > 0) {
  echo $count--, "\n";
  sleep(1);
}