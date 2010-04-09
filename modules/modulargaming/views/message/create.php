<h2><?php echo __('Messages').' - '.__('Write a new message') ?></h2>

<div class="left w150"><?php echo $sidebar ?></div>

<div class="right w500" id="new_pm">
	
	<?php echo Message::render() ?>
	
	<?php echo form::open() ?>
	<fieldset>
		<dl>
			<dt><?php echo form::label('to', __('Reciver:')); ?></dt>
			<dd><?php echo form::input('to', $post['to']); ?></dd>
		</dl>
		<dl>
			<dt><?php echo form::label('title', __('Title:')); ?></dt>
			<dd><?php echo form::input('title', $post['title']); ?></dd>
		</dl>
		<dl>
			<dt><?php echo form::label('message', __('Message:')); ?></dt>
			<dd>
				<?php echo form::textarea('message', $post['message']); ?>
			</dd>
		</dl>
		<br /><br />
		
		<?php echo form::submit('send', __('Send')); ?>
	</fieldset>
	<?php echo form::close() ?>

</div>