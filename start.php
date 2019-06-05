<?php
/**
 * Subgroups Hierarchy Plugin
 */

namespace HLV\SubGroupsInheritance;

const PLUGIN_ID = 'subgroups_inheritance';

//register the plugin hook handler
elgg_register_event_handler('init', 'system', __NAMESPACE__.'\\init');

/**
 * plugin init function
 */
function init() {

	// add css
	elgg_extend_view('css/elgg', 'css/subgroups_inheritance/style.css');
	
	// group Option
	if(Functions::SETTING_BOOL('enable_inheritance')){

		add_group_tool_option('subgroups_inheritance', elgg_echo('subgroupsinheritance:group:tool:text'));
	}

	elgg_register_event_handler('join', 'group',  __NAMESPACE__.'\\group_inheritance_join_event', 600);
	elgg_register_event_handler('create', 'group',  __NAMESPACE__.'\\group_inheritance_create_group_event', 1000);
	
	elgg_register_plugin_hook_handler('action','groups/invite', __NAMESPACE__.'\\group_inheritance_invite');
	elgg_register_plugin_hook_handler('action','au_subgroups/move', __NAMESPACE__.'\\group_inheritance_move_group');
	


}

function group_inheritance_join_event($event, $object_type, $object) {

	$user_guid = get_input('user_guid');

	if(!sizeof($user_guid)){
		
		$user_guid = elgg_get_logged_in_user_entity()->guid;
	}
	
	$group_guid = get_input('group_guid');
	
	$user = get_user($user_guid);
	
	// access bypass for getting invisible group
	$ia = elgg_set_ignore_access(true);
	$group = get_entity($group_guid);
	elgg_set_ignore_access($ia);

	if ($user && $group && $group->isMember($user)) { // && ($group instanceof ElggGroup)

		// Loop
		$subgroups = Inheritance::subgroups_inheritance($group);

		foreach ($subgroups as $subgroup) {

			if(!$subgroup->isMember($user)){

				if (groups_join_group($subgroup, $user)) {
					//system_message(elgg_echo("groups:joined"));
					
				} else {
					
					//register_error(elgg_echo("groups:cantjoin").$subgroup->name);
				}
			
			}

		}

	}else{

		return false;
	}
	
	return true;

}

function group_inheritance_create_group_event($event, $type, $object) {
	
	// if we have an input
	$parent_guid = get_input('au_subgroups_parent_guid', false);
	if ($parent_guid !== false) {
		
		$parentgroup = get_entity($parent_guid);
		
		// Parent Users
		$user_guids = $parentgroup->getMembers();
		if (!empty($user_guids) && !is_array($user_guids)) {
			$user_guids = array($user_guids);
		}
		
		if (!empty($user_guids) && $object && $object->subgroups_inheritance_enable == 'yes') {
				
			// Loop
			$subgroups = Inheritance::subgroups_inheritance($object);
			// Add current group to array
			array_unshift($subgroups, $object);
	
			foreach ($subgroups as $subgroup) {
				
				foreach ($user_guids as $user) {
					
					if (empty($user)) {
						continue;
					}
					
					if (!$subgroup->isMember($user)) {
						if (groups_join_group($subgroup, $user)) {
			
							//system_message(elgg_echo('groups:addedtogroup'));
						
						}else{
							
							//register_error(elgg_echo("groups:cantjoin"));
						}
					}
					
				}
				
			}
		}
		
	}
}

function group_inheritance_move_group($hook, $entity_type, $returnvalue, $params) {

	$subgroup_guid = get_input('subgroup_guid');
	$parent_guid = get_input('parent_guid');
		
	// access bypass for getting invisible group
	$ia = elgg_set_ignore_access(true);
	$group = get_entity($subgroup_guid);
	$parentgroup = get_entity($parent_guid);
	elgg_set_ignore_access($ia);
	
	if($parentgroup && $group && $group->subgroups_inheritance_enable == 'yes'){
		
		// Parent Users
		$user_guids = $parentgroup->getMembers();
		if (!empty($user_guids) && !is_array($user_guids)) {
			$user_guids = array($user_guids);
		}
		
		if (!empty($user_guids) && $group) {
				
			// Loop
			$subgroups = Inheritance::subgroups_inheritance($group);
			// Add current group to array
			array_unshift($subgroups, $group);
	
			foreach ($subgroups as $subgroup) {
				
				foreach ($user_guids as $user) {
					
					if (empty($user)) {
						continue;
					}
					
					if (!$subgroup->isMember($user)) {
						if (groups_join_group($subgroup, $user)) {
			
							//system_message(elgg_echo('groups:addedtogroup'));
						
						}else{
							
							//register_error(elgg_echo("groups:cantjoin"));
						}
					}
					
				}
				
			}
		}
	
	}

	return true;
}

function group_inheritance_invite($hook, $entity_type, $returnvalue, $params) {  //$event, $object_type, $object) {//
//function group_inheritance_invite_event($event, $object_type, $object) {

	$logged_in_user = elgg_get_logged_in_user_entity();
	
	$user_guids = get_input('user_guid');
	if (!empty($user_guids) && !is_array($user_guids)) {
		$user_guids = array($user_guids);
	}
	
	$group_guid = (int) get_input('group_guid');
	
	// access bypass for getting invisible group
	$ia = elgg_set_ignore_access(true);
	$group = get_entity($group_guid);
	elgg_set_ignore_access($ia);

	
	if (!empty($user_guids) && $group) {
		
		// Loop
		$subgroups = Inheritance::subgroups_inheritance($group);
		
		foreach ($subgroups as $subgroup) {

			foreach ($user_guids as $u_id) {
				$user = get_user($u_id);
				if (empty($user)) {
					continue;
				}

				if (!$subgroup->isMember($user)) {
					if (groups_join_group($subgroup, $user)) {
		
						//system_message(elgg_echo('groups:addedtogroup'));
					
					}else{
						
						//register_error(elgg_echo("groups:cantjoin"));
					}
				} else {

					// if an invitation is still pending clear it up, we don't need it
					remove_entity_relationship($subgroup->guid, 'invited', $user->guid);
					
					// if a membership request is still pending clear it up, we don't need it
					remove_entity_relationship($subgroup->guid, 'membership_request', $group->guid);
				}
			}
		}
		
	}else{

		return false;
	}
	
	return true;
}