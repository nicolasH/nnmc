<?php
////////////////////////
// PROJECTS HANDLING
////////////////////////
$lastProject="";


function getProjectFileFromRequest($uri){
        ////////////
        //if(string ends with edit etc.... remove that
        $html = "Get project file : $uri<br/>";
        $array = explode("/",$uri);
        $length = sizeof($array);

        $html.="array length : $length<br/>";
        $i=$length-1;
        if($array[$i]==""){
                unset($array[$i]);
        }
        $i=0;
        $html.="cleaning up : <br/>";
        foreach($array as $field){
                $html.="array field $i=[$field]<br/>";
                $i++;
        }
        $i--;
        $blurb=FALSE;
        $normal=FALSE;
        $draft=FALSE;
        $return="";
        if($array[$i]=="blurb"){
                $f=$array[$i-1];
                unset($array[$i]);
                $blurb=TRUE;
                $return=BASE_PATH.implode("/",$array)."/".$f.".blurb";
                $html.="is a blurb.:$f(.blurb)<br/>";
        }else{
                if($array[1] =="drafts"){
                        $f=$array[$i];
                        $draft=TRUE;
                        $return=BASE_PATH.implode("/",$array).TEXTENSION;
                        $html.="is not a blurb, is a draft : $f(.text)<br/>";
                }else{//normal project
                        $f=$array[$i];
                        $normal=TRUE;
                        $return=BASE_PATH.implode("/",$array)."/".$f.TEXTENSION;
                        $html.="is not a blurb : $f(.text)<br/>";
                }
        }
        $html.= "\$f = $f , \$file=$file, \$noSlash=$noSlash ===> $return<br>";
        return $return;
}

////////////////////////
// PRINT PROJECT DIRS
function printProjects($dir,$uri){
        if($uri==PROJECTS_DIR){
                return listProjects(BASE_PATH.$uri,"", BASE_URI.PROJECTS_DIR);
        }
        $file=getFileFromRequest($uri);
        $f="";
        ////////////
        $html="";
        if($dir[strlen($dir)-1]=='/'){
                $noSlash=substr($dir,0,-1);
                //$html.= "base : $base last position of / =". strripos($noSlash,'/')."<br>\n";
                $f=substr($noSlash,strripos($noSlash,'/')+1);
                //$html.= "base : $f<br>";
                if(strlen($f)>0){
                        $file=$f.TEXTENSION;
                }
        }
        ////////////
        if(strlen($file)> 0 && file_exists($noSlash.TEXTENSION)){
        //is a probably a draft
                $dirF=substr($noSlash,0,-strlen($f));
                $html.=printPost($dirF,$file,BASE_URI.$uri);
        }
        if(is_dir($dir)){
                //$html.= "is a directory $dir !!!!!!<br/>";
                if(preg_match("/projects\/drafts\//",$uri)){
                        $html.= "<div class=\"draft title\"><h1>Drafts</h1></div><div><i><b>This posts are draft for non currently developed projects : either old or futur projects</b></i></div>\n";
                        if ($dh = opendir($dir)){
                                while (($file = readdir($dh)) !== false ){
                                        if(interestingFile($file,$searchRegexp)){
                                                $uriPost=$uri.substr($file,0,-5)."/";
                                                $html.=printPost($dir,$file,BASE_URI.$uriPost);
                                        }
                                }
                        }
                        return $html;
                }else{
                        //is it an actual project ????
                        if(file_exists($dir.$file) ){
                                $close = printHierarchie($dir,$file,$uri);
                                $html.= $close[0];
                                $html.= printPost($dir,$file,BASE_URI.$uri);
                                $html.= $close[1];
                        }else{
                                //list projects
                                $html.= '<div class="dir"><a href="'.BASE_URI.$uri.'" class="title">'.$f.'</a>'."\n";
                                $html.= listProjects(BASE_PATH.$uri,"", BASE_URI.PROJECTS_DIR.$f."/");
                                $html.= "</div>\n";
                        }
                }
                //remove any edit suffix.
                //check if the
                //isProjectDirectory ? :
                //$parts=strtok($dir,'/');
                //if($lastPart="drafts"
                //if is proejct dir proint project
                //if drafts dir $html.= drafts
                //if language $html.= projects
        }else{
                //$html.= "===== $dir is not a directory<br/>";
        }
        return $html;
}
////////////////////////////
//
function printHierarchie($path,$file,$uri){
        $array=explode("/",$uri);
        $html=array();
        //print_r($array);
        $url=BASE_URI."projects/";
        for($i=1; $i<count($array)-2 ;$i++){
                $url.=$array[$i]."/";
                $html[0].= '<div><a href="'.$url.'" >'.$array[$i]."</a>\n";
                $html[1].= "</div>";
        }
        return $html;
}

////////////////////////////
// $html.= BLURB
function printBlurb($path,$projectName,$uri){
        //$html.= "blurb : $path, $projectName, $uri <br/>\n";
        $inText = file_get_contents($path);
        return "<div><p><a href=\"$uri\">$projectName</a> &mdash; ".$inText.'</p>'."</div>\n";

}
////////////////////////////
// LIST PROJECTS
function listProjects($path,$dir,$uri){
        #$html.= "LIST PROJECTS : $path,  $dir, $uri <br/>\n ";
        if (isset($dir) && $dir == ""){
          $html= '<div id="projects">';
        }else{
          $html= "<div>";
        }
        if(isset($dir) && $dir != ""){
                if($dir=="drafts/"){
                        $html.= '<a href="'.$uri.'" class="title">'.$dir."</a><ul>\n";
                }else{
                        $html.= '<a href="'.$uri.'" class="title">'.$dir.'</a>'."\n";
                }
        }
        #print "should be trying to open : ".$path.$dir."<br/>";
        if ($dh = opendir($path.$dir)){
                //$html.= "opened directory : ".$path.$dir." <br/>";
                while (($file = readdir($dh)) !== false){
                        if(interestingFile($file,$searchRegexp)) {
                                if(is_dir($path.$dir.$file)){
                                        if(file_exists($path.$dir.$file."/".$file.BLURB)){
                                                $html.= printBlurb($path.$dir.$file."/".$file.BLURB,$file,$uri.$file.'/');
                                        }else{
                                                $html.= listProjects($path.$dir,$file.'/',$uri.$file.'/');
                                        }
                                }else{
                                        if($dir == "drafts/"){//Surely it is draft ???
                                                $html.= "<li>".guessDraftLinkList($path.$dir,$file,$uri)."</li>\n";
                                        }
                                }
                        }
                }
                closedir($dh);
        }else{
                $html.= "could not open this file : [".$path.$dir."]<br/>";
        }
        if($dir=="drafts/"){
                $html.= "</ul>\n";
        }
        $html.= "</div>\n";
        return $html;
}
////////////////////////////
// GUESS LINK LIST
function guessDraftLinkList($dir,$file,$uri){
        $inText = file_get_contents($dir.$file);
        $f=str_replace(TEXTENSION,"",$file);
        $title= strtok($inText,"\n");
        return "<a href=\"".$uri.$f."/\">$title</a>";
}
?>