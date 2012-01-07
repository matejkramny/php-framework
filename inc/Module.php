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
	 *			   'tooltip' => 'some text displayed in tooltip',
	 *			   'tooltipColour' => 'red' / 'green' / 'black' (default)
	 *			   'id' => 'the id of the element, accessible by javascript'
	 *             // more optional parameters based on the 'type'
	 *         )
	 *     )
	 * )
	 *
	 * Data types:
	 * 	- datatype { // some relevant parameters (value type, like string, bool) }
	 * 	- input { required (bool), value (string), validator (array ( type (string), min (int), max (int), regex (string), error (string) )) }
	 *	- password { required (bool), value (string), validator (array ( type (string), min (int), max (int), regex (string), error (string) )) }
	 *	- file { required (bool), location (string) }
	 *	- radio { required (bool) }
	 *	- checkbox { required (bool), checked (bool) }
	 *  - hidden { value (string) }
	 *  - button { value (string) }
	 *  - submit { value (string) }
	 *  - reset { value (string) }
	 *  - header { legend (string) }
	 *  - headerend
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
    private $validCode;
    
    private function setMethod ($method)
    {
        $m = strtoupper ($method);
        if ($m != "POST" && $m != "GET")
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
        $method = $this->method == "POST" ? $_POST : $_GET;
        
        if (isset($method["submitted"]))
        	$this->submitted = true;
        
        return $this->submitted;
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
        
        $m = $this->method == "POST" ? $_POST : $_GET;
       	
        $valid = isset ($m['valid']) ? $m['valid'] : NULL;
        if ($valid == NULL)
        	return false;
        
        $oDb = DB::getRow ("forms", array ("where" => array ("code" => $valid)));
        if ($oDb == NULL)
        	return false;
    	
    	// Validate form elements
    	$vData = $this->validate ($m, $this->data);
    	
    	// Rebuild form data
    	$this->formCode = NULL;
    	$this->buildForm();
    }
    private function validate ($received, $local)
    {
    	// Validates local data against remote, and modifies $this->data accordingly
    	foreach ($local as $localKey => $localValue)
    	{
    		if (!isset ($localValue["name"])) continue;
    		$required = isset ($localValue["required"]) ? $localValue["required"] : false;
    		
    		$receivedValue = isset ($received[$localValue["name"]]) ? $received[$localValue["name"]] : NULL;
    		
    		if (strtolower($localValue["type"]) == "checkbox")
    		{
    			// Checked
    			$this->data[$localKey]["checked"] = true;
    			
				// Unchecked
    			if ($receivedValue === NULL)
    				$this->data[$localKey]["checked"] = false;
    			
    			if ($required && $receivedValue === NULL)
    			{
    				// It is supposed to be checked
    				$this->data[$localKey]["tooltip"] = _t ("form_field_required");
    				$this->data[$localKey]["tooltipColour"] = "red";
    			}
    			
    			continue;
    		}
			else if (strtolower($localValue["type"]) == "radio")
			{
				// Not submitted / checked any radio button
				if ($required && $receivedValue === NULL)
				{
					$this->data[$localKey]["tooltip"] = _t ("form_field_required");
					$this->data[$localKey]["tooltipColour"] = "red";
					$this->data[$localKey]["checked"] = false;
					
					continue;
				}
				
				// Submitted, but not this radio
				if ($receivedValue != $localValue["value"])
				{
					$this->data[$localKey]["tooltip"] = "";
					$this->data[$localKey]["checked"] = false;
				}
				else
				{
					$this->data[$localKey]["tooltip"] = "";
					$this->data[$localKey]["checked"] = true;
				}
				
				continue;
			}
    		
    		$this->data[$localKey]["value"] = $receivedValue;
    		
    		// Required flag evaluation
    		if ($required && ($receivedValue === NULL || strlen($receivedValue) == 0))
    		{
    			$this->data[$localKey]["tooltip"] = _t ("form_field_required");
    			$this->data[$localKey]["tooltipColour"] = "red";
    			$this->data[$localKey]["value"] = "";
    		}
    		else if (isset ($localValue["validator"]))
    		{
    			// Parse Validator array
    		}
    	}
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
    		$elements .= $this->buildElement ($d);
   		
   		// Add some security and submission check flag into the data array
    	$elements .= $this->buildElement(array(
    		"type" => 'hidden',
    		'value' => true,
    		'name' => 'submitted'
    	));
    	$elements .= $this->buildElement(array(
    		"type" => 'hidden',
    		'value' => $this->getValidCodeForForm (),
    		'name' => 'valid'
    	));
   			
   		$this->formCode = $GLOBALS['fw_template']::loadFile ("Form/form.html", array (
   				"action" => $this->action,
   				"encoding" => $this->encoding,
   				"method" => $this->method,
   				"content" => $elements
   			));
   		
   		return $this->formCode;
   	}
   	private function submittableElementType ($type)
   	{
   		switch (strtolower ($type))
    	{
    		case "input":
    		case "password":
    		case "file":
    		case "radio":
    		case "checkbox":
    		case "hidden":
			case "custom":
    			return true;
    		case "button":
    		case "submit":
    		case "reset":
    		case "header":
    		case "headerend":
    		default:
    			return false;
    	}
   	}
    private function buildElement ($e)
    {
    	switch (strtolower ($e["type"]))
    	{
    		case "input":
    		case "password":
    		case "file":
    		case "hidden":
    		case "button":
    		case "submit":
    		case "reset":
    			return $this->buildInput ($e, strtolower($e["type"]));
    		case "checkbox":
			case "radio":
    			return $this->buildRadioCheckbox ($e, strtolower($e["type"]));
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
    	$tooltip = "";
    	$tooltipColour = "black";
    	
    	if (isset($e['tooltip']))
    		$tooltip = $GLOBALS['fw_template']::loadFile ('Tooltip/tooltip.html', array (
    			'text' => $e['tooltip']
    		));
    	if (isset($e['tooltipColour']))
    		$tooltipColour = $GLOBALS['fw_template']::getTooltipColour ($e['tooltipColour']);
    	
    	return $GLOBALS['fw_template']::loadFile ('Form/input.html', array (
    			"label" => isset ($e['label']) ? $e['label'] : "",
    			"value" => isset ($e['value']) ? " value=\"{$e['value']}\"" : "",
    			"name" => isset ($e['name']) ? " name=\"{$e['name']}\"" : "",
    			"id" => isset ($e['id']) && strlen ($e['id']) > 0 ? " id=\"{$e['id']}\"" : "",
    			"tooltip" => $tooltip,
    			"tooltipColour" => $tooltipColour,
    			"type" => $type
    		));
    }
    private function buildRadioCheckbox ($e, $type)
    {
    	$tooltip = "";
    	$tooltipColour = "black";
    	
    	if (isset($e['tooltip']))
    		$tooltip = $GLOBALS['fw_template']::loadFile ('Tooltip/tooltip.html', array (
    			'text' => $e['tooltip']
    		));
    	if (isset($e['tooltipColour']))
    		$tooltipColour = $GLOBALS['fw_template']::getTooltipColour ($e['tooltipColour']);
    	
    	return $GLOBALS['fw_template']::loadFile ('Form/checkedInput.html', array (
    			"label" => isset ($e['label']) ? $e['label'] : "",
    			"name" => isset ($e['name']) ? " name=\"{$e['name']}\"" : "",
    			"id" => isset ($e['id']) && strlen ($e['id']) > 0 ? " id=\"{$e['id']}\"" : "",
    			"tooltip" => $tooltip,
    			"tooltipColour" => $tooltipColour,
    			"checked" => isset ($e['checked']) && $e['checked'] == true ? "checked=\"yes\"" : "",
				"type" => $type,
				"value" => isset ($e["value"]) ? " value=\"{$e['value']}\"" : ""
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
    private function getValidCodeForForm ()
    {
    	if ($this->validCode != NULL)
    		return $this->validCode;
    	
    	$r = str_shuffle(base64_encode(mt_rand()));
    	DB::insert ("forms", array (
    		"code" => $r,
    		"timestamp" => time()
    	));
    	
    	$this->validCode = $r;
    	
    	return $r;
    }
}
