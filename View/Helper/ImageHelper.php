<?php
App::uses('AppHelper', 'View/Helper');
App::uses('Set', 'Utility');
App::uses('Image', 'Upload.Lib');
class ImageHelper extends AppHelper {

	public $helpers = array('Html');


	public function imageResize($image, $width, $height) {

		$newIMG = pathinfo($image);

		$newImage = $newIMG['dirname'] . '/' . $newIMG['filename'] . '_' . $width . 'x' . $height . '.' . $newIMG['extension'];
		
		if(!file_exists(WWW_ROOT . $newImage)) {
			$IMAGELIB = new Image();
			$IMAGELIB->prepare(WWW_ROOT . $image);
			$IMAGELIB->resize($width, $height);
			$IMAGELIB->save(WWW_ROOT . $newImage);

			chmod(WWW_ROOT.$newImage,0777);
		}

		return $this->Html->image($newImage);
	}
}

?>