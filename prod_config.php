<?php

#define('BASE_PATH', '~niko/tests/w2n-remote/');					// leave any trailing slash
#define('BASE_URI', '~niko/tests/w2n-remote');	// Omit any trailing slash
define('BASE_PATH', '/home/public/content_nnmc/');	// leave any trailing slash
define('BASE_URI', '/');	// Omit any trailing slash
define('DEFAULT_PAGE', 'notes/Home.txt');
define('DISABLE_UPLOADS', true);
define('CSS_FILE', 'index.css');
define('MOBILE_CSS_FILE', 'index.mobile.css');

define('SITE_NAME',"NICONOMICON");
define('BLOG_POST_NUMBER',5);

define('BLOG_DIR','blog/');
define('NOTES_DIR','notes/');
define('PROJECTS_DIR','projects/');

define('TEXTENSION','.text');
define('BLURB','.blurb');

$analytics = '<script type="text/javascript">
	var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
	document.write(unescape("%3Cscript src=\'" + gaJsHost + "google-analytics.com/ga.js\' type=\'text/javascript\'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
	var pageTracker = _gat._getTracker("UA-3650019-1");
	pageTracker._initData();
	pageTracker._trackPageview();
</script>';

$specialPages = array(
	'colophon'=> BASE_PATH.'pages/colophon.text',
	'banner'=> BASE_PATH.'pages/banner.text',
	'home'=> BASE_PATH.'pages/home.text'
	//'' =>'pages/home.text'
	);

define('HOME_PAGE','pages/home.text');
$actionStart = array("new","edit","import","login");
$actionEnd= array("delete","save","create","logout");
	
$categories = array(
	"blog/"=>"well, my blog. Duh.",
	"notes/"=>"just not as pretentious as 'article'",
	"projects/"=>"Some of the projects I have been working on",
	"colophon"=>"about this website");

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

$unwanted=array("^","'","`","~","\"","�","*","*","%");
$replaced=array("\t"," ","?","&","!","-","_","/","\\");

//define('BASE_PATH', getcwd());					// Omit any trailing slash
//define('BASE_URI', 'http://stevenf.com/pw2');	// Omit any trailing slash
//define('DEFAULT_PAGE', 'Home');
//define('DISABLE_UPLOADS', false);
define('VALID_UPLOAD_TYPES', 'image/jpeg,image/pjpeg,image/png,image/gif,application/pdf,application/zip,application/x-diskcopy');
define('VALID_UPLOAD_EXTS', 'jpg,jpeg,png,gif,pdf,zip,dmg');
define('TITLE_DATE', 'r');

define('EDIT_ROWS', 30);
define('EDIT_COLS', 80);

#define('CONTENT_HANDLER','markdown.php');
define('CONTENT_HANDLER','markdown_extra.php');


//define('REQUIRE_PASSWORD', true);
define('EDIT_ONLY',1);
define('ACCESS',2);
define('NEVER',0);

define('REQUIRE_PASSWORD',1 );//
define('W2_PASSWORD', 'notSecret');

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
	return implode(" : ",$pieces);
}

?>
