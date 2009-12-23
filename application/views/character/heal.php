<h2>Heal your Character</h2>

Welcome to the magical healing center, for a small fee we can give you some magical 
herbs that will refill your health.<br /><br />

<span class="bar hp">
	<?php echo '<span style="width: ' . $char->percent_hp() . '%"></span>'; ?>
	<p><?php echo $character->hp . ' / '. $character->maxhp; ?></p>
</span>
<br />
<?php echo form::open(); ?>
<fieldset>
	<dl>
		<dt>
			<?php echo form::label('ammount', 'Ammount:'); ?><br />
			<span>(2gold per health point)</span>
		</dt>
		<dd><?php echo form::input( 'ammount', $character->maxhp - $character->hp ); ?></dd>
	</dl>
	<br />
	<?php echo form::submit('heal', 'Heal'); ?>
</fieldset>
<?php echo form::close(); ?>