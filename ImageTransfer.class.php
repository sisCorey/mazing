<?php

/**
* 图像读取类
*/
class ImageTransfer
{

	function __construct()
	{
		$this->init();
	}

	public function init()
	{

	}

	public function choose($name)
	{
		$this->fileName = $name;
	}

	public function make()
	{
		$path = './upload/'.$this->fileName;
		$info = getimagesize($path);
		if (!empty($info)) {
			$image = null;
			if (strpos($info['mime'], 'png')) {
				$image = imagecreatefrompng($path);
			}
			elseif (strpos($info['mime'], 'jpeg')) {
				$image = imagecreatefromjpeg($path);
			}
			elseif (strpos($info['mime'], 'webp')) {
				$image = imagecreatefromwebp($path);
			}
			else {
				return false;
			}

			$width = $info[0];
			$height = $info[1];
			// $width = imagesx($image);
			// $height = imagesy($image);
			$data = array();
			for ($i=0; $i < $width; $i++) { 
				for ($j=0; $j < $height; $j++) {
					$pxi = imagecolorat($image, $i, $j);
					$r = ($pxi >> 16) & 0xff;
					$g = ($pxi >> 8) & 0xff;
					$b = ($pxi ) & 0xff;
					$a = 0;
					// var_dump($r, $g, $b);
					// $px = imagecolorsforindex($image, $pxi);
					// $r = $px['red'];
					// $g = $px['green'];
					// $b = $px['blue'];
					// $a = $px['alpha'];
					// $data[$i][$j] = array('r'=>$r, 'g'=>$g, 'b'=>$b, 'a'=>$a);
					$data[$i][$j] = array($r, $g, $b, $a);
					unset($pxi, $px);
				}
			}
			imagedestroy($image);
			return array('width'=>$width, 'height'=>$height, 'data'=>$data);
		}
		else {
			return false;
		}


	}
}