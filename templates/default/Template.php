<?php

final class Template extends TemplateBase
{
	function haveFun ()
	{
		$contents = parent::loadFile ("base.html", array (
			'PAGE_TITLE' => "My Page TITLE",
			'PAGE_CONTENT' => "My Page CONTENT",
			'ExampleIF' => array (
				'type' => 'if',
				'condition' => 'true',
				'true' => "Hello world!",
				'false' => "No HELL!"
			)
		));
		
		echo $contents;
	}
	
	// Overrides
	static function message ($message)
	{
		
	}
}

$fw_template = new Template ();

?>