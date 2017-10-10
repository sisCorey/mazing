<?php

/**
* 质点类
*/
class Point
{
	public $g = 0;
	public $h = 0;
	public $f = 0;
	public $nd = '';

	public $num = 0;
	public $parent = null;
	public $next = null;

	function __construct($x, $y)
	{
		$this->x = $x;
		$this->y = $y;
	}

	public function f()
	{
		return ($this->g * 3 + $this->h * 1);
	}

	public function equal($point)
	{
		if ($this->x == $point->x && $this->y == $point->y) {
			return true;
		}
		return false;
	}

	public function at($x, $y)
	{
		if ($this->x == $x && $this->y == $y) {
			return true;
		}
		return false;
	}

	public function setParent(& $point)
	{
		$this->parent = $point;
	}

	public function setNext(& $point)
	{
		$this->next = $point;
	}

	public function moveForward($d, $offset = 1)
	{
		$x = $this->x;
		$y = $this->y;
		switch ($d) {
			case 'up':
				$y = $y - $offset;
				break;
			case 'down':
				$y = $y + $offset;
				break;
			case 'left':
				$x = $x - $offset;
				break;
			case 'right':
				$x = $x + $offset;
				break;
			case 'lu':
				$x = $x - $offset;
				$y = $y - $offset;
				break;
			case 'ld':
				$x = $x - $offset;
				$y = $y + $offset;
				break;
			case 'ru':
				$x = $x + $offset;
				$y = $y - $offset;
				break;
			case 'rd':
				$x = $x + $offset;
				$y = $y + $offset;
				break;
			default:
				return false;
				break;
		}
		$point = new Point($x, $y);
		$point->nd = $d;
		return $point;
	}

	public function toString()
	{
		return $this->x.'_'.$this->y;
	}

}