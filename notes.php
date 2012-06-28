<?php
$lastNote="";
////////////////////////
// PRINT NOTES
function printNotes($request){
        $dir=BASE_PATH.$request;
        $uri=BASE_URI.$request;
        $html = '<div id="notes">';
        if(is_dir($dir)){
                $html.= printDir($request);
                $html.="</div>";
                return $html;
        }
        else{
                if(substr($request ,-1)== '/'){
                        $request=substr($request, 0,strlen($request) -1);
                }
                if(file_exists(BASE_PATH.$request.TEXTENSION)){
                        $file = substr($request, strrpos($request,'/')+1,strlen($request));
                        $dir = substr($request,0,strrpos($request,'/')+1);
                        $html.= printPost(BASE_PATH.$dir,$file.TEXTENSION,$uri);
                        $html.="</div>";
                        return $html;
                }
        }
        return printErrorText();
}

//////////////////////////
// PRINT DIR
function printDir($request){
        $dir=BASE_PATH.$request;
        $uri=BASE_URI.$request;
        $html= "";
        if ($dh = opendir($dir)){
                while (($file = readdir($dh)) !== false){
                        if(interestingFile($file,$bla)){
                                if(is_dir($dir.'/'.$file)){
                                        $html.= "<div>\n".'<a class="title" href="'.$uri."/".$file.'/">'.$file."</a>\n";
                                        $html.= listContent($dir.'/'.$file,$uri.$file."/");//printEntryLink($file);
                                        $html.= "</div>\n";
                                }else{//file : print it
                                        $html.= printPost($dir,$file,$uri.substr($file,0,-5));
                                }
                        }
                }
        }
        return $html;
        closedir($dh);
}

//////////////////////////
// GUESS LINK
function guessLink($file,$uri){
        $inText = file_get_contents($file);
        $title= strtok($inText,"\n");
        if($uri{0}=="/"){
                $uri=substr($uri,0,-5);
        }
        return "<a href=\"".$uri."\">$title</a>";
}

//////////////////////////
// LIST CONTENTS
function listContent($dir,$uri){
        $html = "<ul>\n";
        if ($dh = opendir($dir)){
                while (($file = readdir($dh)) !== false){
                        if(interestingFile($file,$bla)){
                                //link content
                                if(is_dir($dir.'/'.$file)){
                                        $html.= '<a class="dir" href="'.$uri.$file.'/">'.$file."</a>\n";
                                }else{
                                        $html.= "<li>".guessLink($dir.'/'.$file,$uri.$file)."</li>\n";
                                }
                        }
                }
        }
        $html.= "</ul>\n";
        return $html;
}
?>
