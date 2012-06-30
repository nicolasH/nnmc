<?php

include_once "config.php";
include_once "base.php";
include_once "blog.php";
include_once "project.php";
include_once "notes.php";

include_once CONTENT_HANDLER;

///////////////////////
///////////////////////
// $html.= HEAD
function printHead(){
  global $title;
        $html= "
        <head>
                <title> $title ~ ".SITE_NAME."</title>
                <link rel=\"stylesheet\" media=\"all\" href=\"". BASE_URI . CSS_FILE ."\" />
                <link rel=\"stylesheet\" media=\"only screen and (max-width: 800px)\" href=\"". BASE_URI . CSS_MOBILE_FILE ."\" />
                <meta name=\"viewport\" content=\"width=device-width, initial-scale=1, maximum-scale=1\">
        ";
        #<link rel=\"stylesheet\" media=\"only screen and (max-width: 800px)\" href=\"". BASE_URI . CSS_MOBILE_FILE ."\" />
        #<meta name=\"viewport\" content=\"width=device-width, initial-scale=1, maximum-scale=1\">
        #<meta name=\"viewport\" content=\"width=800, user-scalable=yes\">
        $html.= "\t</head>\n";
        return $html;
}

////////////////////////
// $html.= TOP BAR
function printTopBar($category)
{
        global $categories;
        $html= "\n\t<div id=\"navigation\">\n";
        $html.= "\t\t<span class=\"spacer\">&nbsp;</span>\n";
        $i=0;
        foreach($categories as $name => $alt){
           #$html.= "cateogry : [$name, $category] strcmp=".strcmp($name,$category);
            $html.= "\t\t".'<a class="category-link';
                if(strcmp($name ,$category)==0){
                  $html.= " selected";
                }
        $html.= '" href="'.BASE_URI.$name.'" title="'.$alt.'">'.$name.'</a>'."\n";
        $i++;
        }
        $html.= "\t\t<span class=\"spacer\">&nbsp;</span>\n";
        $html.= "\t</div>\n";
        return $html;
}

////////////////////////
// EXPAND IN LINK
function expandInLink($text){
        global $shortcuts;
        foreach($shortcuts as $short=> $long){
                $text = str_replace($short,$long,$text);
        }
        return $text;
}

function getCategoryField($request){
        global $categoriesRegexp;
        foreach($categoriesRegexp as $regexp=>$category){
                if(preg_match($regexp,$request,$match)){
                        return $category;
                }
        }
}


////////////////////////
// INTERESTING FILE
function interestingFile($file,$searchRegexp){
        if(isset($searchRegexp)){
                return preg_match($searchRegexp,$file);
        }
        if($file{0} == '.'){
                return false;
        }else{
                return true;
        }
}

///////////////////////////////
//GET FILE FROM REQUEST
function getFileFromRequest($request){
        //$html.= "getFileFromRequest<br/>\n";
        //BLOG CASE
        if(preg_match("/^blog\//",$request)){
                return BASE_PATH."blog/".getBlogFileFromRequest($request);
        }
        //projects and notes cases
        $base = "";
        if($base==""){
                $base=$request;
        }
        //if is a blogPost then see above ...
        //if is a note then ...
        //then the file is $base.text

        //if is a projectFile then ...
        if(preg_match("/^projects\//",$request)){
                return getProjectFileFromRequest($base);
        }

        if($base[strlen($base)-1]=='/'){
                $base=substr($base,0,-1);
        }

        //SPECIAL PAGES CASE
        global $specialPages;
        if($request==''){
                        return BASE_PATH.HOME_PAGE;//$specialPages['home'];
        }
        foreach($specialPages as $name => $file){
                if(preg_match("/^$name/",$request)){
                        return $file;
                }
        }
        return BASE_PATH.$base.".text";
}

//////////////////////
function printErrorText(){
        global $errorPage;
        $errorPage=true;
        return "<br/><br/><center><h3>Sorry , the page you are looking for doesn't exist :-( .<br/><br/>
        You can go to the <a href=\"".BASE_URI."\">main page</a> of the site  or try a
        <a href=\"".SEARCH_QUERY."\">web search</a> ?
        </h3></center><br/><br/>\n";
}
function printErrorMessage($error){

}

///////////////////////
// $html.= SPECIAL PAGE
function printSpecialPage($file,$uri){
                $inText = file_get_contents($file);
                $titlePage=getTitleFromContent($inText);

                $html = '<div class="post">'."\n";
                $html.= '<div class="title"><a href="'.$uri.'">'.$titlePage."</a></div>\n";
                $inText = preg_replace("/($titlePage)/","",$inText,1);
                $html.= '<div class="content">
                ';$html.= Markdown($inText);
                $html.= "</div>\n</div>\n";
                return $html;
}
////////////////////////
// PARSE REQUEST
function parseRequest($request,$category){
        global $categoriesRegexp,$dateRegexp,$specialPages,$title;
        //getFileFromRequest($request);
        $html="";
        if($request==''){
                        $title="Home";
                        $html=printSpecialPage($specialPages['home'],BASE_URI);
                        return $html;
        }
        foreach($specialPages as $page=>$file){
                if($request == $page || $request == $page."/"){ // last case is for colophon page
                        $title=ucfirst($page);
                        $html=printSpecialPage($file,$request."/");
                        return $html;
                }
        }
        if($category=="notes/"){
                $html.=printNotes($request);
                return $html;
        }
        if($category=="projects/"){
                $html.=printProjects(BASE_PATH.$request,$request,"projects/");
                return $html;
        }if($category=="blog/"){
                $html.=printBlogPosts($request);
                return $html;
        }
        //error 404
        $html.=printErrorText();
        return $html;
}

/////////////////////////
// $html.= BODY
function printBody(){
        $request = $_REQUEST['page'];
    $elements = split("/",$request);
        $html= "
        <body><div id=\"wrapper\">";
        $html.=BANNER;
        $category=getCategoryField($request);
        $top=printTopBar($category);
        $html.=$top;
        #$main= "<div class=\"main\">\n";
        $main.= parseRequest($request,$category);
        #$main.= "</div>\n";
                //breadcrumbs should be initialized now.
        $html.=$breadcrumbs.$main.$breadcrumbs;
        #$html.=$top;//printTopBar($category);
        $html.=FOOTER;
        $html.="</div>";
        global $analytics;
        $html.= "$analytics</body>";
        return $html;
}
///////////////
// MAIN
///////////////

global $errorPage;
$errorPage=false;
/*
$safari = stripos($_SERVER['HTTP_USER_AGENT'], 'safari');
$iphone = stripos($_SERVER['HTTP_USER_AGENT'], 'mobile') ;
$blazer = stripos($_SERVER['HTTP_USER_AGENT'], 'blazer');
*/
////////////////////////////////////
$title="";
$breadcrumbs="";
$body = printBody();
$html = "<html>";

$title.=getPageHeaderRoot($request);

$html.= printHead();
$html.= $body;
$html.= "\n</html>";

if($errorPage==true){
        header("HTTP/1.0 404 Not Found");
}
print $html;
?>