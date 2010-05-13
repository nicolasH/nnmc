<?php
////////////////////////
// GUESS URI
function guessBlogUri($uri,$file){
	$path = substr($uri,strlen(BASE_URI));
	$f = strrchr($file,'/');
	if($f === FALSE){
		$f=$file;
	}else{
		$f=substr($f,1);
	}
	#print "\nGUESS URI : path : ".$path . "BASE_URI : ".BASE_URI." uri : $uri file: $file f:$f <br/>\n";
	//Works for blogs
	$date = getDateFromName($f);
	$uri_f = substr($f,9);//to remove the date (20081222.)
	$uri_f = substr($uri_f,0,-5);//to remove the extension
	$path = BASE_URI.BLOG_DIR.$date["year"]."/".$date["month"]."/".$date["day"]."/".$uri_f;
	return $path;
}

////////////////////////
// GET DATE FROM NAME (will not work after year 9999)
function getDateFromName($adress){
	$name = strrchr($adress ,'/');
	if($name===FALSE){
		$name=$adress;
	}
	if(preg_match("/(\d\d\d\d)(\d\d)(\d\d)\.(\d\d)(\d\d).*$/",$name,$res))	{	
		return array(
		"year"=>$res[1],
		"month"=>$res[2],
		"day"=>$res[3],
		"hour"=>$res[4],
		"minute"=>$res[5]);
	}
}

////////////////////////
// GET DATE FIELDS
function getDateFields($request){
	global $dateRegexp;
	$results=array();
	foreach($dateRegexp as $regexp => $field ){
		if(preg_match($regexp,$request,$match)){
			$results[$field] = $match[1];
		}
	}
	return $results;
}

////////////////////////
// GET DATE FILTER REGEXP
function getDateFilterRegexp($dateArray){
	$regexp;
	//print_r($dateArray);
	if(isset($dateArray['year'])){
		$regexp="/^".$dateArray['year'];
		if(isset($dateArray['month'])){
			$regexp.=$dateArray['month'];
			if(isset($dateArray['day'])){
				$regexp.=$dateArray['day'];
			}
			if(isset($dateArray['rest'])){
				//print "i don't know what to do with the rest : ";
				//print $dateArray['rest']."<br/>\n";
			}
		}
		if(isset($dateArray['period'])){
			//print "should definitely handle period<br/>\n";
		}
		$regexp.="/";
	}
	return $regexp;
}

////////////////////////
// PRINT TITLE
function printTitle($title,$file,$uri){
	$date=getDateFromName($file);
	$html="";
	$base = BASE_URI . "blog/";
	if(isset($date)){
	   $html.= '<a class="date" href="'.$base.$date['year'].'/">'. $date['year'].'</a> / ';
	   $html.= '<a class="date" href="'.$base.$date['year'].'/'.$date['month'].'/">'.$date['month'] .'</a> / ';
	   $html.= '<a class="date" href="'.$base.$date['year'].'/'.$date['month'].'/'.$date['day'].'/">'.$date['day'].'</a> : ' ;
	}
	if(isset($title)){
		$html = '<div class="title">'.$html;
		$html.= '<a href="'.$uri.'" >'.$title.'</a>';
		$html.= "</div>\n";
	}
	return $html;
}
////////////////////////
// PRINT FILE
function printFile($dir,$file,$uri){	
		$fileUri= guessBlogUri($uri,$dir.$file);
		$titlePrefix=printTitle($notSet,$file,$uri);
		$html = printPost($dir,$file,$fileUri,$titlePrefix);
		return $html;
}

////////////////////////
// PRINT BLOG POSTS
function printBlogPosts($request){
	$dir=BASE_PATH.BLOG_DIR;
	$uri=BASE_URI.BLOG_DIR;
	//"blog/2008/"
	//blog/2008/01/"
	//blog/2008/01/14/"
	//blog/2008/q1/""
	//blog/2008/01/14/01"
	if(strpos($request,"/list")>0){
		return printBlogPostsList($request);
		//return;
	}
	$date = getDateFields($request);
	$filter = getDateFilterRegexp($date);
	$file = getBlogFileFromRequest($request);
	
	$html="";
	if(file_exists($dir.$file)){
	   	//print "<hr>file exist somehow !!!!<br/>\n";
        $dirFiles=getFileList($dir,$empty);
        rsort($dirFiles);
        //previous and next ?
        $preNextFile=getPreNext($dirFiles,$file);
        //$html.=printPostNav($uri,$dir,$preNextFile["pre"],$preNextFile["nxt"],"left");
		$html.=printFile($dir,$file,$uri);
		$html.=printPostNav($uri,$dir,$preNextFile["pre"],$preNextFile["nxt"],"right");
    }else{
		$html.=listPosts($request,$filter);//$dirFiles,$dir,$uri);
	}
	return $html;
}
////////////////////////
// PRINT BLOG POSTS LIST
function printBlogPostsList($request){
	$dir=BASE_PATH.BLOG_DIR;
	$uri=BASE_URI.BLOG_DIR;
	//print "<hr>file exist somehow !!!!<br/>\n";
    $dirFiles=getFileList($dir,$empty);
    rsort($dirFiles);
    $html= "<div class=\"list\">\n";
	$html.= "<div class=\"title\"><h3>List of all the blog posts on this site</h3></div>\n";
	foreach($dirFiles as $file){	
		$fileUri=guessBlogUri($uri,$file);
		$inText = file_get_contents($dir.$file);
		$title=getTitleFromContent($inText);
		$html.=printTitle($title,$dir.$file,$fileUri);
	}
	$html.= "</div>";
	return $html;
}

