<script type="text/javascript">
    $(document).ready(function(){
        $( "#tabs" ).tabs();
        markBadInputs();
        $("input.invalidInput").mouseover(function(){
            $(this).next('div').fadeIn(400);
        });
        $("input.invalidInput").mouseout(function(){
            $(this).next('div').fadeOut(400);
        });
    });

    function createDialog(header, content){
        $("#dialog-modal").remove();
        newhtml = "<div id='dialog-modal' title='"+header+"'>";
        newhtml += "<div id='dialog-content'><p>"+content+"</p></div>";
        newhtml += "</div>";
        $("body").append(newhtml);
        $(function() {
            $("#dialog-modal").dialog({
                modal: true,
                width: 'auto',
                height: 'auto',
                show: {
                    effect: 'drop',
                    duration: 100
                },
                hide: {
                    effect: 'drop',
                    duration: 100
                }
            });
        });
    }

    function templateShowFileBrowser(type){
        if(!type) return;
        $.ajax({
            async: true,
            url: "/alien/ajax.php",
            type: "GET",
            data: "action=templateShowFileBrowser&type="+type,
            timeout: 5000,
            success: function(data){
                json = jQuery.parseJSON(data);
                createDialog(json.header, json.content);
            }
        });
    }

    function chooseFile(file, type){
        if(!file || !type) return;
        $("input[name=template"+type+"]").attr('value', file);
        $("#dialog-modal").dialog('close');
    }

    function templateShowFilePreview(file){
        if(!file) return;
        $.ajax({
            async: true,
            url: "/alien/ajax.php",
            type: "GET",
            data: "action=templateShowFilePreview&file="+file,
            timeout: 5000,
            success: function(data){
                json = jQuery.parseJSON(data);
                createDialog(json.header, json.content);
                if($("#dialog-modal").width() > 1000){
                    $("#dialog-modal").width(1000);
                }
                if($("#dialog-modal").height() > 550){
                    $("#dialog-modal").height(550);
                }
            }
        });
    }

    function markBadInputs(){
        json = jQuery.parseJSON('<?=$_SESSION['formErrorOutput'];?>');
        if(json == null) return;
        for(i = 0; i <= json.length; i++){
            item = json.pop();
            $("input[name="+item.inputName+"]").addClass('invalidInput');
            $("<div class=\"inputErrorHelper\">"+item.errorMsg+"</div>").insertAfter($("input[name="+item.inputName+"]"));
        }
        <? unset($_SESSION['formErrorOutput']); ?>
    }

</script>

