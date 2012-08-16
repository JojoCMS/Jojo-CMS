<?php

class Jojo_Dwoo_Smarty_Adapter extends Dwoo_Smarty_Adapter
{
	public function fetch($filename, $cacheId=null, $compileId=null, $display=false)
	{
		/* if in mobile mode, and template.mob.tpl exists, serve template.mob.tpl instead of template.tpl */
		if (Jojo::isMobile() && (strpos($filename, '.mob.tpl')===false)) {
    		$mobile_filename = str_replace('.tpl', '.mob.tpl', $filename);
    		foreach(Jojo::listPluginsReverse('templates/'.$mobile_filename) as $new_filename) {
    		    $filename = basename($new_filename);//todo - some templates legitimately work from a subfolder
    		    break;
    		}
		}
		return parent::fetch($filename, $cacheId, $compileId, $display);
	}

}


