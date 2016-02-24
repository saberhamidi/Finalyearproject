<script type="text/javascript">
	tinymce.init({
		  selector: "textarea",  // change this value according to your HTML
		    theme: 'modern',
			  plugins: [
			    "insertdatetime media table contextmenu paste jbimages"
			  ],
		    images_upload_url: 'postAcceptor.php',
		    images_upload_base_path: '/some/basepath',
		    images_upload_credentials: true,

				// ===========================================
			  // PUT PLUGIN'S BUTTON on the toolbar
			  // ===========================================
				
			  toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image jbimages",
				
			  // ===========================================
			  // SET RELATIVE_URLS to FALSE (This is required for images to display properly)
			  // ===========================================
				
			  relative_urls: false
		});
</script>

<div class="campaigns form">
<?php echo $this->Form->create('Campaign'); ?>
	<fieldset>
		<legend><?php echo __('Add Campaign'); ?></legend>
	<?php
		echo $this->Form->input('name', ['type' =>'text']);
		echo $this->Form->input('type', array(
    		'options' => array('Re-tweeting Promotion'=>'Re-tweeting Promotion','Recommend for Reward'=>'Recommend for Reward'),
    		'empty' => '(Type)'
		));
		echo "<label class = 'required'>Start Date</label>";
		echo $this->Form->date('start_date');
		echo "<br><br><label class = 'required'>Expire Date</label>";
		echo $this->Form->date('expire_date');
		echo "<br><br><label class = 'required'>Content</label>";
		echo $this->Wysiwyg->textarea('page');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Campaigns'), array('action' => 'index')); ?></li>
	</ul>
</div>
