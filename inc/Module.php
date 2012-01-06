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
	 *     'data' => array ( // required
	 *         'somelabel' => array (
	 *             'type' => 'datatype',
	 *             'label' => 'the information before the input, like "Username:"',
	 *             'name' => 'name of the element, to be accessed as $_GET/POST["yournameofelement"]',
	 *			   'id' => 'the id of the element, accessible by javascript'
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
	 *	- radio { required (bool) }
	 *	- checkbox { required (bool) }
	 *  - hidden { value (string) }
	 *  - button { value (string) }
	 *  - submit { value (string) }
	 *  - reset { value (string) }
	 *  - header { legend (string) }
	 *  - headerend { }
	 *	- custom { content (string) }
	 */
    public function Form ($a)
    {
        $this->action = isset ($a['action']) ? $a['action'] : "";
        $this->setMethod (isset ($a['method']) ? $a['method'] : "POST");
        $this->setEncoding (isset ($a['enctype']) ? $a['enctype'] : NULL);
        $this->data = $a['data'];

        if (!$this->formSubmitted ())
            $this->buildForm ();
        else
        {
            $this->validateForm ();
        }
    }

    private $action;
    private $method;
    private $encoding;
    private $data;
    private $submitted;
    private $valid;
	private $addedScriptsToTemplate;
    private $formCode;
    
    private function setMethod ($method)
    {
        $m = strtoupper ($method);
        if ($m != "POST" || $m != "GET")
            $m = "POST";

        $this->method = $m;
    }
    private function setEncoding ($encoding)
    {
        // Only two encoding types can be accepted, if NULL (undefined) or not equal to multipart/form-data, a default enctype is set
        if ($encoding == NULL || strtolower($encoding) != "multipart/form-data")
            $encoding = "application/x-www-form-urlencoded";

        $this->encoding = $encoding;
    }
    public function formSubmitted ()
    {
        // Not worth wasting time reevaluating form submission once it has been done
        if ($this->submitted != NULL)
            return $this->submitted;

        // Check if form is submitted
        
   	}
   	
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
        // Validate form
        
        // Cannot validate form if not submitted
        if (!$this->formSubmitted ())
        	return false;
        
        
    }
    public function getCode ()
    {
        // Get the HTML out
		// Including necessary form CSS and JS

		if (!$this->addedScriptsToTemplate == NULL || !$this->addedScriptsToTemplate == false)
		{
			$this->addedScriptsToTemplate = true;
			
			// Add form CSS and JS to current Template
		}
		
		return $this->buildForm ();
	}
    
    private function buildForm ()
    {
    	if ($this->formCode != NULL)
    		return $this->formCode;
    	
    	// Build form
    	$elements = "";
    	
    	foreach ($this->data as $d)
    	{
    		$elements .= $this->buildElement ($d);
    	}
   		
   		return $GLOBALS['fw_template']::loadFile ("Form/form.html", array (
   				"action" => $this->action,
   				"encoding" => $this->encoding,
   				"method" => $this->method,
   				"content" => $elements
   			));
   	}
    private function buildElement ($e)
    {
    	switch (strtolower ($e["type"]))
    	{
    		case "input":
    		case "password":
    		case "file":
    		case "radio":
    		case "checkbox":
    		case "hidden":
    		case "button":
    		case "submit":
    		case "reset":
    			return $this->buildInput ($e, strtolower($e["type"]));
    		case "header":
    			return $this->buildHeader ($e);
    		case "headerend":
    			return $this->buildHeader ($e, true);
    		case "custom":
    			return $this->buildCustom ($e);
    		default:
    			return false;
    	}
    }
    private function buildInput ($e, $type="input")
    {
    	return $GLOBALS['fw_template']::loadFile ('Form/input.html', array (
    			"label" => isset ($e['label']) ? $e['label'] : "",
    			"value" => isset ($e['value']) ? " value=\"{$e['value']}\"" : "",
    			"name" => isset ($e['name']) ? " name=\"{$e['name']}\"" : "",
    			"id" => isset ($e['id']) && strlen ($e['id']) > 0 ? " id=\"{$e['id']}\"" : "",
    			"type" => $type
    		));
    }
    private function buildHeader ($e, $end = false)
    {
    	$legend = isset($e['legend']) ? $e['legend'] : NULL;
    	if ($legend == NULL && !$end) return NULL;
    	
    	if ($end)
    		return $GLOBALS['fw_template']->loadFile ("Form/header_end.html");
    	
    	return $GLOBALS['fw_template']::loadFile ("Form/header.html", array (
    			"legend" => $legend
    		));
    }
    private function buildCustom ($e)
    {
    	if (!isset($e['content']))
    		return "";
    	
    	return $GLOBALS['fw_template']::loadFile ("Form/customInput.html", array(
    			"label" => isset($e['label']) && $e['label'] != "" ? $e['label'] : "No label",
    			"content" => $e['content']
    		));
    }
}
