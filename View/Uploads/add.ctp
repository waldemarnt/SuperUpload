<div class="uploads index">
<div style="border: 1px solid #000;">
<?php echo $this->Form->create('Upload', array('enctype' => 'multipart/form-data', 'class' => 'dropzone')); ?>
    <div class="fallback">
        <?php echo $this->Form->input('file', array('type' => 'file', 'multiple')); ?>
    </div>
    
<?php echo $this->Form->end(); ?>
<?php
	$uploadUrl = array(
		'plugin' => 'Upload',
		'controller' => 'uploads',
		'action' => 'add'
	);
	$this->Html->script('Upload.dropzone.min', array('block' => 'uploadScripts'));
    echo $this->Html->css('Upload.dropzone');
?>
<?php echo $this->fetch('uploadScripts'); ?>
</div>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('List Uploads'), array('action' => 'index')); ?></li>
	</ul>
</div>
