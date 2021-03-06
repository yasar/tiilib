<?php 
/********************************************************
$Rev$:
$Author$:
$Date$:
*********************************************************/ 

class TiiMenuItem{
	public $id;
	public $name;
    public $text;
    public $link;
    public $target = '_self';
    public $title;
	public $is_active =true;
	public $is_deleted = false;
	public $is_archived = false; 
	public $allow_new = true;
	public $allow_edit = true;
	public $allow_delete = true;
	private $is_current=false;
	private $on_class='on';
	/**
	 * @var TiiMenu
	 */
	public $parent = null;
	
    /**
    * @var TiiMenu
    */
    private $submenu;
	
    /**
    * @return TiiMenuItem
    */
    public function ID($s){$this->id = $s; return $this;}
    
    /**
    * @return TiiMenuItem
    */
    public function IsActive($s){$this->is_active = $s; return $this;}
    
    /**
    * @return TiiMenuItem
    */
    public function IsDeleted($s){$this->is_deleted = $s; return $this;}
    
    /**
    * @return TiiMenuItem
    */
    public function IsArchived($s){$this->is_archived = $s; return $this;}
    
    /**
    * @return TiiMenuItem
    */
    public function Text($s){$this->text = $s; return $this;}
    
    /**
    * @return TiiMenuItem
    */
    public function AllowNew($s){$this->allow_new = $s; return $this;}
    
    /**
    * @return TiiMenuItem
    */
    public function AllowEdit($s){$this->allow_edit = $s; return $this;}
    
    /**
    * @return TiiMenuItem
    */
    public function AllowDelete($s){$this->allow_delete = $s; return $this;}
    
    /**
    * @return TiiMenuItem
    */
    public function Link($s){$this->link = $s; return $this;}
    
    /**
    * @return TiiMenuItem
    */
    public function Target($s){$this->target = $s; return $this;}
    
    /**
    * @return TiiMenuItem
    */
    public function Title($s){$this->title = $s; return $this;}
    
    public function __construct($name='', $text='', $link='', $target='_self', $title=''){
    	$this->name = $name;
        $this->text = $text;
        $this->link = $link;
        $this->target = $target;
        $this->title = $title;
    }
	
	public function __destruct(){
		unset($this->submenu);
	}
	
	public function __toString(){
		return $this->name;
	}
	
	/**
	 * @return TiiMenu
	 */
	public function GetParentMenu(){
		if (is_null($this->parent)) return false;
		$this->parent->SetLastAccessedMenu($this->parent);
		return $this->parent;
	}

    /**
    * Set the submenu
    *
    * @param TiiMenu $submenu
    * @return TiiMenuItem
    */
    public function SetSubMenu(TiiMenu $menu){
        $this->submenu =& $menu;
		$this->submenu->parent =& $this;
        return $this;
    }
	
	/**
	 * 
	 * @param object $class_name
	 * @return TiiMenuItem 
	 */
	public function SetOnClass($class_name){
		$this->on_class = $class_name;
		return $this;
	}
	
	/**
	 * 
	 * @return String 
	 */
	public function GetOnClass(){
		return $this->on_class;
	}
    
    /**
    * @return boolean
    */
    public function HasSubMenu(){
        return isset($this->submenu);
    }
    
    /**
    * @return TiiMenu
    */
    public function &GetSubMenu(){
    	$this->parent->SetLastAccessedMenu($this->submenu);
        return $this->submenu;
    }
	
	/**
	 * @return mixed TiiMenuItem | boolean
	 * @param boolean $bool[optional]
	 */
	public function IsCurrent($bool = null){
		if (! is_null($bool)){
			$this->is_current = $bool;
			return $this;
		}
		return $this->is_current;
	}
}

class TiiMenu implements IteratorAggregate{
	const DEBUG = false;
	
    public $name;
    private $items=array();
    //private $count=0;
	private $last_item_name;
	private $is_current = false;
	//private $current_item_class = 'on2';
	private $ctr=0;
	private $is_built = false;
	/**
	 * @var TiiMenuItem
	 */
	public $parent = null;
    
    /**
    * holds the html codes for this menu generated by build function
    * 
    * @var string
    */
    private $html;
	
	private $level=1;

    private $last_menu_accessed;
    
	/**
	 * @param TiiMenu $menu
	 * @return TiiMenu 
	 */
	public function SetLastAccessedMenu(TiiMenu $menu){
		self::$last_menu_accessed =& $menu;
		return $this;
	}
	
