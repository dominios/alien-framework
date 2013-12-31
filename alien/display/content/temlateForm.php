<script type="text/javascript">
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

    $(function() {
        $("aside#rightFloatPanel").removeClass('disabled');
        $("section.tabs").find('li a').live('click', function() {
            if ($(this).attr('href') === '#content') {
                $("aside#rightFloatPanel").removeClass('disabled');
            } else {
                $("aside#rightFloatPanel").addClass('disabled');
            }
        });

        $(".template-block").sortable({
            items: ".item",
            cursor: "move",
            placeholder: "ui-state-highlight",
            delay: 200,
            opacity: 0.65,
            revert: 200,
            scroll: true,
            stop: function(ev, ui) {
                type = ui.item.attr('data-type');
                if (!type) {
                    return;
                }
                $.ajax({
                    async: true,
                    url: "/alien/ajax.php",
                    type: "GET",
                    data: "action=widgetGenerateItem&type=" + type,
                    timeout: 5000,
                    success: function(data) {
                        json = jQuery.parseJSON(data);
                        ui.item.replaceWith(json.item);
                    }
                });
            }
        });

        $(".item-creatable").draggable({
            connectToSortable: '.template-block',
            revert: 'invalid',
            helper: 'clone',
        });

    });
</script>

<?= $this->form->startTag(); ?>
<?= $this->form->getElement('action'); ?>
<?= $this->form->getElement('templateId'); ?>


<section class="tabs" id="userTabs">
    <header>
        <ul>
            <li><a href="#config"><span class="icon icon-service"></span>Konfigurácia</a></li>
            <li class="active"><a href="#content"><span class="icon icon-puzzle"></span>Obsah</a></li>
        </ul>
    </header>
    <section>
        <article id="config" class="tab-hidden">
            <table class="full">
                <tr>
                    <td><span class="icon icon-template"></span> Názov šablóny:</td>
                    <td colspan="2"><?= $this->form->getElement('templateName'); ?></td>
                </tr><tr>
                    <td><span class="icon icon-note"></span> Krátky popis:</td>
                    <td colspan="2"><?= $this->form->getElement('templateDescription'); ?></td>
                </tr><tr>
                    <td><span class="icon icon-code"></span> Zdrojový súbor:</td>
                    <td><?= $this->form->getElement('templateSrc'); ?></td>
                    <td>
                        <?= $this->buttonSrcChoose; ?>
                        <?= $this->buttonSrcMagnify; ?>
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
        <article id="content">
            <?
            $blocks = $this->template->fetchBlocks();
            foreach ($blocks as $block):
                $block->setTemplate($this->template);
                ?>
                <div class="template-block">
                    <h2><?= $block->getName(); ?></h2>
                    <?
                    $params = array(
                        'layout' => 'row',
                        'sortable' => true,
                        'items' => $block->getWidgets()
                    );
                    echo $this->partial('display/content/viewList.php', $params);
//                    foreach ($block->getWidgets() as $widget):
//
//                        echo '<div class="template-widget">' . $widget['id_v'] . '</div>';
//                    endforeach;
                    ?>
                </div>
                <?
            endforeach;
            ?>
        </article>
    </section>
</section>

<?= $this->form->endTag(); ?>