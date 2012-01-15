<?php

final class Template extends TemplateBase
{
	public function Template ()
	{
		
	}
	
	// Overrides
	public function message ($message)
	{
		
	}
	
	public function endPage ($pageProperties = NULL)
	{
		if ($pageProperties === NULL)
		{
			$pageProperties = array();
		}
		
		$page_title = isset ($pageProperties['page_title']) ? $pageProperties['page_title'] : _t ('default_page_title');
		unset($pageProperties['page_title']);
		
		// Parse the module output
		$module_content = ob_get_contents();
		parent::parse ($module_content, $pageProperties);
		
		$opt = array_merge(array (
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
			),
			'page_title' => $page_title,
			'module_content' => $module_content
		));
		
		$opt['body'] = isset ($opt['body']) ? $opt['body'] : _t ("module_no_output");
		
		$this->page = parent::loadFile ("full.html", $opt);
	}
}

$fw_template = new Template ();