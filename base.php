<?php

define('TITLE_DATE', 'r');
define('CONTENT_HANDLER','markdown_extra.php');


define('DEFAULT_PAGE', 'notes/Home.txt');
define('CSS_FILE', 'index.css');

define('SEARCH_QUERY','http://www.google.com/search?q=inurl%3Aniconomicon.net');

define('SITE_NAME',"NICONOMICON");
define('BLOG_POST_NUMBER',5);

define('BANNER',"\n\t".'<div class="banner">&nbsp;<a href="'.BASE_URI.'">'.SITE_NAME.'</a> (the)</div>'."\n");
define('FOOTER',"\n\t".'<div class="footer"><a href="http://creativecommons.org/licenses/by-nc-sa/3.0/">Copyright Nicolas Hoibian</a> since, like 2006 or something.</div>'."\n");

define('BLOG_DIR','blog/');
define('NOTES_DIR','notes/');
define('PROJECTS_DIR','projects/');

define('TEXTENSION','.text');
define('BLURB','.blurb');

define('HOME_PAGE','pages/home.text');

$specialPages = array(
	'colophon'=> BASE_PATH.'pages/colophon.text',
	'home'=> BASE_PATH.'pages/home.text'
	);

	
$categories = array(
	"blog/"=>"Well, a set of posts loosely related to me and each other organized in a reverse chronological order.",
	"notes/"=>"Just not as pretentious as 'article'",
	"projects/"=>"Some of the projects I have been (and may still be) working on.",
	"colophon"=>"More informations about this website and your host.");

$shortcuts=array(
	'(n/'=>'('.BASE_URI.'notes/',
	'(p/'=>'('.BASE_URI.'projects/');

$categoriesRegexp=array(
	"/blog\/?.*/" => "blog/",
	"/notes\/?.*/" => "notes/",
	"/projects\/?.*/" => "projects/",
	"/colophon?.*/" => "colophon"
	);
	
$dateRegexp = array(
	"/^\w+\/(\d{4})\/?.*/"=>"year",//year
	"/^\w+\/\d{4}\/(\d{2})\/?.*/"=>"month",//month
	"/^\w+\/\d{4}\/(\w\d+)\/?.*/"=>"period",//quarter 1 as q1 week 1 as w1
	"/^\w+\/\d{4}\/\d{2}\/(\d{2})\/?.*/"=>"day",//day
	"/^\w+\/\d{4}\/\d{2}\/\d{2}\/(.*)/"=>"rest"//rest
	);

$unwanted=array("^","'","`","~","\"","Â","*","*","%");
$replaced=array("\t"," ","?","&","!","-","_","/","\\");

////////////////////////////
// PRINT POST 
function printPost($path,$file,$uri,$titleLinkPrefix=""){
	$inText = file_get_contents($path.$file);
	$title=getTitleFromContent($inText);
	$html.= '<div class="post">'."\n";
	$html.= '<div class="title">';
	$html.= $titleLinkPrefix;
	$html.= '<a class="title" href="'.$uri.'">'.$title."</a>\n";
	$html.= "</div>\n";
	$inText = preg_replace("/($title\s+)/","",$inText,1);
	$inText = expandInLink($inText);
	$html.= '<div class="content">
	';$html.= Markdown($inText);
	$html.= "</div>\n</div>\n";
	return $html;
}

$titleCount=0;
$lastTitle="";
///////////////////////////
// returns the first line of a file.
function getTitleFromContent($text){
	global $titleCount,$lastTitle;
	preg_match("/([^\\n]+)\n.*/",$text,$res);
	$titleCount++;	
	
	if(isset($res[1])){
		$lastTitle=rtrim($res[1]);
		return $lastTitle;}
	else 
		return "";
}
function getPageHeaderRoot(){
	$pieces=explode("/",$_REQUEST['page']);
	//(if is a post : no slash, so we can remove the file name)
	//if is not a post, there will be a slash, so we can remove the last element, as it will be empty.
	//except of course for the sinlge pages : "colophon" and the "home".
	global $titleCount,$lastTitle;
	if($pieces[sizeof($pieces)-1]==""){
		array_pop($pieces);
	}
	if($titleCount==1){
		array_pop($pieces);
		array_push($pieces," [$lastTitle]");
		
	}
	for($i = 0; $i < sizeof($pieces); $i++)	{
		$pieces[$i]=ucwords($pieces[$i]);
	}
	return implode(" : ",array_reverse($pieces));
}

?>
