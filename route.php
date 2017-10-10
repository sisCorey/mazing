<?php

set_time_limit(0);
define('DEBUG', false);

require_once('config.php');

$spend = array();

/**
* 主场景
*/
class Main
{

	public $sence;
	
	function __construct()
	{
		$this->init();
	}

	public function init()
	{
		global $spend;

		try {
			
		// scan image

		// transfer image to data
		$t1 = microtime(true);
			$transfer = new ImageTransfer;
			$transfer->choose('maze5.jpg');
			$mapData = $transfer->make();
		$t2 = microtime(true);
		$spend['transfer'] = ($t2-$t1);

		//create map from data
		$t1 = microtime(true);
			$map = new MapFactory;
			// $map->setSize(30, 30);
			// $map->makeWall(12,0, 12,18);
			// $map->makeWall(5,19, 25,19);
			// $map->makeWall(25,19, 25,26);
			// $map->makeWall(20,26, 25,26);
			// $map->makeWall(5,12, 5,19);
			// $map->makeWall(1,12, 5,12);
			// $map->setStart(0, 1);
			// $map->setEnd(19, 13);
			$map->setSize($mapData['width'], $mapData['height']);
			$map->autoConfig($mapData['data']);
		$t2 = microtime(true);
		$spend['createMap'] = ($t2-$t1);
			
			$this->sence = $map;

		} catch (Exception $e) {
			exit($e->getMessage());
		}
	}

	public function start()
	{
		global $spend;

		$t1 = microtime(true);
		$map = $this->sence;
		$a = new A($map);
		$a->boot();
		$t2 = microtime(true);
		$spend['search'] = ($t2-$t1);

		$t1 = microtime(true);
		echo $a->printPath('main');
		$t2 = microtime(true);
		$spend['print'] = ($t2-$t1);

		var_export($spend);
	}
}