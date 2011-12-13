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
		
	}
}
