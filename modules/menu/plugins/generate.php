<?php

function MenuPlugin_Generate($params=null){
    $params = json_decode($params);
    
    if(isset($params->parser)) 
    Tii::App()->Template()->Import($params->parser);
    
    if(isset($params->load))
    Tii::App()->Template()->AddScriptCode('('.$params->load.')();');    
    
    Tii::Import('base/menu.php');
    $M = new TiiMenu();
    foreach($params->structure as $MI){
        $M->Add( new TiiMenuItem($MI->title,$MI->title,$MI->link) );
    }
    
    return '<div id="'.$params->id.'">'.$M->Build(0, $params->class)->GetHTML().'</div>';
}