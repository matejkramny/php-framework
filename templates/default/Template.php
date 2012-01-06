<?php

final class Template extends TemplateBase
{
	function haveFun ()
	{
		$opt = array(
			'method' => 'GET',
			'data' => array(
				'formHeader' => array(
					'type' => 'header',
					'legend' => 'Test form'
				),
				'uname' => array(
					'type' => 'input',
					'label' => 'Username:',
					'name' => 'username',
					'required' => true,
					'validator' => array(
						'type' => 'length',
						'min' => 4,
						'max' => 20,
						'error' => 'Username must be between 4 and 20 characters!'
					)
				),
				'pword' => array(
					'type' => 'password',
					'label' => 'Password:',
					'name' => 'password',
					'required' => true,
					'validator' => array(
						'type' => 'regex',
						'regex' => '/[^A-Za-z0-9.#\\-$]/', // Invalid character test
						'error' => 'Password contains special characters!'
					)
				),
				'male' => array(
					'type' => 'radio',
					'label' => 'Male:',
					'name' => 'gender'
				),
				'female' => array(
					'type' => 'radio',
					'label' => 'Female:',
					'name' => 'gender'
				),
				'submit' => array(
					'type' => 'submit',
					'value' => 'Submit form'
				),
				'formHeaderEnd' => array(
					'type' => 'headerend'
				),
			)
		);
		$form = new Form ($opt);
		
		echo parent::loadFile ("skeleton.html", array (
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
				),
			'PAGE_FORM' => $form->getCode ()
		));
	}
	
	// Overrides
	static function message ($message)
	{
		
	}
}

$fw_template = new Template ();

?>
