<?php
App::uses('File', 'Utility');
App::uses('Folder', 'Utility');
App::uses('AppModel', 'Model');
include(APP.'Plugin'.DS.'Upload'.DS.'Vendor'.DS.'WideImage'.DS.'WideImage.php');
class Upload extends AppModel {

	public $uploadOptions = null;




	public $validate = array(
		'mime' => array(
			'mimetype' => array(
				'rule' => array('mimetype', array(
					'image/png',
					'image/jpeg',
					'image/gif',

					'video/mp4',
					'video/webm',
					'video/ogv',

					'audio/mp4',
				)),
				'message' => 'Invalid mime type',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),

		'path' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'url' => array(
			'url' => array(
				'rule' => array('url'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);

	//create first thumb, receives post data
	public function createThumb($model,$file,$uploadPath,$filename,$width,$height){

		//replace directory bar , to get correct url
		$new_file = str_replace(DS, '/', $file);

		//use WideImage Properties to load image
		$new_file =  WideImage::load($new_file['url']);

		//create thumb and save indo folder
		//$img_resized = $new_file->resize($width,$height);
		$img_resized = $new_file;
		//save thumb and rename
		$img_resized->saveToFile(WWW_ROOT.$uploadPath.DS.'thumb_'.$filename);
		$to_crop = WideImage::load(WWW_ROOT.$uploadPath.DS.'thumb_'.$filename)->resize($width,$height,'outside');
		$to_crop = $to_crop->crop('center','center',$width,$height);

		$to_crop->saveToFile(WWW_ROOT.$uploadPath.DS.'thumb_'.$filename);

		if($model == 'Service' || $model == 'Post'){
		// $to_crop2 = WideImage::load(WWW_ROOT.$uploadPath.DS.'thumb_'.$filename)->resize(360,360,'outside');
		// $to_crop2 = $to_crop2->crop('center','center',360,360);

		// $to_crop2->saveToFile(WWW_ROOT.$uploadPath.DS.'equivalent_'.$filename);


		$to_crop3 = WideImage::load(WWW_ROOT.$uploadPath.DS.'thumb_'.$filename)->resize(280,380,'outside');
		$to_crop3 = $to_crop3->crop('center','center',280,380);
		$to_crop3->saveToFile(WWW_ROOT.$uploadPath.DS.'tiny_'.$filename);

		}
		//create a black n' whide image thumb


	}

	//receive data , crop image
	public function cropImage($data){
		$new_file =  WideImage::load(WWW_ROOT.$data['path']);
		//crop post data based
		$cropped = $new_file->crop($data['x'],$data['y'],$data['w'],$data['h']);

		$file = $this->field('path');
		$info = pathinfo($file);
		$cropped->saveToFile(WWW_ROOT.$info['dirname'].DS.'crop_'.$info['basename']);
		//call create thumb function
		$this->createCroppedThumb($cropped,$data['crop_model'],$data['thumb_width'],$data['thumb_height']);

	}

	public function createCroppedThumb($cropped,$crop_model,$width,$height){
		//receives a cropped image to resize and create a thumb

		$file = $this->field('path');
		$info = pathinfo($file);
		$img_resized = $cropped->resize($width,$height,'outside');
		$img_resized = $img_resized->crop('center','center',$width,$height);

		$img_resized->saveToFile(WWW_ROOT.$info['dirname'].DS.'thumb_'.$info['basename']);

	}



	//tell function before saves upload data
	public function beforeSave($options = array()) {
		        parent::beforeSave();

		//if file have id, update and crop before save this data
		if(isset($this->data['Upload']['id'])){
			$this->cropImage($this->data['Upload']);

		}else{
		$this->uploadOptions = Configure::read('uploadOptions');
		if(isset($this->data[$this->alias]['error']) && $this->data[$this->alias]['error'][0] == 0) {
			$uploadPath = $this->uploadOptions['uploadPath'].$this->data['Upload']['model'].DS.$this->data['Upload']['media_id'].DS;
			$dir = new Folder(WWW_ROOT .$uploadPath, true, 0777);
			$file = $this->data[$this->alias]['name'];
			$tmp = $this->data[$this->alias]['tmp_name'];
			$file = pathinfo($file);
			$file['dirname'] = '';
			$upload = $this->__duplicateNames($this->uploadOptions['uploadPath'] . $file['dirname'].$file['basename'], $count = 0);
			$type = explode('/', $this->data[$this->alias]['type']);
			$model = $this->data['Upload']['model'];
			$media_id=$this->data['Upload']['media_id'];
			$filename = md5($file['basename']);
			$file_ext = pathinfo($file['basename'], PATHINFO_EXTENSION);
			$filename = $filename.".".$file_ext;

			$file = array(
				'dirname' => '',
				'tmp' => $tmp,
				'upload' => $upload,
				'url' => $upload,
				'size' => $this->data[$this->alias]['size'],
				'mime' => $this->data[$this->alias]['type'],
				'type' => $type['0'],
				'model'=>$this->data['Upload']['model'],
				'media_id'=>$this->data['Upload']['media_id'],
				'filename'=>$filename,
				'thumb_name'=>'thumb_'.$filename,
				'thumb_width'=>$this->data['Upload']['thumb_width'],
				'thumb_height'=>$this->data['Upload']['thumb_height'],
				'is_single'=>$this->data['Upload']['is_single'],
			);
			$file['dirname']  = $uploadPath . $filename;
			$file['url']  = Router::url($file['dirname'], true);

			if(move_uploaded_file($file['tmp'], WWW_ROOT . $file['dirname'])) {
				chmod(WWW_ROOT.$file['dirname'],0777);
				$file['path'] = str_replace(DS, '/', $file['dirname'])	;
				$file['url'] = WWW_ROOT.$file['dirname'];


				$imagedata = getimagesize(WWW_ROOT . $file['dirname']);
				$file['width'] = $imagedata[0];
				$file['height'] = $imagedata[1];

				$this->createThumb($model,$file,$uploadPath,$filename,$this->data['Upload']['thumb_width'],$this->data['Upload']['thumb_height']);
				return $this->data['Upload'] = $file;
			}

			return false;

		}

		return false;
	}
	}
	//if name is duplicated, old file is removed
	protected function __duplicateNames($checkfile, $count = 0) {



		$file = pathinfo($checkfile);
		if($count > 0) {
			$checkfile = $file['dirname'] . DS .   Inflector::slug($file['filename']) . '-' . $count . '.' . $file['extension'];
		}

		if(!file_exists(WWW_ROOT.$checkfile)) {
			$checkfile = $file['dirname'] . DS .   Inflector::slug($file['filename']) . '.' . $file['extension'];
			return $checkfile;
		} else {
			$count++;
			$checkfile = $file['dirname'] . DS .   Inflector::slug($file['filename']) . '.' . $file['extension'];
			return $this->__duplicateNames($checkfile, $count);
		}
	}

	public function beforeDelete($cascade = true){
		$file = $this->field('path');
		$info = pathinfo($file);

		foreach(glob(WWW_ROOT . $info['dirname'] . '/' .'thumb_'. $info['filename']  . '.' . $info['extension']) as $v){
			unlink($v);
		}
		foreach(glob(WWW_ROOT . $info['dirname'] . '/' .'crop_'. $info['filename']  . '.' . $info['extension']) as $v){
			unlink($v);
		}		foreach(glob(WWW_ROOT . $info['dirname'] . '/' . $info['filename'] . '.' . $info['extension']) as $v){
			unlink($v);
		}
		return true;
	}
}