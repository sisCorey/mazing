<?php


/**
* A*算法
*/
class A 
{
	// 注入地图工厂类
	function __construct($map)
	{
		$this->map = $map;
	}

	public $map = null;
	public $open = array();
	public $openAll = array();
	public $close = array();
	public $path = array();
	public $pathInfo = array();

	public $directionWeight = array('up'=>1,'ru'=>1.99,'right'=>1,'rd'=>1.99,'down'=>1,'ld'=>1.99,'left'=>1,'lu'=>1.99);

	public function debug($type)
	{
		if (DEBUG) {
			echo "* $type start ****\n";
			switch ($type) {
				case 'open':
					foreach ($this->open as $k => $v) {
						echo sprintf("%s:\t%4.1f\t%4.0d\t%4.1f\n",$k, $v->g, $v->h, $v->f);
					}
					break;
				
				default:
					# code...
					break;
			}
			echo "* $type end ******\n";
		}
	}

	public function boot()
	{
		// $mapData = $this->map->create();
		$start = $this->map->getStart();
		$end = $this->map->getEnd();

		// find path
		$reach = $this->run($start, $end);
		$this->savePath($reach, 'main');

		// $j = 0;
		// $node = $this->path['main'][$this->pathInfo['main']['tail']]->parent;
		// while (true) {
		// 	$j++;
		// 	$e = $this->run($start, $node);
		// 	$this->savePath($e, "sub_$j");

		// 	if ($node->equal($start)) {
		// 		break;
		// 	}
		// }
	}

	public function run($node, $end)
	{
		$maxCount = 99;
		$i = 0;

		$this->addClose($node);

		$finish = false;
		$fail = false;
		while (!$finish && !$fail) {
			$i++;
			// get neighbours
			$neighbours = $this->map->getNeighbours($node);
			foreach ($neighbours as $k => $v) {
				if ($this->isClosed($v)) {
					$this->openRm($v);
					continue;
				}
				if ($this->isOpen($v)) {
					continue;
				}

				$v->g = $node->g + $this->directionWeight[$v->nd];
				$v->h = $this->h($v, $end);
				$v->f = $v->f();
				$v->setParent($node);
				$this->addOpen($v);
			}
			if (empty($neighbours)) {
				$this->addClose($node);
			}
			if (empty($this->open)) {
				$finish = true;
				$fail = true;
				continue;
			}
			// find nearest neibour in openList
			$minNb = null;
			foreach ($this->open as $k => $v) {
				if ($this->isClosed($v)) {
					$this->openRm($v);
					continue;
				}
				if (isset($minNb->f)) {
					if ($minNb->f >= $v->f) {
						$minNb = $v;
					}
				}
				else {
					$minNb = $v;
				}
			}
			$this->addClose($minNb);
			$node->setNext($minNb);

			// judge reached end ?
			if ($minNb->equal($end)) {
				$finish = true;
				continue;
			}

			$node = $minNb;

			if (DEBUG && $i >= $maxCount) {
				break;
			}
		}

		// echo sprintf("times:%d\n", $i);
		return $minNb;
	}

	// 估算函数
	public function h($ns, $ne)
	{
		$sx = $ns->x;
		$sy = $ns->y;
		$ex = $ne->x;
		$ey = $ne->y;

		return sqrt(pow(($sx - $ex), 2) + pow(($sy - $ey), 2));
		// return (abs($sx - $ex) + abs($sy - $ey));
	}

	public function savePath(& $point, $tag = 'main')
	{
		$key = $point->x.'_'.$point->y;
		if ($P = $point->parent) {
			$this->savePath($P, $tag);
			$point->num = $P->num+1;
		}
		else {
			$this->path[$tag] = array();
			$this->pathInfo[$tag]['header'] = $key;
			$point->num = 1;
		}
		if (!$P = $point->next) {
			$this->pathInfo[$tag]['tail'] = $key;
		}
		$this->path[$tag][$key] = $point;
	}

	public function printPath($tag = 'main')
	{
		// foreach ($this->path as $k => $v) {
		// 	echo "=> ".$v->toString()."\n";
		// }
		$html = '';
		for ($y=0; $y < $this->map->height; $y++) { 
			$html .= "<div class='l'>";
			for ($x=0; $x < $this->map->width; $x++) {
				$bit = '0';
				$text = "($x,$y)";
				$key = $x.'_'.$y;
				if ($this->isAtOpenAll($x, $y)) {
					$bit = 'o';
				}
				if (isset($this->path[$tag][$key])) {
					$node = $this->path[$tag][$key];
					$bit = '1';
					$text .= "{$node->num}";
				}
				if ($this->map->start->at($x, $y)) {
					$bit = 's';
				}
				if ($this->map->end->at($x, $y)) {
					$bit = 'e';
				}
				if ($this->map->isAtWall($x, $y)) {
					$bit = 'w';
				}
				$html .= sprintf("<span class='p p_%s' title='$text'></span>", $bit);
			}
			$html .= "</div>";
		}
		return $html;
	}

	public function addClose($point)
	{
		$this->close[$point->x][$point->y] = 1;
		$this->openRm($point);
	}

	public function isClosed($point)
	{
		if (isset($this->close[$point->x][$point->y]) && $this->close[$point->x][$point->y]) {
			return true;
		}
		return false;
	}

	public function isOpen($point)
	{
		$key = $point->x.'_'.$point->y;
		if (isset($this->open[$key])) {
			return true;
		}
		return false;
	}

	public function isAtOpenAll($x, $y)
	{
		$key = $x.'_'.$y;
		if (isset($this->openAll[$key])) {
			return true;
		}
		return false;
	}

	public function addOpen($point)
	{
		$key = $point->x.'_'.$point->y;
		$this->open[$key] = $point;
		$this->openAll[$key] = $point;
	}

	public function openRm($point) 
	{
		$key = $point->x.'_'.$point->y;
		if (isset($this->open[$key])) {
			unset($this->open[$key]);
		}
	}

}
