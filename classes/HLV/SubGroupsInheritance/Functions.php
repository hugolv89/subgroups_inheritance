<?php

namespace HLV\SubGroupsInheritance;

class Functions {

	/**
	 * User Setting
	 */
	public static function USETTING($id){

		return elgg_get_plugin_user_setting($id, elgg_get_logged_in_user_entity()->guid, PLUGIN_ID);
	}

	/**
	 * User Boolean Setting
	 */
	public static function USETTING_BOOL($id){

		return filter_var(Functions::USETTING($id), FILTER_VALIDATE_BOOLEAN);
	}

	/**
	 * Plugin Setting
	 */
	public static function SETTING($id){

		return elgg_get_plugin_setting($id, PLUGIN_ID);
	}

	/**
	 * Plugin Boolean Setting
	 */
	public static function SETTING_BOOL($id){

		return filter_var(Functions::SETTING($id), FILTER_VALIDATE_BOOLEAN);
	}

	/**
	 * Clean String to safe html class name
	 */
	public static function seoUrl($string) {
	    //Lower case everything
	    $string = strtolower($string);
	    //Make alphanumeric (removes all other characters)
	    $string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
	    //Clean up multiple dashes or whitespaces
	    $string = preg_replace("/[\s-]+/", " ", $string);
	    //Convert whitespaces and underscore to dash
	    $string = preg_replace("/[\s_]/", "-", $string);
	    return $string;
	}

}

?>
