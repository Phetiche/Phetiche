<?php

class Phetiche_apc {

	public static function fetch($key)
	{
		if (Phetiche_config::get('default/apc_cache') && function_exists('apc_fetch')) {
			return apc_fetch($key);
		} else {
			return false;
		}
	}

	public static function store($key, $value, $ttl = 180)
	{
		if (Phetiche_config::get('default/apc_cache') && function_exists('apc_store')) {
			apc_store($key, $value, $ttl);
		}
	}	

	public static function delete($key)
	{
		if (Phetiche_config::get('default/apc_cache') && function_exists('apc_delete')) {
			apc_delete($key);
		}
	}

}