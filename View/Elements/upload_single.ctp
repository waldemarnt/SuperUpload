	<div class="row">
		<div class="col-sm-offset-4 col-sm-4">
			<div class="form-group">
				<div class="col-sm-5">
					<div class="fileinput fileinput-new" data-provides="fileinput">
						<div class="fileinput-new thumbnail" style="width: <?php echo $upload_thumb_width; ?>px; height: <?php echo $upload_thumb_height; ?>px;" data-trigger="fileinput">
							<img src="http://placehold.it/<?php echo $upload_thumb_width; ?>x<?php echo $upload_thumb_height; ?>" alt="...">
						</div>
						<div class="fileinput-preview fileinput-exists thumbnail" style="min-width:200px;min-height:150px;max-width:<?php echo $upload_thumb_width; ?>px; max-height: <?php echo $upload_thumb_height; ?>px">
							<?php
							if(isset($upload_data[0]['path'])){
								echo $this->Form->hidden('image_name',array('id'=>'image_name','value'=>$upload_data[0]['filename']));
								echo $this->Html->image('../'.$upload_data[0]['path'],array('id'=>'image_exist'));
							}
							?>
						</div>
						<div>
							<span class="btn btn-white btn-file">
								<span class="fileinput-new">Selecionar</span>
								<span class="fileinput-exists">Trocar</span>
								<?php if(isset($upload_data['filename'])){ ?>
								<input id="single_image" type="file" data-position='1' name="<?php echo $upload_data[0]['filename']; ?>" accept="image/*">
								<?php }else{ ?>
								<input id="single_image" type="file" name="" data-position='1' accept="image/*">
								<?php } ?>
							</span>
							<!-- <a href="#" id="crop" class="btn btn-orange fileinput-exists" >Crop</a> -->
							<a href="#" class="btn btn-orange fileinput-exists" id="remove_image" data-dismiss="fileinput">Remove</a>
						</div>
					</div>

				</div>

			</div>
			<?php echo $this->Form->input('local_path',array('type'=>'hidden','id'=>'local_path','value'=>$this->html->url('/'))); ?>
			<?php echo $this->Form->input('model',array('type'=>'hidden','name'=>'model','id'=>'model','value'=>$upload_model)); ?>
			<?php echo $this->Form->input('id',array('type'=>'hidden','name'=>'id','value'=>$upload_model_id)); ?>
			<?php echo $this->Form->input('thumb_width',array('type'=>'hidden','name'=>'thumb_width','value'=>$upload_thumb_width)); ?>
			<?php echo $this->Form->input('thumb_height',array('type'=>'hidden','name'=>'thumb_height','value'=>$upload_thumb_height)); ?>
		</div>
	</div>
	<?php 	echo $this->Html->script(array($this->Html->url('/', true).'Upload/js/jquery-1.10.2.min.js')); ?>
	<?php 	echo $this->Html->css(array($this->Html->url('/', true).'Upload/css/neon.css')); ?>
	<?php 	echo $this->Html->script(array($this->Html->url('/', true).'Upload/js/fileinput.js')); ?>