//////////////////////////
// GET all interesting files in directory
function getFileList($dir,$searchRegexp){
	$allFiles=array();
	if ($dh = opendir($dir)){
		while (($file = readdir($dh)) !== false){
			if(interestingFile($file,$searchRegexp)){
				if(!is_dir($dir.$file)){
					array_push($allFiles,$file);
				}
			}
		}
	}
	closedir($dh);
    return $allFiles;
}
//////////////////////////
// GET PREVIOUS AND NEXT POST
function getPreNext($allFiles,$fileMiddle){
	$i=0;
	$return = array('pre'=>"",'nxt'=>"");
	//previous is the file posted before $file, so after $file.
	//next is the file posted after $file, so before $file
	foreach($allFiles as $file){
        if($file==$fileMiddle){
            if($i+1<count($allFiles)){
                $return['pre']=$allFiles[$i+1];
                }
            break;
        }
        $return['nxt']=$file;
        $i++;
	}
	return $return;
}

////////////////////////
////////LIST POSTS
function listPosts($request,$filter){
	$match;
	$offset;
	$postNumber;
	//Navigating back and forth.
	if(preg_match("/.+offset\/(\d+)(\/display\/\d+)?[\/]?$/",$request,$match)){
		$offset=$match[1];
	}
	if(preg_match("/(.+offset\/\d+)?\/display\/(\d+)[\/]?$/",$request,$match)){
		$postNumber=$match[2];
	}
	$dir=BASE_PATH.BLOG_DIR;
	$allFiles=getFileList(BASE_PATH.BLOG_DIR,$filter);
    rsort($allFiles);
    if(!isset($offset)){
		$offset=0;
	}
	if(!isset($postNumber)){
		$postNumber=BLOG_POST_NUMBER;
	}
	$printCount=0;
	$itemCount=0;
	$usedToMatch=false;
	
	//$html= printBlogNav($offset,$offset+$postNumber,count($allFiles),"left");
	$html.="<br/>";
	
	foreach($allFiles as $file){
		if($itemCount>=$offset && $printCount<$postNumber){
			$html.=printFile($dir,$file,$uri);
			$printCount++;
			$usedToMatch=true;
		}else{
			if($usedToMatch==true){break;}
		}
		$itemCount++;	
	}
	$html.= "<br/>";
	$html.=printBlogNav($offset,$offset+$postNumber,count($allFiles),"right");
	return $html;
}

/////////////////////////////
//GET BLOG FILE FROM REQUEST
//should be guess file from request.
function getBlogFileFromRequest($request){
	//$request = preg_replace("/^blog\//","/",$request);
	$date = getDateFields($request);
	$year = $date["year"];
	$month = $date["month"];
	$day = $date["day"];
	$request = preg_replace("/^blog\//","",$request);
	$request = preg_replace("/(\/\w+)$/","",$request);
	
	$file = str_replace("$year/$month/$day","",$request);
	$file = str_replace("/","",$file);
	
	//print "blog found from the request : $year$month$day.$file.text<br/>";
	return "$year$month$day.$file.text";
}

/////////////////////////////
// PRINT BLOG NAV
function printBlogNav($start,$end,$max,$align){
	$html= "<div class=\"nav\" align=\"$align\">";
	$cnt=$end-$start;
	$offset=$end;
	$html.= "<a href=\"".BASE_URI.BLOG_DIR."list\">list posts</a>";
	if($start>0 && $start-$cnt>=0){
		$off=$start-$cnt;
//		$html.= "<a href=\"".BASE_URI.BLOG_DIR."offset/$off/display/$cnt\">&larr; younger posts</a>&nbsp;&nbsp;|";
		$html.= "<br/><a href=\"".BASE_URI.BLOG_DIR."offset/$off/display/$cnt\">younger posts &larr;</a>";
	}
//	$html.= "&nbsp;&nbsp;<a href=\"".BASE_URI.BLOG_DIR."list\">- list posts -</a>&nbsp;&nbsp;";
	
	if($offset<$max){
		$html.= "<br/><a href=\"".BASE_URI.BLOG_DIR."offset/$offset/display/$cnt\">older posts &rarr;</a>";
//		$html.= "|&nbsp;&nbsp;<a href=\"".BASE_URI.BLOG_DIR."offset/$offset/display/$cnt\">older posts &rarr;</a>";
	}
	$html.= "</div>";
	return $html;
}
/////////////////////////////
//PRINTS PRE AND NEXT LINKS
function printPostNav($uri,$dir,$previous,$next,$align){
    $html=  "<div class=\"nav\" align=\"$align\">";
/*    if(isset($previous) && $previous !=""){
    	$pre=guessBlogUri($uri,$dir.$previous);
    	$html.=  '<a href="'.$pre.'" ><b>&larr;</b> older post</a>'."\n";
    	$html.=  "&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp; ";
    }
    $html.= "<a href=\"".BASE_URI.BLOG_DIR."list\">- list posts -</a>\n";
    if(isset($next) && $next != ""){
    	$nxt=guessBlogUri($uri,$dir.$next);
		$html.= "&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp; ";
    	$html.= '<a href="'.$nxt.'"> younger post <b>&rarr;</b></a><br/>'."\n";
    }
*/
    $html.= "<a href=\"".BASE_URI.BLOG_DIR."list\">list posts</a>\n";
	if(isset($previous) && $previous !=""){
    	$pre=guessBlogUri($uri,$dir.$previous);
    	$html.=  '<br/><a href="'.$pre.'" >older post <b>&rarr;</b></a>'."\n";
    }
    if(isset($next) && $next != ""){
    	$nxt=guessBlogUri($uri,$dir.$next);
    	$html.= '<br/><a href="'.$nxt.'">younger post <b>&larr;</b></a>'."\n";
    }
    $html.=  "</div>";
	return $html;
}
?>