	/**
	 * @return TiiMenu 
	 */
	public function GetLastAccessedMenu(){
		return isset(self::$last_menu_accessed) ? self::$last_menu_accessed : $this;
	}
	
    public function __construct($name=''){
        $this->name = $name;
    }

	/**
	 * @return TiiMenuItem
	 */
	public function GetParentItem(){
		return $this->parent;
	}

	/**
	 * @return boolean
	 */
	public function HasParentItem(){
		return ! is_null($this->parent) ? true : false;
	}
	
	/**
	 * @return TiiMenu
	 */
	public function GetParentMenu(){
		return ! is_null($this->parent) ? $this->parent->parent : false;
	}

    public function getIterator() {
        return new TiiMenu($this->items);
    }
	
	/**
	 * @param string $name
	 * @return TiiMenuItem
	 */
	public function Get($name=''){
		empty($name) && $name = $this->last_item_name;
		
		return $this->items[$name];
	}

    /**
    * add a new TiiMenuItem into TiiMenu
    *
    * @param TiiMenuItem $value
    * @return TiiMenu
    */
    public function Add(TiiMenuItem $MI) {
		$this->last_item_name = $MI->name;
		$MI->parent = $this;
        $this->items[$MI->name] = $MI;
		return $this;
    }
    
    /**
    * Build the html code which will render the menu
    * 
    * @return TiiMenu
    */
    public function Build($max_level = 0, $holder_class=''){
        $li = array();
		$MI = new TiiMenuItem();
        foreach($this->items as $MI){
        	if (! $MI->is_active || $MI->is_deleted) continue;
            $li[]='<li id="tii-menu-'.$MI->name.'" tii="{id: '.(empty($MI->id)?'\'\'':$MI->id).', allow: '.$MI->allow_new.$MI->allow_edit.$MI->allow_delete.'}" class="'.($MI->IsCurrent() ? $MI->GetOnClass() : '').'">';
            if (! empty($MI->link)) $li[]='<a href="'.$MI->link.'" title="'.$MI->title.'">';
            $li[]=$MI->text;
            if (! empty($MI->link)) $li[]='</a>';
            if ( $MI->HasSubMenu() && ($max_level == 0 || self::$level < $max_level)){
            	self::$level ++; 
				$li[]=$MI->GetSubMenu()->Build()->GetHTML();
				self::$level --;
			}
            $li[]='</li>';
        }
        
        $this->html = '<ul id="tii-menu-holder-'.$this->name.'"'.(!empty($holder_class) ? ' class="'.$holder_class.'"' : '').'>'.implode('', $li).'</ul>';
        
		$this->is_built = true;
        return $this;
    }
    
    /**
    * @return string
    */
    public function GetHTML(){
    	if (! $this->is_built) $this->Build();
        return $this->html;
    }
	
	/**
	 * @return TiiMenu
	 */
	public function SetCurrent($class_name){
		//self::$current_item_class = $class_name;
		
		$link = $_SERVER['REQUEST_URI'];
		$found_item = $this->FindMenuItemByLink($link);
		
		if ($found_item === false) return $this;
		//var_dump($found_item);
		$this->SetCurrentTree($found_item, $class_name);
		return $this;
	}
	
	/**
	 * 
	 * @param object $link
	 * @return TiiMenuItem
	 */
	public function FindMenuItemByLink($link){
		$item = new TiiMenuItem();
		foreach($this->items as $item){
			$item->link = str_replace('http://'.$_SERVER['HTTP_HOST'],'',$item->link);
			//error_log($item->link);
			if ($item->link == $link) return $item;
			
			if ($item->HasSubMenu()) {
				$sub_item = $item->GetSubMenu()->FindMenuItemByLink($link);
				if ($sub_item !== false) return $sub_item;
			}
		}
		return false;
	}
	
	/**
	 * 
	 * @param object $name
	 * @return TiiMenuItem
	 */
	public function FindMenuItemByName($name){
		$item = new TiiMenuItem();
		foreach($this->items as $item){
			if ($item->name == $name) return $item;
			
			if ($item->HasSubMenu()) {
				$sub_item = $item->GetSubMenu()->FindMenuItemByName($name);
				if ($sub_item !== false) return $sub_item;
			}
		}
		return false;
	}
	
