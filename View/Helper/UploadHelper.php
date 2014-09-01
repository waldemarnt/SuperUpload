<?php
App::uses('AppHelper', 'View/Helper');


class UploadHelper extends AppHelper {

	public $helpers = array('Html');


	public function uploadImage($id=null,$imageWidth=null,$imageHeight=null,$model=null){

	$upload=array(		
	array("<div class='modal fade' id='modal-6'>"),
	array("<div class='modal-dialog'>"),
	array("<div class='modal-content'>"),
	array("<div class='modal-header'>"),
	array("<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>"),
	array("<h4 class='modal-title'>Selecione as dimensões da imagem</h4>"),
	array("</div>"),
	array("<div class='modal-body'>"),
	array("<div class='panel-body'>"),
	array("<h4>Thumb Preview</h4>"),
	array("<div style='width:150px; height:150px; overflow:hidden;' class='thumbnail-highlight'>"),
	array("<img  class='img-rounded' id='preview' />"),
	array("</div>"),
	array("</div>"),
	array("<div class='form-group'>"),
	array("<input type='hidden' class='form-control' id='imgId' />"),
	array("<input type='hidden' class='form-control' id='x' />"),
	array("<input type='hidden' class='form-control' id='y' />"),
	array("<input type='hidden' class='form-control' id='w' />"),
	array("<input type='hidden' class='form-control' id='h' />"),
	array("</div>"),
	array("<div class = 'form-group'>"),
	array("<label for='field-1' class='control-label'>Titulo</label>"),
	array("<input type='text' class='form-control' id='crop-title' value='' placeholder='Titulo da imagem'>"),
	array("</div>"),
	array("<div class='form-group'>"),
	array("<label for='field-1' class='control-label'>Descrição</label>"),
	array("<textarea class='form-control autogrow' id='crop-description' value='' placeholder='Descrição da imagem' style='min-height: 80px;''></textarea>"),
	array("</div>"),
	array("</div>"),
	array("<div class='modal-footer'>"),
	array("<button type='button' class='btn btn-default' data-dismiss='modal'>Fechar</button>"),
	array("<button type='button' class='btn btn-success btn-icon' id='saveCrop' >Salvar Imagem</button>"),
	array("</div>"),
	array("</div>"),
	array("</div>"),
	array("</div>"),
	array("	<div class='uploads index'>
		<div style='border: 1px solid #000;'>
			<form action='/manager-v4/upload/uploads/add' class='dropzone' >
				<input type='hidden' id='model' name='model',value='Post'>
				<input type='hidden' id='id' name='id',value=".$id.">
				<input type='hidden' id='imageWidth' name='imageWidth',value=<?php $id ?>>
				<input type='hidden' id='imageHeight' name='imageHeight',value=<?php $id ?>>
				<div class='fallback'>
					<?php echo $this->Form->input('file', array('id'=>1,'type' => 'file', 'multiple','enctype' => 'multipart/form-data')); ?>
				</div>

				</form>

				<?php echo $this->fetch('uploadScripts'); ?>
			</div>
		</div>
				"),
	);

	return $upload;
    

	}
}


?>