<form name="editTemplateForm" method="POST" action="" id="templateForm">
    <input type="hidden" name="action" value="templateFormSubmit">
    <input type="hidden" name="templateId" value="<?=$this->Template->getId();?>">

    <div id="tabs">
        <ul>
            <li><a href="#tabs-1">Obsah</a></li>
            <li><a href="#tabs-2">Konfigurácia</a></li>
        </ul>
        <div id="tabs-2">
            <table>
                <tr>
                    <td><img src="<?=Alien::$SystemImgUrl;?>template.png" alt="name"> Názov šablóny:</td>
                    <td colspan="2"><input type="text" name="templateName" value="<?=$this->Template->getName();?>" style="width: 600px;"></td>
                </tr><tr>
                <td><img src="<?=Alien::$SystemImgUrl;?>note.png" alt="name"> Krátky popis:</td>
                <td colspan="2"><input type="text" name="templateDesc" value="<?=$this->Template->getDescription();?>" style="width: 600px;"></td>
            </tr><tr>
                <td><img src="<?=Alien::$SystemImgUrl;?>php.png" alt="php"> Súbor PHP:</td>
                <td><input type="text" name="templatePhp" value="<?=$this->Template->getHTMLUrl();?>" style="width: 505px;"></td>
                <td>
                    <div class="button" onclick="templateShowFileBrowser('php');"><img src="<?=Alien::$SystemImgUrl;?>external_link.png"></div>
                    <div class="button" onclick="templateShowFilePreview($('input[name=templatePhp]').attr('value'));"><img src="<?=Alien::$SystemImgUrl;?>magnifier.png"></div>
                </td>
            </tr><tr>
                <td><img src="<?=Alien::$SystemImgUrl;?>css.png" alt="css"> Súbor CSS:</td>
                <td><input type="text" name="templateCss" value="<?=$this->Template->getCSSUrl();?>" style="width: 505px;"></td>
                <td>
                    <div class="button" onclick="templateShowFileBrowser('css');"><img src="<?=Alien::$SystemImgUrl;?>external_link.png"></div>
                    <div class="button" onclick="templateShowFilePreview($('input[name=templateCss]').attr('value'));"><img src="<?=Alien::$SystemImgUrl;?>magnifier.png"></div>
                </td>
            </tr><tr>
                <td><img src="<?=Alien::$SystemImgUrl;?>service.png" alt="css"> Konfiguračný súbor:</td>
                <td><input type="text" name="templateIni" value="<?=$this->Template->getConfigUrl();?>" style="width: 505px;"></td>
                <td>
                    <div class="button" onclick="templateShowFileBrowser('ini');"><img src="<?=Alien::$SystemImgUrl;?>external_link.png"></div>
                    <div class="button" onclick="templateShowFilePreview($('input[name=templateIni]').attr('value'));"><img src="<?=Alien::$SystemImgUrl;?>magnifier.png"></div>
                </td>
            </tr><tr>
                <td colspan="3"><hr></td>
            </tr><tr>
                <td colspan="3">
                    <div class="button negative" onclick="javascript: window.location='<?=$this->ReturnAction;?>';"><img src="<?=Alien::$SystemImgUrl;?>back.png" alt="cancel"> Zrušiť</div>
                    <div class="button positive" onclick="javascript: $('#templateForm').submit();"><img src="<?=Alien::$SystemImgUrl;?>save.png" alt="save"> Uložiť šablónu</div>
                </td>
            </tr>
            </table>
        </div>
        <div id="tabs-1">
            <p>
                <?
                    $i = 1;

                    $blocks = $this->Template->getBlocks();

                    foreach($blocks as $block){

                        $name = $block['name'];
                        $items = $block['items'];

                        $poradie = '';
                        $addViewAction='javascript: window.location=\'?content&amp;addViewToTemplate&amptid='.$this->Template->getId().'&amp;block='.$i.'\'';

                        echo ('<fieldset style="margin-top: 10px;"><legend><img class="toggleHideable less" onClick="javascript: toggleHideable('.$i.');" src="'.Alien::$SystemImgUrl.'/less.png" style="width: 16px; margin-right: 6px;">'.$name.'</legend>');
                        echo ('<div id="hideable-'.$i.'">');
                        echo ('<div id="sortable-'.$i.'" class="sortable">');

                        foreach($items as $item){
                            $itemView = new AlienView('display/content/itemList.php');
                            $itemView->Item = $item;
//                            echo $itemView->getContent();
                        }


//                        var_dump(count($block['items']));

//                            echo ('<div class="ui-state-default" id="'.$view->getId().'">');
//                            echo ('</div>');
//                            $poradie.=$view->getId().',';
                        echo ('</div>');
                        echo ('</div>');

                        $poradie=substr($poradie,0,strlen($poradie)-1);
                        echo ('<input type="hidden" name="order-sortable-'.$i.'" value="'.$poradie.'">');
                        echo '<div class="button neutral" style="margin-left: 5px; margin-top: 7px; margin-bottom: 10px;" onClick="'.$addViewAction.'"><img src="'.ALien::$SystemImgUrl.'/plus.png">&nbsp;Pridat objekt do: <i>'.$name.'</i></div>';
                        $i++;
                        echo ('</fieldset>');
                    }
                ?>
            </p>
        </div>
    </div>


</form>





















<? return; ?>


