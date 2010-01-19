<?php
class TApplication extends TCore{
	
	public function Name($val=null){
		return $this->GetOrSet(__FUNCTION__,$val);
	}
}
