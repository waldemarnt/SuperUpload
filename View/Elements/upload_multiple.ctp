	<div class="uploads index">
		<div style="border: 1px solid #000;">
			<form action="<?php echo $this->html->url('/upload/uploads/add', true); ?>" class="dropzone" >
				<?php echo $this->Form->input('model',array('type'=>'hidden','name'=>'model','id'=>'model','value'=>$upload_model)); ?>

				<?php echo $this->Form->input('id',array('type'=>'hidden','name'=>'id','value'=>$upload_model_id)); ?>

				<?php echo $this->Form->input('thumb_width',array('type'=>'hidden','name'=>'thumb_width','value'=>$upload_thumb_width)); ?>
				<?php echo $this->Form->input('thumb_height',array('type'=>'hidden','name'=>'thumb_height','value'=>$upload_thumb_height)); ?>

				<div class="fallback">
					<?php echo $this->Form->input('file', array('id'=>1,'type' => 'file', 'multiple','enctype' => 'multipart/form-data')); ?>
				</div>

				<?php echo $this->Form->end(); ?>

			</div>
		</div>
		<?php 	echo $this->Html->css(array($this->Html->url('/', true).'Upload/js/dropzone/dropzone.css')); ?>
		<?php 	echo $this->Html->script(array($this->Html->url('/', true).'Upload/js/dropzone/dropzone.js')); ?>