if((!$new && Authorization::permissionTest(null, array('TEMPLATE_EDIT'))) || $new){
echo ('<form name="'.($new ? 'new' : 'edit').'TemplateForm" method="POST" action="" id="templateForm">');
    echo ('<input type="hidden" name="action" value="templateSubmit">');
    echo ('<fieldset><legend>Konfigurácia šablóny</legend><table>');

        if(!$new){
        echo ('<input type="hidden" name="templateId" value="'.$template->getId().'">');
        } else {
        echo ('<input type="hidden" name="templateId" value="-1">');
        }

        echo ('<tr>
            <td><img src="images/icons/layout.png" alt="name"> '.obsahSablonaNazov.':</td><td colspan="2"><input type="text" name="templateName" value="'.($new ? @$_POST['templateName'] : $template->getName()).'" size="25"></td></tr>');
        echo ('<tr><td><img src="images/icons/text.png" alt="name"> '.obsahSablonaPopis.':</td><td colspan="2"><input type="text" name="templateDesc" value="'.($new ? @$_POST['templateDesc'] : $template->getDescription()).'" size="45"></td></tr>');
        echo ('<tr><td><img src="images/icons/html.png" alt="html"> '.obsahSablonaHTMLURL.':</td><td><input type="text" name="templateHtml" size="35" value="'.($new ? @$_POST['templateHtml'] : $template->getHtmlUrl()).'"></td><td><div class="button" onClick="javascript: contentShowChooseFile(\'php\');"><img src="images/icons/folder_explore.png"></div></div></td></tr>');
        echo ('<tr><td><img src="images/icons/css.png" alt="css"> '.obsahSablonaCSSURL.':</td><td><input type="text" name="templateCss" size="35" value="'.($new ? @$_POST['templateCss'] : $template->getCssUrl()).'"></td><td><div class="button" onClick="javascript: contentShowChooseFile(\'css\');"><img src="images/icons/folder_explore.png"></div></td></tr>');
        echo ('<tr><td><img src="images/icons/cog.png" alt="css"> '.obsahSablonaConfigSubor.':</td><td><input type="text" name="templateConfig" size="35" value="'.($new ? @$_POST['templateConfig'] : $template->getConfigUrl()).'"></td><td><div class="button" onClick="javascript: contentShowChooseFile(\'ini\');"><img src="images/icons/folder_explore.png"></div></td></tr>');
        echo ('<tr><td colspan="3"><hr></td></tr>');
        echo ('<tr><td colspan="3">
            <div class="button negative" onClick="javascript: window.location=\''.$_SESSION['returnAction'].'\';"><img src="images/icons/cancel.png" alt="cancel"> '.zrusit.'</div>
            <div class="button positive" onClick="javascript: $(\'#templateForm\').submit();"><img src="images/icons/save.png" alt="save"> '.obsahSablonaUlozit.'</div>
        </tr>');
        echo ('</table></fieldset></form>');
}

if($new) return;

echo ('<h2>Obsah šablóny</h2>');


$sortTurnOnAction = 'javascript: turnSortableOn();';
$sortTurnOffAction = 'javascript: turnSortableOff(false);';
$sortSaveAction = 'javascript: saveSorting(\'ContentTemplateSortItems\', '.$template->id.');';
echo ('
<div class="button neutral" id="sortableTurnOn" onClick="'.$sortTurnOnAction.'"><img src="images/icons/transform_layer.png"> Preusporiadať</div>
<div class="button negative" id="sortableCancel" style="display: none;" onClick="'.$sortTurnOffAction.'"><img src="images/icons/cancel.png"> Zrušiť zmeny</div>
<div class="button positive" id="sortableSave" onClick="'.$sortSaveAction.'"><img src="images/icons/save.png"> Uložiť usporiadanie</div>
');

$_SESSION['parentType']='ContentTemplate';
$_SESSION['parentId']=$template->getID();
$_SESSION['returnAction']='?page=content&action=editTemplate&id='.$template->id;

$boxes=$template->getTemplateBlocks();

$STH=$DBH->prepare("SELECT id_v, container FROM ".Alien::getParameter('db_prefix')."_content_views WHERE id_t=:id ORDER BY position");
$STH->bindValue(':id',$template->getId(),PDO::PARAM_INT);
$STH->setFetchMode(5);
$i=1;
foreach($boxes as $box => $name){
echo ('<fieldset style="margin-top: 10px;"><legend><img class="toggleHideable less" onClick="javascript: toggleHideable('.$i.');" src="images/icons/less.png" style="width: 16px; margin-right: 6px;">'.$name.'</legend>');
    echo ('<div id="hideable-'.$i.'">');
        $poradie='';
        $STH->execute();
        echo ('<div id="sortable-'.$i.'" class="sortable">');
            while($obj=$STH->fetch()){
            if($obj->container==$i){
            $view=new ContentItemView($obj->id_v);
            if($view!=null){
            echo ('<div class="ui-state-default" id="'.$view->getId().'">');
                $view->renderInListOfViews();
                echo ('</div>');
            $poradie.=$view->getId().',';
            }
            }
            }
            echo ('</div>');
        $poradie=substr($poradie,0,strlen($poradie)-1);
        echo ('<input type="hidden" name="order-sortable-'.$i.'" value="'.$poradie.'">');
        $addViewAction='javascript: window.location=\'?page=content&amp;action=addViewToTemplate&amp;tid='.$template->id.'&amp;box='.$i.'&viewId=-1&typeId=-1\'';
        echo '<div class="button neutral" style="margin-left: 5px; margin-top: 7px; margin-bottom: 10px;" onClick="'.$addViewAction.'"><img src="images/icons/plus.png">&nbsp;Pridat objekt do: <i>'.$name.'</i></div>';
        $i++;
        echo ('</div>');
    echo ('</fieldset>');
}