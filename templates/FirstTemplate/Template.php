<?php

final class Template extends TemplateBase
{
	public function Template ()
	{
		
	}
	
	// Overrides
	public function message ($message)
	{
		if (!isset ($this->messages) || !is_array($this->messages))
			$this->messages = array ();
		
		$this->messages[] = $message;
	}
	
	public function endPage ($pageProperties = NULL)
	{
		if ($pageProperties === NULL)
			$pageProperties = array();
		
		$page_title = isset ($pageProperties['page_title']) ? $pageProperties['page_title'] : _t ('default_page_title');
		unset($pageProperties['page_title']);
		
		// Parse the module output
		$module_content = ob_get_contents();
		parent::parse ($module_content, $pageProperties);
		
		$opt = array_merge(array (
			'page_title' => $page_title,
			'module_content' => $module_content
		));
		
		$opt['body'] = isset ($opt['body']) ? $opt['body'] : _t ("module_no_output");
		
		$this->page = parent::loadFile ("index.html", $opt);
	}
}

$fw_template = new Template ();