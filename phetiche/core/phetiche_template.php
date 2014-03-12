<?php

/**
 * The Phetiche template handler
 * 
 * @file			phetiche/core/phetiche_template.php
 * @description		The template object.
 * @author			Stefan Aichholzer <yo@stefan.ec>
 * @package			Phetiche/core
 * @license			BSD/GPLv2
 * 
 * @deprecated
 * This file will not be maintained and is due to removal.
 *
 * (c) copyright Stefan Aichholzer
 * This source file is subject to the BSD/GPLv2 License.
 */
final class Phetiche_template {

	private $request_controller = null;
	private $template = null;

	public function __construct($invoking_controller = '')
	{
		if ($invoking_controller) {	
			$this->request_controller = new ReflectionObject($invoking_controller);
		}
		
		if (!$this->template) {
			$this->template = Phetiche_config::get('tplEngine');
		}
	}


	public function render($template_name = '')
	{
		/**
		 * If no template name was provided, we try to render
		 * the template named after the rendering controller.
		 */
		if (!$template_name && $this->request_controller) {
			$template_name = $this->request_controller->name;
		}

		$this->template->display($template_name . '.tpl');
	}


	/*
	
	 $this->templateStartForm('name', 'action', 'POST')
			   ->addInput('name', 'type', 'placeholder', 'id', array('class', 'Pepe', 'Tio', 'Sample'))
			   ->addInput('name', 'type', 'placeholder', 'id', 'class name sample')
			   ->addInput('name', 'type', 'placeholder', 'id', 'class')
			 ->templateEndForm()
			 ->templateRender();
	 
	 */


	
	public function templateStartForm($name = '', $action = '', $method = '')
	{
		if (!$this->template) {
			$this->templatePrepare();
		}
				
		$name = ($name) ? $name : $this->request_controller->name . 'Form';
		$action = ($action) ? $action : Phetiche_server::url();
		$method = ($method) ? $method : 'POST';

		$this->templateForm = '<form name="'.$name.'" action="'.$action.'" method="'.$method.'">';
		return $this;
	}
	

	public function templateEndForm()
	{
		$this->templateForm .= '</form>';
		$this->template->assign($this->request_controller->name . 'Form', $this->templateForm);

		return $this;
	}


	public function addInput($name = '', $type = '', $placeholder = '', $id = '', $class = '')
	{
		$name = ($name) ? $name : $this->request_controller->name . 'Input[]';
		$type = ($type) ? $type : 'text';
		$id = ($id) ? $id : $this->request_controller->name . 'Id';
		
		if (is_array($class)) {
			$css_class = implode(" " , $class);
		} else {
			$css_class = ($class) ? $class : $this->request_controller->name . 'Class';
		}

		$this->templateForm .= '<input name="'.$name.'" type="'.$type.'" placeholder="'.$placeholder.'" id="'.$id.'" class="'.$css_class.'" />';
		
		return $this;
	}

}
