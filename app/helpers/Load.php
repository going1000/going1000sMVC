<?php
class Load {
	public static function helper($name) {
		if (!class_exists($name, false)) {
			include_once G_HELPER_PATH.'/'.$name.PHP;
		}
	}
}
