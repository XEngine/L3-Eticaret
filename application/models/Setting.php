<?php

class Setting extends Eloquent{

	public static function getConfig($array,$debug = false)
    {
		$result = true;
		$debug = array();
		$cfgs = array();

		/*

		if ( Cache::has('config') )
		{
			$cfgs = Cache::get('config');
		}
		else
		{
			$cfgs = XConfig::all();
			Cache::forget('config'
			Cache::put('config',$cfgs,10);			
		}
		*/
		$cfgs = Setting::all();
		while ($config_valueTMP = current($array)) {

		  	foreach ($cfgs as $cfg)
			{
			     if ($cfg->setting  == key($array) )
			     {
			     	if ( $cfg->value != $config_valueTMP )
			     		{
			     			$result = false;
			     			if ( $debug )
			     			{
			     				array_push($debug, $cfg->setting);
			     			}
			     		}
			     	
			     }			     
			}
		    next($array);
		}
		if ( $debug && $result == false)
		{
			return $debug;
		}
		else
		{
			return $result;
		}
	}
	public static function obtain(){
		$configs = Setting::all();
		$result = array();

		foreach($configs as $item)
		{
			$result[$item->setting] = $item->value;
		}

		return $result;
	}
}