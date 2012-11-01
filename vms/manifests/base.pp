# Basic Puppet manifest

class zookeeper {
  exec { 'apt-get update':
    command => '/usr/bin/apt-get update'
  }

  package { "zookeeper":
    ensure => present,
  }

  package { "zookeeperd":
    ensure => present,
  }

  service { "zookeeper":
    ensure => running,
  }
  
  file { "/var/run/zookeeper/data":
    path => "/var/run/zookeeper/data", 
    ensure => directory, 
    owner => "zookeeper",
    group => "zookeeper",
    mode => 644, 
    backup => false,
  }
  
  file { "/var/run/zookeeper/data/myid":
    path => "/var/run/zookeeper/data/myid", 
    ensure => file, 
    content => template("conf/myid.erb"), 
    owner => "zookeeper",
    group => "zookeeper",
    mode => 644, 
    backup => false,
   }

  file { "conf/zoo.cfg":
    path => "/etc/zookeeper/conf/zoo.cfg",
    owner => "zookeeper",
    group => "zookeeper",
    mode => 644,
    content => template("conf/zookeeper.cfg.erb"), 
  }
}

include zookeeper