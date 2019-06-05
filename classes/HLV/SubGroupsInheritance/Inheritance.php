<?php
/**
 * Subgroups Inheritance Functions
 */

namespace HLV\SubGroupsInheritance;

class Inheritance {

	/**
	 * Get only subgroup with inheritance enable Array
	 */
	public static function subgroups_inheritance($group,$actual = null){
	
		if(!$actual){
			$actual = $group;
		}
	
		$subgroups_array = array();

		if($group && $group->subgroups_enable == 'yes'){

			if( $subgroups = \AU\SubGroups\get_subgroups($group, 0) ){
		
				$subgroups_array = $subgroups;
				
				foreach ($subgroups as $subgroup) {
				
					if($subgroup->subgroups_inheritance_enable != 'yes' || !Functions::SETTING_BOOL('enable_inheritance')){ // Has inheritance enable || Default All
						
						if( $is_group =  Inheritance::subgroups_inheritance($subgroup,$actual) ){
		
							$subgroups_array = array_merge($subgroups_array,$is_group);
						}

					}else{	// Remove from list
			
						$index = array_search($subgroup, $subgroups_array);
					    if($index !== false){
					        unset($subgroups_array[$index]);
					    }
						
					}		
					
				}
	
			}
		}

		return $subgroups_array;
	}

}