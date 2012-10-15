<?php

final class Form
{
	/*
	 * ------------------------> Usage <------------------------
	 * $a = array ( // Data
	 *     'action' => 'some url', // optional, default is the same page
	 *     'method' => 'post or get', // optional, default is POST
	 *     'enctype' => 'encoding type', // optional
	 *	   'table' => '', // optional, mysql table to save data into
	 *     'data' => array ( // required
	 *         'somelabel' => array ( // somelabel should be equal to a database record
	 *             'type' => 'datatype',
	 *             'label' => 'the information before the input, like "Username:"',
	 *             'name' => 'name of the element, to be accessed as $_GET/POST["yournameofelement"]',
	 *			   'tooltip' => 'some text displayed in tooltip',
	 *			   'tooltipColour' => 'red' / 'green' / 'black' (default)
	 *			   'id' => 'the id of the element, accessible by javascript'
	 *             // more optional parameters based on the 'type'
	 *         )
	 *     )
	 * );
	 * $form = new Form ($a); // to insert some data to database
	 * $form = new Form ($a, DB::getRow (etc..)); // to update some data located in database
	 * 
	 * if ($form->formSubmittedAndValid ())
	 * {
	 *		$form->insertData (); // to insert the submitted data into the database table
	 *		$form->updateData (); // to update the submitted data into the database table
	 * }
	 * 
	 * Data types:
	 * 	- datatype { // some relevant parameters (value type, like string, bool) }
	 * 	- input { required (bool), value (string), validator (array) }
	 *	- password { required (bool), value (string), validator (array) }
	 *	- file { required (bool), location (string), filename (string), maxFileSize (int) } // filename is optional, but will be automatically generated if not present, maxFileSize is also optional, needs to be in kilobytes. Default limit is 4096 kilobytes (4MB)
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
	
	// Contructor
    public function Form ($a, $existing = NULL)
    {
        $this->action = isset ($a['action']) ? $a['action'] : "";
        $this->setMethod (isset ($a['method']) ? $a['method'] : "POST");
        $this->data = $a['data'];
        $this->setEncoding (isset ($a['enctype']) ? $a['enctype'] : NULL);
        $this->table = isset ($a['table']) ? $a['table'] : "";
		$this->databaseRecord = is_array($existing) ? $existing : NULL;
		
		$this->applyDatabaseRecord ();
		$this->buildForm ();
		
        if ($this->formSubmitted ())
            $this->validateForm ();
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
	private $table;
	private $receivedData;
	private $databaseRecord;
    
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
        // If a file upload element is present in form, multipart/form-data is chosen automatically
        // Custom choice encoding is present for custom elements, which can be file uploads too
        
		$fileUploadForm = false;
		foreach ($this->data as $k => $v)
		{
			if ($v["type"] == "file")
			{
				$fileUploadForm = true;
				break;
			}
		}
		
		if ($encoding == NULL || strtolower($encoding) != "multipart/form-data")
            $encoding = "application/x-www-form-urlencoded";
		
		if ($fileUploadForm)
			$encoding = "multipart/form-data";
		
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
            $this->valid = $this->validateForm ();

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
       	
       	// HTTP Form Valid field
        $valid = isset ($m['valid']) ? $m['valid'] : NULL;
        if ($valid === NULL)
        	return false;
        
        $oDb = DB::getRow ("forms", array ("where" => array ("code" => $valid)));
        if ($oDb === NULL)
        	return false;
    	
    	// Unset values from remote
    	unset($m["valid"], $m["submitted"]);
    	
    	$this->receivedData = $m;
    	
    	// Validate form elements
    	$this->valid = $this->validate ($m, $this->data);
    	
    	// Rebuild form data
    	$this->formCode = NULL;
    	$this->buildForm();
    	
    	return $this->valid;
    }
    private function validate ($received, $local)
    {
    	// Validates local data against remote, and modifies $this->data accordingly
    	
    	$valid = true;
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
    				
    				$valid = false;
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
					
					$valid = false;
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
			else if (strtolower($localValue["type"] == "file"))
			{
				// File upload
				if (!isset($localValue["name"])) continue;
				$required = isset ($localValue["required"]) ? $localValue["required"] : false;
				
				if (!is_uploaded_file($_FILES[$localValue["name"]]["tmp_name"]))
				{
					if ($required)
					{
						// Required and not present
						$this->data[$localKey]["tooltipColour"] = "red";
						$this->data[$localKey]["tooltip"] = _t ("form_field_required");
					
						$valid = false;
					}
					
					continue;
				}
				if (is_array($_FILES[$localValue["name"]]["name"])) continue; // Multiple upload under same name
				
				$destination = isset ($localValue["location"]) ? $localValue["location"] : fw_dir_temp;
				$fileName = isset ($localValue["fileName"]) ? $localValue["fileName"] : "upload_".time();
				$maxFileSize = isset ($localValue["maxFileSize"]) ? $localValue["maxFileSize"] : fw_settings_upload_file_size;
				
				// get info about the file
				$oPrep = $this->prepareFileUpload ($_FILES[$localValue["name"]], $destination);
				
				if (!$oPrep["writeableDirectory"])
				{
					$this->data[$localKey]["tooltipColour"] = "red";
					$this->data[$localKey]["tooltip"] = _t ("file_upload_server_error");
					
					trigger_error("Form - non-writeable directory: {$destination}", E_USER_WARNING);
					
					$valid = false;
					continue;
				}
				
				if (isset ($localValue["extensions"]) && !in_array($oPrep["extension"], $localValue["extensions"]))
				{
					$this->data[$localKey]["tooltipColour"] = "red";
					$this->data[$localKey]["tooltip"] = _t ("file_upload_not_allowed_extension");
					
					$valid = false;
					continue;
				}
				
				if ($oPrep["file_size"] >= $maxFileSize)
				{
					$this->data[$localKey]["tooltipColour"] = "red";
					$this->data[$localKey]["tooltip"] = _t ("file_upload_over_size");
					
					$valid = false;
					continue;
				}
				
				$oPrep["fullPath"] = "{$destination}/{$fileName}.{$oPrep["extension"]}";
				
				$this->finishFileUpload ($oPrep);
				
				// Add entry to database, store ID of the inserted record.
				DB::insert ("file_uploads", array (
					"location" => $destination,
					"filename" => $fileName,
					"extension" => $oPrep["extension"],
					"timestamp" => time()
				));
				
				// Inject receivedData record to track the file
				$this->receivedData[$localValue["name"]] = DB::last_insert_id ();
				
				continue;
			}
    		
    		$this->data[$localKey]["value"] = $receivedValue;
    		
    		// Required flag evaluation
    		if ($required && ($receivedValue === NULL || strlen($receivedValue) == 0))
    		{
    			$this->data[$localKey]["tooltip"] = _t ("form_field_required");
    			$this->data[$localKey]["tooltipColour"] = "red";
    			$this->data[$localKey]["value"] = "";
    			
    			$valid = false;
    		}
    		else if (isset ($localValue["validator"]))
    		{
    			// Parse Validator array
    			
    			// TODO
    		}
    	}
    	
    	return $valid;
    }
    private function prepareFileUpload ($f, $destination)
    {
    	// Return information about a file upload
    	return array (
    		"temporary_path" => $f["tmp_name"],
    		"file_name" => $f["name"],
    		"extension" => substr($f["name"], strrpos($f["name"],'.')+1, strlen($f["name"])-1),
    		"file_size" => filesize($f["tmp_name"]) / 1024,
    		"writeableDirectory" => is_writeable($destination),
    		"destination" => $destination
    	);
    }
    private function finishFileUpload ($f)
    {
    	// Move uploaded file to location specified
    	move_uploaded_file($f["temporary_path"], $f["fullPath"]);
    }
    public function getData ()
    {
    	// Returns submittable and validated / parsed data in form of:
    	// array (
    	// 		"key" => "value"
    	// );
    	
    	$r = array();
    	
    	foreach ($this->data as $k => $v)
    	{
    		if (!$this->submittableElementType ($v["type"]))
    			continue;
    		
    		if (!isset ($v["name"]))
    			continue; // not submitted/able element
    		
    		$r[$v["name"]] = isset ($this->receivedData[$v["name"]]) ? $this->receivedData[$v["name"]] : "";
    	}
    	
    	return $r;
    }
	public function insertData ($additionalData = NULL, $table = NULL)
	{
		// Saves received data into the database table
		
		if (!$this->formSubmittedAndValid ())
			return false;
		
		$d = $this->getData ();
		$aData = is_array($additionalData) ? $additionalData : array ();
		$t = NULL;
		
		if ($this->table === NULL && $table === NULL)
			return false; // Cannot save data, no table specified
		
		// $table is higher priority than $this->table
		$t = $table;
		if ($table === NULL)
			$t = $this->table;
		
		$d = array_merge($d, $aData);
		
		return DB::insert ($t, $d);
	}
	public function updateData ($id ,$table = NULL)
	{
		// Updates the database record in database
		
		if (!$this->formSubmittedAndValid ())
			return false;
		
		$d = $this->getData ();
		$t = NULL;
		
		if ($this->table === NULL && $table === NULL)
			return false; // Cannot save data, no table specified
		
		// $table is higher priority than $this->table
		$t = $table;
		if ($table === NULL)
			$t = $this->table;
		
		return DB::update ($t, array (
			'fields' => $d,
			'where' => array (
				'id' => $id
			)
		));
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
    
    private function applyDatabaseRecord ()
    {
    	if ($this->databaseRecord === NULL)
    		return;
    	
    	foreach ($this->data as $k => $v)
    	{
    		if (!isset ($v["name"])) continue;
    		if (!isset ($this->databaseRecord[$v["name"]])) continue;
			
    		$dbV = $this->databaseRecord[$v["name"]];
    		
    		if ($v["type"] == "radio")
    		{
    			if ($dbV == $v["value"])
    				$this->data[$k]["checked"] = true;
    			else
    				$this->data[$k]["checked"] = false;
    			
    			continue;
    		}
    		else if ($v["type"] == "checkbox")
    		{
    			if ($dbV == "on")
    				$this->data[$k]["checked"] = true;
    			else
    				$this->data[$k]["checked"] = false;
    			
    			continue;
    		}
    		
    		$this->data[$k]["value"] = $dbV;
    	}
    }
    private function buildForm ()
    {
    	if ($this->formCode !== NULL)
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
