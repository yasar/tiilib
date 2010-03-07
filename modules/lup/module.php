<?php
class TiiModule_LUP extends TiiModule{
    public static $table = 'lup';
    public static $listing_table = 'vw_lup'; //could be vw_lup
    public static $primary_field = 'lup_id';
    
    public function GotoListing(){
        header('location: '.FUNC::RUP(array('a'=>'list', self::$primary_field=>'')));
        exit;
    }

    public function GotoEdit($rec_id)
    {
        header('location: '.FUNC::RUP(array('a'=>'edit', self::$primary_field=>$rec_id)));
        exit;
    }
    
    public function Add()
    {
        if(Config_Lup_Module::$field_value_gui == 'image'){
            
            include DIR.'/tiilib/classes/uploadimage-0.0.2.php';
            
            $Uploader = new UploadImage();
            $Uploader->SetPostField('value');
            $Uploader->AddSize(Config_Lup_Module::$field_value_image_size);
            $Uploader->AddSize(Config_Lup_Module::$field_value_thumb_size);
            $Uploader->SetTargetFolder(DIR.Config_Lup_Module::$field_value_save_path);
            $filename = $Uploader->Save();
            
            $_POST['value'] = $filename;
        }
        
        
        list($fields, $values)=Tii::$DB->BuildFieldsValuesFromPost(self::$table);
        $sql=Tii::$DB->BuildInsertQuery(self::$table, $fields, $values);
        //echo $sql;exit;
        Tii::$DB->Execute($sql);

        if (Tii::$DB->HasError()){
            Page::$error = Tii::$DB->GetError();
            return false;
        }

        return Tii::$DB->GetLastInsertId();
    }
    
    public function Update($rec_id)
    {
        if(Config_Lup_Module::$field_value_gui == 'image'){
            
            include DIR.'/tiilib/classes/uploadimage-0.0.2.php';
            
            $Uploader = new UploadImage();
            $Uploader->SetPostField('value');
            $Uploader->AddSize(Config_Lup_Module::$field_value_image_size);
            $Uploader->AddSize(Config_Lup_Module::$field_value_thumb_size);
            $Uploader->SetTargetFolder(DIR.Config_Lup_Module::$field_value_save_path);
            $filename = $Uploader->Save();
            if($filename) $_POST['value'] = $filename;
        }
        
        
        list($fields, $values)=Tii::$DB->BuildFieldsValuesFromPost(self::$table);
        $sql=Tii::$DB->BuildUpdateQuery(self::$table, $fields, $values, self::$primary_field.'='.$rec_id);
        Tii::$DB->Execute($sql);

        if (Tii::$DB->HasError()){
            Page::$error = Tii::$DB->GetError();
            return false;
        }

        return true;
    }
    
    public function Delete($rec_id){
        Tii::$DB->Execute('delete from '.self::$table.' where allow_delete=1 && '.self::$primary_field.'='.$rec_id);
        return;
    }

    public function GetForm($row=Array()){
        ob_start();
        include_once DIR.'/tiilib/3rd-party/simple_html_dom.php';
        
        Page::AddModule('FixButtons');

        if (!isset($row)) $row = array(); // initialize this to an empty array.
        $stack=file_get_contents(dirname(__FILE__).'/shared/templates/'.Config_Lup_Module::$form_template);
        
        Form::$form_handler='';
        Form::$buttons = array();
        if (empty($row))
        {
            Form::$buttons[]=array('type'=>'submit', 'name'=>'action', 
                'class'=>Form::ACTION_ADD, 'value'=>Form::ACTION_ADD, 
                'label'=>'<img src="'.Tii::$rdir.'/tiilib/images/ic/database_add.png" /> Add');
        }
        else
        {
            Form::$buttons[]=array('type'=>'submit', 'name'=>'action', 
                'class'=>Form::ACTION_UPDATE, 'value'=>Form::ACTION_UPDATE, 
                'label'=>'<img src="'.Tii::$rdir.'/tiilib/images/ic/database_add.png" /> Update');
        }
        Form::$buttons[]=array('type'=>'submit', 'name'=>'action', 
            'class'=>Form::ACTION_CANCEL, 'value'=>Form::ACTION_CANCEL, 
            'label'=>'<img src="'.Tii::$rdir.'/tiilib/images/ic/cancel.png" /> Cancel');
        
        $html = new simple_html_dom();
        $html->load($stack);
        Form::Render($html);
        
        $stack = $html->save();
        
        eval('?>'.$stack);
        return ob_get_clean();
    }
    
