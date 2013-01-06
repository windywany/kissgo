<?php
class PreferenceEntity extends Model {
	var $option_id_nak; // INT UNSIGNED NULL AUTO_INCREMENT,
	
	var $option_group_sr = 'base'; // VARCHAR(16) NOT NULL DEFAULT 'base' COMMENT
	// '选项组',
	var $option_name_sr; // VARCHAR(16) NOT NULL COMMENT '选项名',
	var $option_value_sr = ''; // TEXT NULL COMMENT '选项值',

	public static function getOption($name, $default = '', $emp = false) {
		static $options = false;
		if ($options === false) {
			$com = new PreferenceEntity ();
			$options = $com->retrieve ( 'option_name,option_value' );
			$options = $options ? $options->toArray ( 'option_name', 'option_value' ) : array ();
		}
		if (isset ( $options [$name] )) {
			return empty ( $options [$name] ) && ! $emp ? $default : $options [$name];
		} else {
			return $default;
		}
	}
	
	public static function getOptionInGroup($name, $group, $default = '', $emp = false) {
		static $options = array ();
		if (! isset ( $options [$group] )) {
			$options [$group] = apply_filter ( 'get_option_' . $group, array () );
		}
		if (isset ( $options [$group] [$name] )) {
			return empty ( $options [$group] [$name] ) && ! $emp ? $default : $options [$group] [$name];
		} else {
			return $default;
		}
	}
}