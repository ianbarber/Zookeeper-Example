<?php

$params = array(array(
		'perms' => Zookeeper::PERM_ALL,
		'scheme' => 'world',
		'id' => 'anyone'
		));

$servers = array(
  "192.168.12.10:2181", 
  "192.168.12.11:2181", 
  "192.168.12.12:2181"
);

$zk = new Zookeeper(implode(",", $servers));

if($zk->exists("/test")) {
  $zk->delete("/test");
}

$zk->create("/test", 'hello ' . rand(1, 256), $params);

var_dump($zk->get("/test"));