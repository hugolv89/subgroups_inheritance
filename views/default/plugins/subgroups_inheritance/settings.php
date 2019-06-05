<label><?php echo elgg_echo('subgroupsinheritance:settings:group:editable'); ?></label> 
<select name="params[enable_inheritance]">
	<option value="true"  <?php if  ($vars['entity']->enable_inheritance == 'true') echo " selected=\"yes\" "; ?>><?php echo elgg_echo('option:yes')?></option>
	<option value="false" <?php if (($vars['entity']->enable_inheritance == 'false') || (!isset($vars['entity']->enable_inheritance))) echo " selected=\"yes\" "; ?>><?php echo elgg_echo('option:no')?></option>
</select>
<br/><br/>
