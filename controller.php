#!/usr/bin/php
<?php

if($_SERVER['argc'] > 1) {
	worker($_SERVER['argv'][1]);
} else {
	master();
}

function worker($master_ep) {
	$name = null;
	$ctx = new ZMQContext();
	$sock = new ZMQSocket(ZMQ::SOCKET_DEALER);
	$sock->connect($master_ep);
	$sock->setSockOpt(ZMQ::SOCKOPT_SNDTIMEOUT, 5000); 

	while(true) {
		$sock->sendMulti("SUP", $name);
		$data = $sock->recvMulti();
		if(count($data)) {
			switch($data[0]) {
				case "DIE":
					exit();
				case "DOIT":
					call_user_func($data[1], json_decode($data[2]));
					break;
				case "UR":
					$name = $data[1];
					break;
			}
		}
	}
}

function basic_register() {
	// Register node in zookeeper with the name, and data
}

function 


function master() {
	$names = array("Soupy Norman", "Grandpa", "Tony");
	$help = "Commands: \n\tstart n - run n workers\n\tstop worker - where worker is name or 'all'\n\trun cmd args - where args is a list of space separated arguments";
	$workers = array();
	$prompt = "\n$>";
	$endpoint = "tcp://127.0.0.1:12465";
	$ctx = new ZMQContext();
	$sock = new ZMQSocket(ZMQ::SOCKET_ROUTER);
	$sock->bind($endpoint);
	$poll = new ZMQPoll();
	$poll->add($sock, ZMQ::POLL_IN);
	$poll->add(STDIN, ZMQ::POLL_IN);

	// Loop on command prompt and worker updates
	// TODO: Display currently alive workers 
	// TODO: heartbeat workers
	while(true) {
		$ev = $poll->poll($read, $write, 0);
		if( $read[0] === $sock ) {
			$req = $sock->recvMulti();
			if( $req[2] == null ) {
				$name = next($names);
				$sock->sendMulti(array($req[0], "UR", $name));
				echo "$name online\n";
			} else { 
				$workers[$req[2]] = $req[0];
			}
		} else {
			$command = explode(readline($fh));
			switch($command[0]) {
				case "start":
					$n = intval($command[1]);
					if($n == 0) {
						echo "\nUse start n - where n is the number of workers to run";
					} else {
						for($i = 0; $i < $n; $i++) {
							spawn_worker($endpoint);
						}
					}
					break;
				case "stop":
					$who = $command[1];
					if( $who == $all ) {
						foreach($workers as $id => $name) {
							stop_worker($id, $name, $sock);
						}
					} else {
						$id = array_search($workers, $who);
						if($id) {	
							stop_worker($id, $who, $sock);
						} else {
							echo "\n$worker not found";
						}
					}
					break;
				case "run":
					$function = $command[1];
					$args = json_encode(array_slice($command, 2));
					foreach($workers as $id => $name) {
						$sock->sendMulti(array($id, "DOIT", $function, $args));
					}
					break;
				default: 
					echo $help; 
			}

			echo $prompt;
		}
	}
}

function spawn_worker($ep) {
	$pid = pcntl_fork();
	if( $pid == 0 ) {
		exec(__DIR__ . __PATH_SEPARATOR__ . __FILE__ . " " . $ep);
		exit();
	}
}

function stop_worker($id, $who, $sock) {
	$sock->sendMulti(array($id, "DIE"));
	echo "\nStopping $who";
}
