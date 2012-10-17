<?php

final class HomeModule extends Module
{
	public function HomeModule ()
	{
		// Initializator
		
		// Instantiate database
		$this->setUpDB = true;
		
		// Default action
		$this->defaultAction = "home";
		
		// Prepare forms, files later on used by module
		$this->form = array(
			'method' => 'POST',
			'table' => 'testTable',
			'action' => fw_uri.fw_module_name.'/submitted',
			'data' => array(
				'formHeader' => array(
					'type' => 'header',
					'legend' => 'Test form'
				),
				'uname' => array(
					'type' => 'input',
					'label' => 'Username:',
					'name' => 'username',
					'tooltip' => 'obtained during registration.',
					'required' => true,
					'validator' => array(
						'type' => 'length',
						'min' => 4,
						'max' => 20,
						'error' => 'must be between 4 and 20 characters!'
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
					'name' => 'gender',
					'value' => 'male',
					'required' => true
				),
				'female' => array(
					'type' => 'radio',
					'label' => 'Female:',
					'name' => 'gender',
					'required' => true,
					'value' => 'female'
				),
				'picture' => array (
					'type' => 'file',
					'label' => 'Your picture:',
					'name' => 'picture',
					'required' => true,
					'location' => fw_module_path.'data/',
					'fileName' => 'up_picture_'.time (),
					'maxFileSize' => 4096,
					'extensions' => array (
						'png',
						'jpg',
						'jpeg',
						'gif',
						'bmp'
					)
				),
				'terms' => array(
					'type' => 'checkbox',
					'label' => 'I agree to the terms and conditions:',
					'required' => true,
					'name' => 'terms'
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
	}
	
	public function _home ($args, $argc)
	{
		// When module argument #1 is equal to nothing or 'home'
//		$this->loadFile ('Public/css/sometest.css');
		
		// Print out form.
		$form = new Form ($this->form);
		
		if ($form->formSubmittedAndValid ())
		{
			echo "Yupee, the form was submitted";
			$form->insertData ();
		}
		
		echo $form->getCode().'{HelloWorld}';
		
		// Set up the page via templater
		$this->end (array(
			'page_title' => 'My Module Title',	// Page title
			'HelloWorld' => 'Hello, world.'
		));
	}
	
	public function _submitted ($args, $argc)
	{
		
		$form = new Form ($this->form);
		
		if ($form->formSubmittedAndValid())
		{
			echo "Yup. the form was submitted.";
		}
		else
		{
			$this->redirect (fw_uri.fw_module_name);
		}
			
		$this->end (array(
			'page_title' => 'Form submitted action'
		));
	}
}
