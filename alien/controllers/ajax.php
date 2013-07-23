<?php
require_once 'init.php';

if(!isset($_REQUEST['action'])){
    exit;
}

ob_start();

function displayLayoutType($REQ){    
    
    $type = $REQ['type'];
    
    $view = unserialize($_SESSION['SDATA']);    
    
    $view->DisplayLayout = $type;
    return $view->getContent();
}

function evalConsoleInput($REQ){
    $action = $REQ['data'];
    $ret = '';
    
    $ctrl = new ConsoleController(); 
    
    if(preg_match('/^php\s.*$/', $action)){
        error_reporting(0);
        $a = explode('php', $action, 2);
        $action = (string)$a[1];
        $ret .= (string)eval("".$action);
        $ret .= '<br>';
        echo $ret;
        exit;
    }
    
    if(method_exists($ctrl, $action) && !in_array($action, Array('init_action', 'getContent', '__construct', 'NOP', 'nop'))){
        
        $ret .= ('<span class="ConsoleTime">['.date('d.m.Y H:i:s', time()).']</span> <span class="'.AlienConsole::CONSOLE_MSG.'">'.$action.'<br>'.$ctrl->$action().'</span><br>');
    } else {
        $ret .= ('<span class="ConsoleTime">['.date('d.m.Y H:i:s', time()).']</span> <span class="'.AlienConsole::CONSOLE_ERROR.'">Command <i><b>'.$action.'</b></i> not recognized.</span><br>');
    }

    return $ret;
}

function templateShowFileBrowser($REQ){

    $header = 'Vybrať súbor';
    $ret = '';
    $content = '';
    $dir = 'templates';

    switch($REQ['type']){
        case 'php': $pattern = '.*\.php$'; $img = 'php.png'; break;
        case 'ini': $pattern = '.*\.ini$'; $img = 'service.png'; break;
        case 'css': $pattern = '.*\.css$'; $img = 'css.png'; break;
        default: return json_encode(Array('header'=>$header, 'content'=>'Invalid request'));
    }

    $ret = '<div class="gridLayout">';

    if ($dh = opendir($dir)) {
        while (($file = readdir($dh)) !== false) {
            if(preg_match('/'.$pattern.'/', $file)){
                $content .= '<div class="item" onclick="javascript: chooseFile(\'templates/'.$file.'\', \''.ucfirst($REQ['type']).'\');">';
                $content .= '<img src="'.Alien::$SystemImgUrl.'/'.$img.'" style="width: 48px; height: 48px;">';
                $content .= '<div style="position: absolute; bottom: 5px; width: 100px; text-align: center;">'.$file.'</div>';
                $content .= '</div>';
            }
        }
        closedir($dh);
    }

    if(!strlen($content)){
        $content .= 'Žiadne súbory požadovaného typu.';
    }

    $ret .= $content;
    $ret .= '</div>';

    $content .= '<div style="clear: left;"></div>';

    return json_encode(Array('header'=>$header, 'content'=>$ret));
}

function templateShowFilePreview($REQ){
    return json_encode(Array('header'=>$REQ['file'], 'content'=>highlight_file($REQ['file'], true)));
}

$action = $_REQUEST['action'];
$ret = $action($_REQUEST);
ob_clean();
echo $ret;

?>
