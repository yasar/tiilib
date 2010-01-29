<?php
/**
 * Created by IntelliJ IDEA.
 * User: yasar
 * Date: 29-Jan-2010
 * Time: 1:18:57 AM
 * To change this template use File | Settings | File Templates.
 */

	class TiiController extends TiiCore{
		/**
		 * @var TiiTemplate
		 */
		protected $Template;

		public function __construct(){
			parent::__construct();

			$this->path = $this->GetCreatorsPath();
			
			$this->Template = new TiiTemplate();
		}

		protected function SetTemplate($template_file, $absolute_path = false){
			$this->Template->SetTemplate($template_file, $absolute_path);
			return $this->Template;
		}
	}