<?php
App::uses('AppController', 'Controller');
/**
 * Uploads Controller
 *
 * @property Upload $Upload
 */
class UploadsController extends AppController {

/**
 * index method
 *
 * @return void
 */
public $helpers = array('js');
public $components = array(
	'RequestHandler',
	'Session'
	);



public function index() {
	$this->Upload->recursive = 0;
	$this->set('uploads', $this->paginate());
}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
public function view($id = null) {
	if (!$this->Upload->exists($id)) {
		throw new NotFoundException(__('Invalid upload'));
	}
	$options = array('conditions' => array('Upload.' . $this->Upload->primaryKey => $id));
	$this->set('upload', $this->Upload->find('first', $options));
}

public function load_images(){

	$uploads = $this->Upload->find('all',array('conditions'=>array('Upload.media_id'=>$this->data['id'],'Upload.model'=>$this->data['model'],'is_single'=>null)));

	$this->set(compact('uploads'));
	$this->set('_serialize',array('uploads'));

}
public function save_single(){
	$success = false;
	if($this->request->is('post')){
		@$this->request->data['Upload'] = $_FILES['image'];
		$this->request->data['Upload']['model']= $this->data['model'];
		$this->request->data['Upload']['media_id']=$this->data['id'];
		$this->request->data['Upload']['thumb_width']=$this->data['thumb_width'];
		$this->request->data['Upload']['thumb_height']=$this->data['thumb_height'];
		$this->request->data['Upload']['is_single']=$this->data['is_single'];
		if(isset($_FILES['image'])){
			$up_image = getimagesize($_FILES['image']['tmp_name']);
			if($up_image[0] < $this->data['thumb_width'] ||  $up_image[1] < $this->data['thumb_height']){
			$success = 'size_error';

			}
		}
		$if_exist = $this->Upload->find('count',array('conditions'=>array('model'=>$this->request->data['Upload']['model'],'media_id'=>$this->request->data['Upload']['media_id'],'is_single'=>$this->data['is_single'])));
		if($if_exist==0){
			if ($this->Upload->save($this->request->data['Upload'])) {
				if($success != 'size_error'){
					$success= true;
				}

			} else {
				if($success != 'size_error'){
					$success = false;
				}
			}
		}else{
		$exist = $this->Upload->find('first',array('conditions'=>array('model'=>$this->request->data['Upload']['model'],'media_id'=>$this->request->data['Upload']['media_id'],'is_single'=>$this->data['is_single'])));
		$this->Upload->delete($exist['Upload']['id']);
			if ($this->Upload->save($this->request->data['Upload'])) {
				if($success != 'size_error'){
					$success= true;
				}

			} else {
				if($success != 'size_error'){
					$success = false;
				}
			}
	}

	}
	$this->set(compact('success'));
	$this->set('_serialize',array('success'));
}
/**
 * add method
 *
 * @return void
 */
public function add($file=null) {
	if ($this->request->is('post')) {
		if($this->request->is('ajax')) {
			$this->request->data['Upload'] = $_FILES['file'];
			$this->request->data['Upload']['model']= $this->data['model'];
			$this->request->data['Upload']['media_id']=$this->data['id'];
			$this->request->data['Upload']['thumb_width']=$this->data['thumb_width'];
			$this->request->data['Upload']['thumb_height']=$this->data['thumb_height'];
		}else {
			$this->request->data['Upload'] = $this->request->data['Upload']['file'];
		}
		$this->Upload->create();
		if ($this->Upload->save($this->request->data['Upload'])) {
			if($this->request->is('ajax')) {
				return true;
			}
			$this->Session->setFlash(__('The upload has been saved'));
			$this->redirect(array('action' => 'index'));
		} else {
			if($this->request->is('ajax')) {
				return false;
			}
			$this->Session->setFlash(__('The upload could not be saved. Please, try again.'));
		}
	}
}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */

public function remove (){
	$this->autoRender= false;
	$medias = $this->Upload->find('all',array('conditions'=>array('model'=>$this->data['model'],'filename'=>$this->data['filename'],'media_id'=>$this->data['media_id'])));
	foreach ($medias as $key => $value) {
		$this->Upload->id = $value['Upload']['id'];
		$this->Upload->delete();
	}



}



//load image to crop
public function crop (){
	$this->Layout= 'ajax';
	$path = $this->data['filename'];
	$path = explode('.',$path);
	$path = end($path);

	$medias = $this->Upload->find('all',array('conditions'=>array('model'=>$this->data['model'],'media_id'=>$this->data['media_id'],'OR'=>array(array('filename'=>$this->data['filename']),array('filename'=>md5($this->data['filename']).".".$path)))));
	foreach ($medias as $key => $value) {
		$this->Upload->id = $value['Upload']['id'];
	}
		//set json data from view
	$this->set(compact('value'));
	$this->set('_serialize',array('value'));
}

//save image cropped , resize and create thumb in before save
public function savecropped(){
	$this->Layout= 'ajax';

	$media = $this->Upload->find('first',array('conditions'=>array('path'=>$this->data['image'])));
	$this->Upload->id = $media['Upload']['id'];
	$this->Upload->set(array('id'=>$media['Upload']['id'],'crop_model'=>$this->data['crop_model'],'path'=>$this->data['image'],'title'=>@$this->data['title'],'description'=>@$this->data['description'],
		'x'=>$this->data['x'],'y'=>$this->data['y'],'x2'=>$this->data['x2'],'y2'=>$this->data['y2'],'h'=>$this->data['h'],'w'=>$this->data['w'],'thumb_width'=>$media['Upload']['thumb_width'],'thumb_height'=>$media['Upload']['thumb_height']));
	$this->Upload->save();

	$this->set(compact('value'));
	$this->set('_serialize',array('value'));
}

//not used
public function edit($id = null) {
	if (!$this->Upload->exists($id)) {
		throw new NotFoundException(__('Invalid upload'));
	}
	if ($this->request->is('post') || $this->request->is('put')) {
		if ($this->Upload->save($this->request->data)) {
			$this->Session->setFlash(__('The upload has been saved'));
			$this->redirect(array('action' => 'index'));
		} else {
			$this->Session->setFlash(__('The upload could not be saved. Please, try again.'));
		}
	} else {
		$options = array('conditions' => array('Upload.' . $this->Upload->primaryKey => $id));
		$this->request->data = $this->Upload->find('first', $options);
	}
}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
public function delete($id = null) {
	$this->Upload->id = $id;
	if (!$this->Upload->exists()) {
		throw new NotFoundException(__('Invalid upload'));
	}
	$this->request->onlyAllow('post', 'delete');
	if ($this->Upload->delete()) {
		$this->Session->setFlash(__('Upload deleted'));
		$this->redirect(array('action' => 'index'));
	}
	$this->Session->setFlash(__('Upload was not deleted'));
	$this->redirect(array('action' => 'index'));
}
}