    public function GetListing(){
        ob_start();
        Tii::$CurrentSubMenu = FSO::GetFilename(__FILE__);
        
        include_once DIR.'/tiilib/classes/ui.php';
        include_once DIR.'/tiilib/classes/listview.php';
        include_once DIR.'/tiilib/classes/htmlhelper.php';
        
        Page::AddStyle(Tii::$rdir.'/tiilib/styles/listing.css');
        
        Page::$show_link_menu = true;
        if(Config_Lup_Module::$listing_enable_add_new) Page::$add_record_link = FUNC::RUP(array('a'=>'new'));

        $db = Tii::$DB;
        $OListView=new ListView();

        #======================================================================
        # Customizable
        #``````````````````````````````````````````````````````````````````````
        $OListView->records_per_page=100;
        #----------------------------------------------------------------------
        # Has to match with database
        #``````````````````````````````````````````````````````````````````````
        $OListView->table_name=self::$listing_table;
        $OListView->primary_id=self::$primary_field;
        $OListView->enable_filter=empty(Config_Lup_Module::$listing_filterables)?false:true;
        $OListView->show_searchable_fields=true;
        $OListView->enable_row_sorting=false;
        $OListView->use_clue_tip = false;
        $OListView->enable_toggle_advanced_search = Config_Lup_Module::$listing_toggle_advanced_search;

        if(! empty(Config_Lup_Module::$listing_filterables)){
            $OListView->AddFilterables(Config_Lup_Module::$listing_filterables);
        }

        //if(Config_Lup_Module::$enable_domain_id && Config_Lup_Module::$field_domain_id_listable){
        if(in_array('domain_id',Config_Lup_Module::$listing_fields)){
            $OListView
                ->AddField('domain_name')
                ->AddLabel('Domain')
                ->AddDisplayFormat('{}')
                ->AddFieldsMore(array('domain_id'))
            ;
        }

        //if(Config_Lup_Module::$enable_name && Config_Lup_Module::$field_name_listable){
        if (in_array('name',Config_Lup_Module::$listing_fields)) {
            $OListView
                ->AddField('name')
                ->AddLabel('Name')
                ->AddDisplayFormat('{}')
            ;
        }

        //if(Config_Lup_Module::$enable_description && Config_Lup_Module::$field_description_listable){
        if (in_array('description',Config_Lup_Module::$listing_fields)) {
            $OListView
                ->AddField('description')
                ->AddLabel('Description')
                ->AddDisplayFormat('{}')
            ;
        }

        if (in_array('value',Config_Lup_Module::$listing_fields)) {
            $OListView->AddField('value')->AddLabel('Value');
            if (! is_null(Config_Lup_Module::$field_value_datasource)) {
                $OListView->AddDisplayFormat('<span class="datasource" datasource="'.htmlentities(json_encode(Config_Lup_Module::$field_value_datasource)).'">{db:[value]}</span>');
            }
            else if(Config_Lup_Module::$field_value_gui == 'image'){
                $OListView->AddDisplayFormat('<span><img src="'.Config_Lup_Module::$field_value_save_path.'/'.Config_Lup_Module::$field_value_thumb_size->prefix.'{db:[value]}" /></span>');
            }
            else {
                $OListView->AddDisplayFormat('{}');
            }
        }
        
        $OListView
            ->AddSearchables(array('name','description','value'))
            ->AddSortables(array('name|Name','description|Description','value|Value'))
            ->AddFieldsMore(array('lup_id','allow_delete'))
            //->SetStaticFilter('user_type <> "admin" && is_active=1')
            //->SetStaticFilter('`status`="published" or (`status`="draft" and public_id not in (select public_id from fanzone where `status`="published") and fanzone_id in (select fanzone_id from (select fanzone_id,public_id from fanzone where `status`="draft" order by timestamp_update desc) as t1 group by public_id))')
            //->SetStaticSortBy('timestamp_post')
        ;
        
        if(Config_Lup_Module::$filter_by_group){
            $OListView->SetStaticFilter('`group`="'.Config_Lup_Module::$default_group.'"', true);
        }
        
        if(Config_Lup_Module::$filter_by_section){
            $OListView->SetStaticFilter('`section`="'.Config_Lup_Module::$default_section.'"', true);
        }
        
        if(Config_Lup_Module::$filter_by_domain_id){
            $OListView->SetStaticFilter('`domain_id`="'.Config_Lup_Module::$default_domain_id.'"', true);
        }
        
        if(Config_Lup_Module::$filter_by_name){
            $OListView->SetStaticFilter('`name`="'.Config_Lup_Module::$default_name.'"', true);
        }
        

        $OListView->page_number=FUNC::IfGet('pnum', 1);
        $OListView->complete_list_with_empty_rows = false;

        $OListView->sort_by=FUNC::ifget('sort_by','');
        $OListView->sort_order=FUNC::ifget('sort_order','');
        $OListView->search_for=FUNC::ifget('search_for','');
        $OListView->ShowHeader(true);

        #******************************************
        $action=new AnAction();
        $action->action='javascript:$.tii.RUP([{p:\'a\', v:\'edit\'},{p:\''.self::$primary_field.'\', v:{db:['.self::$primary_field.']}}])';
        $action->label='<img src="'.Tii::$rdir.'/tiilib/images/ic/database_edit.png" /> Edit';
        $action->ui='link';
        $OListView->AddAction($action);
        #******************************************
        $action=new AnAction();
        $action->action='javascript:$.tii.RUP([{p:\'a\', v:\'delete\'},{p:\''.self::$primary_field.'\', v:{db:['.self::$primary_field.']}}])';
        $action->label='<img src="'.Tii::$rdir.'/tiilib/images/ic/delete.png" /> Delete';
        $action->ui='link';
        $action->request_confirmation=true;
        $action->display_condition = 'true && \'{db:[allow_delete]}\' == \'1\'';
        $OListView->AddAction($action);
        #******************************************
        unset($action);
        
        //var_dump($OListView); exit;
        
        try{
            $OHTMLHelper=new HTMLHelper();
            $sql = $db->PrepareListQuery($OListView);
            //echo $sql;exit;
            $OHTMLHelper->data_source = $db->Query($sql);
            //var_dump($OHTMLHelper->data_source);exit;
            $_list_view=$OHTMLHelper->UI_DBRecordListing($OListView);
        }
        catch(Exception $e){
            print($e->getMessage()); 
            print($e->getTrace()); 
            exit;
        }
        return $_list_view;
    }
    
    public static function GetRecord($rec_id){
        return Tii::$DB->Query('select * from '.self::$table.' where '.self::$primary_field.'='.$rec_id,true);
    }
    
    public static function GetView($rec_id){
        $row = self::GetRecord($rec_id);
        ob_start();
        include Config_Lup_Module::$view_template;
        return ob_get_clean();
    }
}
