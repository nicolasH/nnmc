<?php

include_once "config.php";
include_once "blog.php";
include_once "project.php";
include_once "notes.php";

include_once CONTENT_HANDLER;

$analytics='
<script type="text/javascript">
	var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
	document.write(unescape("%3Cscript src=\'" + gaJsHost + "google-analytics.com/ga.js\' type=\'text/javascript\'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
	var pageTracker = _gat._getTracker("UA-3650019-1");
	pageTracker._initData();
	pageTracker._trackPageview();
</script>';
///////////////////////
// SECURITY
function checkSession(){
	
	if(REQUIRE_PASSWORD==EDIT_ONLY && $_SESSION['password'] != W2_PASSWORD )
	{
		if ( $_POST['p'] == W2_PASSWORD )
			$_SESSION['password'] = W2_PASSWORD;
		else
			return printPasswordForm();
	}
}

///////////////////////
// $html.= PASSWORD FORM
function printPasswordForm(){
	$html= "<html>";
	$html.= "<head>";
	$html.= "<title>".SITE_NAME." : Authentication</title>\n";
	$html.= "<meta name=\"viewport\" content=\"width=500, user-scalable=yes\">\n";
	$html.= "<link type=\"text/css\" rel=\"stylesheet\" href=\"" . BASE_URI . "/" . CSS_FILE ."\" />\n";
	/*if($mobile){
		$html.= "<link type=\"text/css\" rel=\"stylesheet\" href=\"" . BASE_URI . "/" . MOBILE_CSS_FILE ."\" />\n";
	}*/
	$html.= "</head>\n";
	$html.= "<body><center><br><br>please enter the password : <br><br><form method=\"post\"><input type=\"password\" name=\"p\"></input>";
	$html.= "<input id=\"pwdSubmit\" type=\"submit\" value=\"go\"/>\n</form>\n";
	
	$html.= '<br/><a href="'.BASE_URI.'"> go to main page</a><br>'."\n";
	$html.="</center></body></html>";
	print $html;
	exit;
}
//////////////////////
// DESTROY SESSION
function destroySession()
{
	if (isset($_COOKIE[session_name()]) ){	
		error_log("COOKIE WAS SET");
		setcookie(session_name(), '', time()-42000, '/');
	}
	session_destroy();
	unset($_SESSION["password"]);
	unset($_SESSION);
}


///////////////////////
///////////////////////
// $html.= HEAD
function printHead(){
global $mobile, $iphone,$title;
	//if($iphone>0){$html.= "<h1> THIS! IS! MOBILE!<br></h1>\n";}
	
    //if($mobile){$html.= "mobile !!";}
	//else{$html.= "NOT mobile !!";}
	$html= "
	<head>
		<title>".SITE_NAME." : $title</title>
		<meta name=\"viewport\" content=\"width=500, user-scalable=yes\">
		<link type=\"text/css\" rel=\"stylesheet\" href=\"" . BASE_URI . CSS_FILE ."\" />
";
	if($mobile){
		$html.= "<meta name=\"viewport\" content=\"width=500, user-scalable=yes\">";
		$html.= "<link type=\"text/css\" rel=\"stylesheet\" href=\"" . BASE_URI. MOBILE_CSS_FILE ."\" />\n";
	}
	$html.= "</head>\n";
	return $html;
}

////////////////////////
// $html.= TOP BAR
function printTopBar($category,$bottom="")
{
	global $categories;
	$html= "\n\t<div class=\"toolbar$bottom\">\n";
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
	$html.= "\t</div>\n";
	return $html; 
}

////////////////////////
//PRINT BANNER
function printBanner(){
	return "\n\t".'<div class="banner">&nbsp;<a href="'.BASE_URI.'">NICONOMICON</a> (the)</div>'."\n";	
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
	//remove all possible action
	global $actionStart,$actionEnd;
	$base = "";
	foreach($actionStart as $action){
		if(preg_match("/(.+\/)$action$/",$request,$results)){
			$base=$results[1];
		}
	}if($base ==""){
		foreach($actionEnd as $action){
			if(preg_match("/(.+\/)$action$/",$request,$results)){
			$base=$results[1];
			}
		}
	}
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
// $html.= ERROR TEXT
function printErrorText(){
	global $errorPage;
	$errorPage=true;
	return "<br/><br/><center><h3>Sorry , the page you are looking for doesn't exist :-( .<br/><br/>
	You can go to the <a href=\"".BASE_URI."\">main page</a> of the site  or try a 
	<a href=\"http://www.google.com/search?q=inurl%3Aniconomicon.net\">google search</a> ?
	</h3></center><br/><br/>\n";
}
function printErrorMessage($error){
	
}
//////////////////////
// PRINT EDIT FORM
function printEditForm($request,$action,$text,$date){

	preg_match("/(.+)\/$action$/",$request,$match);	
	$page=$match[1];
	$html= "\n edit : [$page] guessed<br>\n";	
	$formAction="";
	if($action == "edit"){
		$formAction=BASE_URI.$page."/save";
		if($text==""){
			$html.=printErrorText();
			return $html;
		}
	}
	if($action == "new"){
		$formAction=BASE_URI.$page."/create";
	}
	//$html.= "$html.= edit form :  $action, $request, $text, $date<br/>";
	$html .= "<form id=\"edit\" method=\"post\" action=\"$formAction\">\n";
	if ($action == "edit" ){
		$html .= "<input type=\"hidden\" name=\"pageName\" value=\"$page\" />\n";
	}else{
		$html .= "<p>node name: <input id=\"title\" type=\"text\" name=\"newTitle\" /></p>\n";}
	if ( $action == ACTION_NEW)
		$text = "";
		
	$html .= "<p><textarea id=\"text\" name=\"newText\" rows=\"" . EDIT_ROWS . "\" cols=\"" . EDIT_COLS . "\">$text</textarea></p>\n";
	$html .= "<p>\n";
	$html .= "<input type=\"hidden\" name=\"action\" value=\"save\" />";
	$html .= "<input id=\"save\" type=\"submit\" value=\"Save\" />\n";
	$html .= "<input id=\"cancel\" type=\"button\" onclick=\"history.go(-1);\" value=\"Cancel\" /></p>\n";
	$html .= "</form>\n";
	return $html;
}

/////////////////
//
function commitChanges($request,$action){
	//$html.= "saving file : $request<br>\n";
	$newText = trim(stripslashes($_REQUEST['newText']));
	//$html.= "newText = $newText<br>";
	$html="";
	if($action=="save"){
		$fileName = getFileFromRequest($request);
		//open the file,
		$html.="filename trying to save : $filename<br/>";
		if(file_exists($fileName)){
			//write the file
			if(isset($newText) && $newText!=""){
				file_put_contents($fileName, $newText);
			}
			$url=substr_replace($request,"",strlen($request)-strlen($action));
			$html.= " should redirect to $url<br/>";
			header("Location:".BASE_URI.$url);
		}else{
			$html.=printErrorText();
			return $html;
		}
	}
	if($action=="create"){
		//if blog then fileName=blog/$date.$title.text
		$name=$_REQUEST['newTitle'];
		/////////////////////////
		//cleanup form for fileName (== uri)
		$string= trim($name);
		//iso-8859-1
		//$string = iconv('UTF-8', 'ASCII//TRANSLIT',$string);
		$string = iconv('iso-8859-1', 'ASCII//TRANSLIT',$string);
		global $unwanted,$replaced;
		$unwanted=array("^","'","`","~","\"","�","*","*","%");
		//$html.= "1 " . $string."<br/>\n";
		$string = str_replace($unwanted,"",$string);
		//$html.= "2 " . $string."<br/>\n";
		$string = str_replace($replaced,".",$string);
		//$html.= "3 " . $string."<br/>\n";
		$string = strtolower($string);

		$text= $_REQUEST['newText'];

		/////////////////////////
		//get dir from request
		$category=getCategoryField($request);
		//get title from form
		//if blog then fileName=blog/$date.$title.text
		if($category==BLOG_DIR){
			$filename = date("Ymd.Hi").".".$string;
			$filename=BASE_PATH.BLOG_DIR.$filename.TEXTENSION;
		}
		if($category==NOTES_DIR){
			$filename=BASE_PATH.NOTES_DIR.$string.TEXTENSION;
		}
		$html.= "Filename will look like that : [$filename]<br/>\n";
		//open the file,
		if(file_exists($filename)){
			$html.=printErrorText();
		}else{
			//write the file
			file_put_contents($filename, $text);
			return $html;
		}
	}
	if($action=="delete"){
		$fileName = getFileFromRequest($request);
		if(file_exists($fileName)){
			#fclose();
			unlink($fileName);
		}else{
			$html.=printErrorText();
		}
	}
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
	global $actionStart,$actionEnd;
	///////////////////
	// new, edit , import
	foreach($actionStart as $action){ 
		if(preg_match("/$action$/",$request)){
			$file = getFileFromRequest($request);
			$html.="$request does match '/$action$/' : trying to do something to this file : $file<br/>";
			$text = file_get_contents($file);
			$date = date("Y/m/d H:i");
			$html.= printEditForm($request,$action,$text,$date,"");
			return $html;
		}
	}
	////////////////////
	// delete, save, create
	foreach($actionEnd as $action){ 
		if(preg_match("/$action$/",$request)){
			#$file = getFileFromRequest($request);$html.="$request does match '/$action$/' : trying to do something to this file : $file<br/>";$text = file_get_contents($file);
			commitChanges($request,$action);
			return;
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
	<body>";
	$html.=printBanner();
	$category=getCategoryField($request);
	$top=printTopBar($category);
	$html.=$top;
	$main= "<div class=\"main\">\n";
	$main.= parseRequest($request,$category);
	$main.= "</div>\n";
		//breadcrumbs should be initialized now.
	$html.=$breadcrumbs.$main.$breadcrumbs;
	$html.=$top;//printTopBar($category);
	global $analytics;
	$html.= "$analytics</body>";
	return $html;
}
///////////////////
// finds the known action from the URL
// GET ACTION
function getAction($request){
	global $actionStart,$actionEnd;
	///////////////////
	// new, edit , import
	foreach($actionStart as $action){ 
		if(preg_match("/$action$/",$request)){
			return $action;
		}
	}
	////////////////////
	// delete, save, create
	foreach($actionEnd as $action){ 
		if(preg_match("/$action$/",$request)){
			return $action;
		}
	}
}
///////////////
// MAIN
///////////////
////////////////////////////////////
// if the request is an action (edit/new/save etc..), then check the session
// else, go on according to the usual
/////////////////////
session_name("W2");
session_start();

$action = getAction($_REQUEST['page']);
if(isset($action)){
	if ( $action == "logout" ){
		error_log("DO LOGOUT");
		destroySession();
		header("Location: " . BASE_URI . "/");
		exit;
	}
	//Other actions : edit etc ...
	checkSession();
}
/////////////////////
/////////////////////
global $errorPage;
$errorPage=false;
$safari = stripos($_SERVER['HTTP_USER_AGENT'], 'safari');
$iphone = stripos($_SERVER['HTTP_USER_AGENT'], 'mobile') ;
$blazer = stripos($_SERVER['HTTP_USER_AGENT'], 'blazer');
$mobile=false;
if($iphone > 0 && $safari > 0){
	$mobile=true;
}
if($blazer>0){
	$mobile=true;
}
//$mobile = $iphone + $blazer;
//$elements= split("/",$_REQUEST['page']);
//$html.=_r($elements);
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