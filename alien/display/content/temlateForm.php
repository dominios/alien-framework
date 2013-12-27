<script type="text/javascript">
    $(document).ready(function() {
        $("input.invalidInput").mouseover(function() {
            $(this).next('div').fadeIn(400);
        });
        $("input.invalidInput").mouseout(function() {
            $(this).next('div').fadeOut(400);
        });
    });
    function templateShowFileBrowser(type) {
        if (!type)
            return;
        $.ajax({
            async: true,
            url: "/alien/ajax.php",
            type: "GET",
            data: "action=templateShowFileBrowser&type=" + type,
            timeout: 5000,
            success: function(data) {
                json = jQuery.parseJSON(data);
                createDialog(json.header, json.content);
            }
        });
    }

    function chooseFile(file, type) {
        if (!file || !type)
            return;
        $("input[name=template" + type + "]").attr('value', file);
        $("#dialog-modal").dialog('close');
    }

    function templateShowFilePreview(file) {
        showFilePreview(file);
    }

</script>

<?= $this->form->startTag(); ?>
<?= $this->form->getElement('action'); ?>
<?= $this->form->getElement('templateId'); ?>


<section class="tabs" id="userTabs">
    <header>
        <ul>
            <li class="active"><a href="#config"><span class="icon icon-service"></span>Konfigurácia</a></li>
            <li><a href="#content"><span class="icon icon-puzzle"></span>Obsah</a></li>
        </ul>
    </header>
    <section>
        <article id="config">
            <table clas="full">
                <tr>
                    <td><span class="icon icon-template"></span> Názov šablóny:</td>
                    <td colspan="2"><?= $this->form->getElement('templateName'); ?></td>
                </tr><tr>
                    <td><span class="icon icon-note"></span> Krátky popis:</td>
                    <td colspan="2"><?= $this->form->getElement('templateDescription'); ?></td>
                </tr><tr>
                    <td><span class="icon icon-php"></span> Zdrojový súbor:</td>
                    <td><?= $this->form->getElement('templateSrc'); ?></td>
                    <td>
                        <?= $this->buttonSrcChoose; ?>
                        <?= $this->buttonSrcMagnify; ?>
                    </td>
                </tr><tr>
                    <td><span class="icon icon-service"></span> Konfiguračný súbor:</td>
                    <td><?= $this->form->getElement('templateIni'); ?></td>
                    <td>
                        <?= $this->buttonIniChoose; ?>
                        <?= $this->buttonIniMagnify; ?>
                    </td>
                </tr><tr>
                    <td colspan="3"><div class="hr"></div></td>
                </tr><tr>
                    <td colspan="3">
                        <div class="button negative" onclick="javascript: window.location = '<?= $this->returnAction; ?>';"><span class="icon icon-back"></span>Zrušiť</div>
                        <div class="button positive" onclick="javascript: $('#templateForm').submit();"><span class="icon icon-save"></span>Uložiť šablónu</div>
                    </td>
                </tr>
            </table>
        </article>
        <article id="content" class="tab-hidden">
            <p>ldsajdksad</p><p>sdjakdjsakdas</p>
            <p>
                <?
                $i = 1;

//                $blocks = $this->Template->getBlocks();

                $blocks = array();

                foreach ($blocks as $block) {

                    $urlname = $block['name'];
                    $items = $block['items'];

                    $poradie = '';
//                    $addViewAction = 'javascript: window.location=\'?content&amp;addViewToTemplate&amptid=' . $this->Template->getId() . '&amp;block=' . $i . '\'';
                    $addViewAction = BaseController::actionURL('content', 'addView', array('template' => $this->Template->getId(), 'box' => $i));

                    echo ('<fieldset style="margin-top: 10px;"><legend><img class="toggleHideable less" onClick="javascript: toggleHideable(' . $i . ');" src="' . Alien::$SystemImgUrl . '/less.png" style="width: 16px; margin-right: 6px;">' . $urlname . '</legend>');
                    echo ('<div id="hideable-' . $i . '">');
                    echo ('<div id="sortable-' . $i . '" class="sortable">');

                    foreach ($items as $item) {
                        $itemView = new View('display/content/itemList.php');
                        $itemView->Item = $item;
                        echo $itemView->renderToString();
//                        var_dump($itemView);
//                        echo $itemView->getContent();
                    }


//                        var_dump(count($block['items']));
//                            echo ('<div class="ui-state-default" id="'.$view->getId().'">');
//                            echo ('</div>');
//                            $poradie.=$view->getId().',';
                    echo ('</div>');
                    echo ('</div>');

                    $poradie = substr($poradie, 0, strlen($poradie) - 1);
                    echo ('<input type="hidden" name="order-sortable-' . $i . '" value="' . $poradie . '">');
                    echo '<a class="button neutral" style="margin-left: 5px; margin-top: 7px; margin-bottom: 10px;" href="' . $addViewAction . '"><img src="' . Alien::$SystemImgUrl . '/add.png">&nbsp;Pridat objekt do: <i>' . $urlname . '</i></a>';
                    $i++;
                    echo ('</fieldset>');
                }
                ?>
            </p>
        </article>
    </section>
</section>


<?= $this->form->endTag(); ?>