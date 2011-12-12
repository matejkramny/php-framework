<?php

final class Template extends TemplateBase
{
	function haveFun ()
	{
		$contents = parent::loadFile ("skeleton.html", array (
			'PAGE_TITLE' => "My Page TITLE",
			'PAGE_CONTENT' => "My Page CONTENT",
			'ExampleIF' => array (
				'type' => 'if',
				'condition' => 'true',
				'true' => "Hello world!",
				'false' => "No HELL!"
			),
			'ExampleLoop' => array (
				'type' => 'loop',
				'__start' => '<strong>This is the start of the loop</strong>',
				'__data' => '<font style="color:red;">This is some data of the {MYVAR}</font>',
				'__end' => '<strong>This is the end of the loop</strong>',
				array (
					'MYVAR' => 'Hello123'
				      ),
				array (
					'MYVAR' => 'Hello World'
				      )
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
