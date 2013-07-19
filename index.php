<?php die('ALiEN V2 under construction');
//$microtime = MicroTime(1);
//require_once 'alien/init.php';
//
//$uri=$_SERVER['REQUEST_URI'];
//$uri_arr = explode('/',$uri);
//$get=explode('?',end($uri_arr),2);
//$seolink=$get[0];
//
//if(empty($seolink)){
//    //$PAGE = Alien::getHomePage();
//	$PAGE = new ContentPage(1);
//} else {
//    try {
//        $PAGE=new ContentPage($seolink);
//    } catch(UnknownPageException $ex){
//        header("HTTP/1.1 404 Not Found");
//        include 'alien/error/Error404.html';
//        exit;
//        //$PAGE=new ContentPage(Alien::getParameter("notFoundPage")); // zmenit ptm na ERROR 404..
//    }
//}
//$TEMPLATE=$PAGE->getTemplate();
//$BLOCKS=$TEMPLATE->getTemplateBlocks();
//$ALIENWEB['title']=$PAGE->getTitle();
//$ALIENWEB['description']=$PAGE->getDescription();
//$ALIENWEB['keywords']=$PAGE->getKeywords();
//$ALIENWEB['default-css']=$TEMPLATE->getCssUrl();
//foreach($BLOCKS as $BLOCK){
//    $ALIENWEB[$BLOCK]=$PAGE->getPageRenderedContent($BLOCK);
//}
//require_once 'alien/'.$PAGE->getTemplate()->getHtmlUrl();
//
//Alien::renderDebugWindow($microtime);
?>
