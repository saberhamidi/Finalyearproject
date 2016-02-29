<div class="campaigns view">
<h2><?php echo __('Campaign'); ?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($campaign['Campaign']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Name'); ?></dt>
		<dd>
			<?php echo h($campaign['Campaign']['name']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Type'); ?></dt>
		<dd>
			<?php echo h($campaign['Campaign']['type']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Start Date'); ?></dt>
		<dd>
			<?php echo h($campaign['Campaign']['start_date']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Expire Date'); ?></dt>
		<dd>
			<?php echo h($campaign['Campaign']['expire_date']); ?>
			&nbsp;
		</dd>
	</dl>

	<!--Compaign Deployment button-->
<?php echo $this->Form->create(['action' => '/deploy/'.$campaign['Campaign']['id']]); 
	if($campaign['Campaign']['deployed'])
		echo $this->Form->end(array('label'=>'Deployed', 'disabled' =>true, 'class'=>'disabled'));
	else
		echo $this->Form->end('Deploy');

?>
	<h2><?php echo __('Participants'); ?></h2>
	<table cellpadding="0" cellspacing="0">
	<thead>
	<tr>
			<th>Email</th>
			<th>First Name</th>
			<th>Last Name</th>
			<th>Loyalty Balance</th>
	</tr>
	</thead>
	<?php foreach ($participants as $participant): ?>
		<tr>
			<td><?php echo h($participant['email']); ?>&nbsp;</td>
			<td><?php echo h($participant['firstname']); ?>&nbsp;</td>
			<td><?php echo h($participant['lastname']); ?>&nbsp;</td>
			<td><?php echo h($participant['loyalty_balance']); ?>&nbsp;</td>
		</tr>
	<?php endforeach; ?>
	</tbody>
	</table>

</div>

<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Campaign'), array('action' => 'edit', $campaign['Campaign']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete Campaign'), array('action' => 'delete', $campaign['Campaign']['id']), array('confirm' => __('Are you sure you want to delete # %s?', $campaign['Campaign']['id']))); ?> </li>
		<li><?php echo $this->Html->link(__('List Campaigns'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Campaign'), array('action' => 'add')); ?> </li>
	</ul>
</div>