	/**
	 * @param object $name
	 * @return TiiMenu
	 */
	public function FindMenuByName($name)
	{
		if ($this->name == $name) return $this;
		$M = null; //new TiiMenu();
		//$item = new TiiMenuItem();
		foreach($this->items as $item){
			if ($item->HasSubMenu()){
				$M = $item->GetSubMenu();
				if ($M->name == $name) break;
				else $M = $M->FindMenuByName($name);
			} 
		}
		return $M;
	}
	
	/**
	 * @param object $id
	 * @return TiiMenuItem 
	 */
	public function FindMenuItemById($id){
		self::DEBUG && error_log('----- FindMenuItemById');
		foreach($this->items as $item) $ids[]=$item->id;
		self::DEBUG && error_log('finding '.$id.' in set: ['.implode(',',$ids).']');
		
		foreach($this->items as &$item){
			self::DEBUG && error_log('checking if: '.$item->id.'=='.$id);
			if (intval($item->id) == intval($id)) {
				self::DEBUG && error_log('searching.. '.$id.' found');
				self::DEBUG && error_log('RETURN');
				return $item;
			}
		}
		foreach($this->items as &$item){
			self::DEBUG && error_log('searching.. '.$id.' NOT found');
			if ($item->HasSubMenu()) {
				self::DEBUG && error_log('searching one level deep for '.$id.' in '.$item->id);
				$return = $item->GetSubMenu()->FindMenuItemById($id);
				if($return !== false) return $return;
			}
		}
		self::DEBUG && error_log('searching.. '.$id.' NOT exist');
		self::DEBUG && error_log('RETURN');
		return false;
	}
	
	public function SetCurrentTree(TiiMenuItem $item, $class_name){
		$item->IsCurrent(true);
		$item->SetOnClass($class_name);
		$parent_menu = $item->GetParentMenu();
		if ($parent_menu->HasParentItem()){
			$this->SetCurrentTree($parent_menu->GetParentItem(), $class_name);
		}
	}
	
	/**
	 * 
	 * @param object $array
	 * @return TiiMenu
	 */
	public function LoadArray($array){
		self::DEBUG && error_log('----- LoadArray');
		self::DEBUG && self::$ctr++;
		$skipped_array=array();
		/**
		 * mi: menu item
		 * pmi: parent menu
		 * psm: parent's sub menu
		 * 
		 **/
		foreach($array as $m){
			isset($m['allow_new']) || $m['allow_new'] = false;
			isset($m['allow_edit']) || $m['allow_edit'] = false;
			isset($m['allow_delete']) || $m['allow_delete'] = false;
			
			$mi = new TiiMenuItem('mn-'.$m['id']);
			$mi
				->ID($m['id'])
				->Link($m['link'])
				->Title($m['title'])
				->Text($m['text'])
				->AllowNew($m['allow_new'])
				->AllowEdit($m['allow_edit'])
				->AllowDelete($m['allow_delete'])
				->IsActive($m['is_active'])
				->IsDeleted($m['is_deleted'])
				->IsArchived($m['is_archived'])
			;
				
			$pmi = new TiiMenuItem();
			if (! $m['pid'] > 0) $pmi = false;
			else  {
				self::DEBUG && error_log('searching for '.$m['id'].'\'s parent: '.$m['pid']);
				// returns the parent menu item or false if not found 
				$pmi = $this->FindMenuItemById($m['pid']);
			}
			
			// if no parent menu item found
			if ($pmi === false){
				if ($m['pid'] > 0) {
					$skipped_array[]=$m;
					self::DEBUG && error_log($m['id'].' has parent but not found.. ***** SKIPPED..');
				}
				else {
					$this->Add($mi);
					self::DEBUG && self::DEBUG && error_log($m['id'].' does not have parent. added to root level');
				}
			}else{
				if (! $pmi->HasSubMenu()){
					$pmi->SetSubMenu(new TiiMenu('sub-mn-'.$pmi->id));
					self::DEBUG && error_log('submenu created for '.$m['pid'].'');
				}
				$pmi->GetSubMenu()->Add($mi);
				self::DEBUG && error_log($m['id'].' is added under '.$m['pid'].'');
			}
		}
		
		if (self::DEBUG && self::$ctr>5) {error_log(self::$ctr.' times executed. terminating..'); return $this;}
		if (! empty($skipped_array)) {
			self::DEBUG && error_log('============LOOP===========');
			$this->LoadArray($skipped_array);
		}
		//var_dump($skipped_array);exit;
		
		return $this;
	}
}