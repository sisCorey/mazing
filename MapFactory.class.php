<?php

/**
* 地图构造工厂
*/
class MapFactory
{
	public $start;
	public $end;
	public $wallData = array();
	
	function __construct($width = 0, $height = 0)
	{
		$this->width = $width;
		$this->height = $height;
	}

	public function getStart()
	{
		return $this->start;
	}

	public function getEnd()
	{
		return $this->end;
	}

	public function getNeighbours($point)
	{
		$all = array();
		$list = array();
		$directions = array('up','ru','right','rd','down','ld','left','lu');
		// 八个方向均可前进
		foreach ($directions as $k => $v) {
			$all[$v] = $node = $point->moveForward($v, 1);
			if (!$this->isWall($node) && !$this->isEdge($node)) {
				// array_push($list, $node);
				$list[$v] = $node;
			}
		}
		// 阻止对角穿墙
		if ($this->isWall($all['up']) && $this->isWall($all['right'])) {
			unset($list['ru']);
		}
		if ($this->isWall($all['right']) && $this->isWall($all['down'])) {
			unset($list['rd']);
		}
		if ($this->isWall($all['down']) && $this->isWall($all['left'])) {
			unset($list['ld']);
		}
		if ($this->isWall($all['left']) && $this->isWall($all['up'])) {
			unset($list['lu']);
		}

		$list = array_values($list);

		return $list;
	}

	public function setSize($width = 0, $height = 0)
	{
		$this->width = $width;
		$this->height = $height;
	}

	public function setWall($x, $y)
	{
		$this->wallData[$x][$y] = 1;
	}

	public function setStart($x, $y)
	{
		$this->start = new Point($x, $y);
	}

	public function setEnd($x, $y)
	{
		$this->end = new Point($x, $y);
	}

	/**
	 * 从 (sx,sy) 到  (ex,ey) 建立一堵连续的墙
	 *
	 **/
	public function makeWall($sx = 0, $sy = 0, $ex = 0, $ey = 0)
	{
		for ($x = $sx; $x <= $ex; $x++) { 
			for ($y = $sy; $y <= $ey; $y++) { 
				$this->setWall($x, $y);
			}
		}
	}

	public function mergeWall($data, $width = 0, $height = 0)
	{
		$width = $width ? intval($width) : count($data);
		$height = $height ? intval($height) : count($data[0]);

		$setting = array(0, 0, 0, 0);
		$offset = 180;
		$ratio = array(12,12);

		for ($i=0; $i < $width; $i++) { 
			for ($j=0; $j < $height; $j++) {
				if (isset($data[$i][$j]) && $i < $this->width && $j < $this->height) {
					$px = $data[$i][$j];
					if ($i > 172 && $i < 179 && $j > 64 && $j < 70) {
						// echo "($i,$j) ".json_encode($px)." <br>";
					}
					if (($px[0]>$setting[0]-$offset && $px[0]<$setting[0]+$offset)
						&& ($px[1]>$setting[1]-$offset && $px[1]<$setting[1]+$offset)
						&& ($px[2]>$setting[2]-$offset && $px[1]<$setting[2]+$offset)
						&& ($px[3]==$setting[3])
						&& (abs($px[0]-$px[1]) <= $ratio[0])
						&& (abs($px[1]-$px[2]) <= $ratio[1])
					) {
						$this->wallData[$i][$j] = 1;
					}
				} 
			}
		}
		// exit;
	}

	public function findStart($data, $width = 0, $height = 0)
	{
		$width = $width ? intval($width) : count($data);
		$height = $height ? intval($height) : count($data[0]);

		$setting = array(237, 28, 36, 0);
		$offset = 8;

		$x = array();
		$y = array();
		for ($i=0; $i < $width; $i++) {
			for ($j=0; $j < $height; $j++) {
				if (isset($data[$i][$j]) && $i < $this->width && $j < $this->height) {
					$px = $data[$i][$j];
					if (($px[0]>$setting[0]-$offset && $px[0]<$setting[0]+$offset)
						&& ($px[1]>$setting[1]-$offset && $px[1]<$setting[1]+$offset)
						&& ($px[2]>$setting[2]-$offset && $px[1]<$setting[2]+$offset)
						&& $px[3]==$setting[3]) {
						$x[] = $i;
						$y[] = $j;
					}
				} 
			}
		}
		if (empty($x) || empty($y)) {
			ethrow('Start Point Absent !');
			return false;
		}
		$ax = round(array_sum($x)/count($x));
		$ay = round(array_sum($y)/count($y));

		$this->setStart($ax, $ay);
	}

	public function findEnd($data, $width = 0, $height = 0)
	{
		$width = $width ? intval($width) : count($data);
		$height = $height ? intval($height) : count($data[0]);

		$setting = array(34, 177, 76, 0);
		$offset = 8;

		$x = array();
		$y = array();
		for ($i=0; $i < $width; $i++) { 
			for ($j=0; $j < $height; $j++) {
				if (isset($data[$i][$j]) && $i < $this->width && $j < $this->height) {
					$px = $data[$i][$j];
				// var_dump(($px[0]>$setting[0]-$offset && $px[0]<$setting[0]+$offset));
				// var_dump(($px[1]>$setting[1]-$offset && $px[1]<$setting[1]+$offset));
				// var_dump(($px[2]>$setting[2]-$offset && $px[2]<$setting[2]+$offset));
				// echo "($i,$j) ".json_encode($px)." <br>";
					if (($px[0]>$setting[0]-$offset && $px[0]<$setting[0]+$offset)
						&& ($px[1]>$setting[1]-$offset && $px[1]<$setting[1]+$offset)
						&& ($px[2]>$setting[2]-$offset && $px[2]<$setting[2]+$offset)
						&& $px[3]==$setting[3]) {
						$x[] = $i;
						$y[] = $j;
					}
				} 
			}
		}
		if (empty($x) || empty($y)) {
			ethrow('End Point Absent !');
			return false;
		}
		$ax = round(array_sum($x)/count($x));
		$ay = round(array_sum($y)/count($y));

		$this->setEnd($ax, $ay);
	}

	public function autoConfig($data)
	{
		$this->mergeWall($data);
		$this->findStart($data);
		$this->findEnd($data);
	}

	public function isAtWall($x, $y)
	{
		if (isset($this->wallData[$x][$y]) && $this->wallData[$x][$y]) {
			return true;
		}
		return false;
	}

	public function isWall($point)
	{
		if (isset($this->wallData[$point->x][$point->y]) && $this->wallData[$point->x][$point->y]) {
			return true;
		}
		return false;
	}

	public function isEdge($point)
	{
		if ($point->x < 0 || $point->y < 0 || $point->x >= $this->width || $point->y >= $this->height) {
			return true;
		}
		return false;
	}

}