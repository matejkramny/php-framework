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
				'__start' => '<table><thead><th>ID</th><th>Label</th></thead><tbody>',
				'__data' => '<tr><td>{ID}</td><td>{LABEL}</td></tr>',
				'__end' => '</tbody></table>',
				array (
					'ID' => 1,
					'LABEL' => 'first label'
				      ),
				array (
					'ID' => 2,
					'LABEL' => 'second label'
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
