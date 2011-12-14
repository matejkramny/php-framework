<?php

// Incomplete

abstract class Module
{
}

final class Form
{
	// Contructor
	/*
	 * $a = array (
	 *     'action' => 'some url', // optional, default is the same page
	 *     'method' => 'post or get', // optional, default is POST
	 *     'enctype' => 'encoding type', // optional
	 *     'data' => array (
	 *         'somelabel' => array (
	 *             'type' => 'datatype',
	 *             'label' => 'the information before the input, like "Username:"'
	 *             'name' => 'name of the element, to be accessed as $_GET/POST["yournameofelement"]'
	 *             // more optional parameters based on the 'type'
	 *         )
	 *     )
	 * )
	 * 
	 * Data types:
	 * 	- datatype { // some relevant parameters (value type, like string, bool) }
	 * 	- input { required (bool), value (string), validator (array ( length (int), regex (string), error (string) )) }
	 *	- password { required (bool), value (string), validator (array ( length (int), regex (string), error (string) )) }
	 *	- file { required (bool), location (string) }
	 *	- radio ( required (bool) }
	 *	- checkbox ( required (bool) }
	 *	- custom { content (string) }
	 */
	public function Form ($a)
	{
		$this->setAction (isset ($a['action']) ? $a['action'] : "");
		$this->setMethod (isset ($a['method']) ? $a['method'] : "POST");
		$this->setEncoding (isset ($a['enctype']) ? $a['enctype'] : NULL);
		$this->setData ($a['data']);
		
		if (!$this->formSubmitted ())
			$this->buildForm ();
		else
		{
			$this->validateForm ();
		}
	}
	
	private $action;
	private function setAction ($action)
	{
		$this->action = $action;
	}
	private $method;
	private function setMethod ($method)
	{
		$m = strtoupper ($method);
		if ($m != "POST" || $m != "GET")
			$m = "POST";

		$this->method = $m;
	}
	private $encoding;
	private function setEncoding ($encoding)
	{
		// Only two encoding types can be accepted, if NULL (undefined) or not equal to multipart/form-data, a default enctype is set
		if ($encoding == NULL || strtolower($encoding) != "multipart/form-data")
			$encoding = "application/x-www-form-urlencoded";

		$this->encoding = $encoding;
	}
	private $data;
	private function setData ($data)
	{
		var_dump ($data);

	}
	
	private $submitted;
	public function formSubmitted ()
	{
		// Not worth wasting time reevaluating form submission once it has been done
		if ($this->submitted != NULL)
			return $this->submitted;
		
		// Check if form is submitted
	}
	private $valid;
	public function formValid ()
	{
		if ($this->valid == NULL)
			$this->valid = validateForm ();
		
		return $this->valid;
	}
	public function formSubmittedAndValid ()
	{
		return ($this->formSubmitted() && $this->formValid()) ? true : false;
	}
	
	private function validateForm ()
	{
		// Validate form.
	}
	
	private $addedScriptsToTemplate;
	public function getCode ()
	{
		// Get the HTML out
		// Including necessary form CSS and JS
		
		if (!$this->addedScriptsToTemplate == NULL || !$this->addedScriptsToTemplate == false)
		{
			$this->addedScriptsToTemplate = true;
			
			// Add form CSS and JS to current Template
		}
	}
}
