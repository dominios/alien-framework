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
                createModal(json);
            }
        });
    }

    function chooseFile(file, type) {
        if (!file || !type)
            return;
        $("input[name=templateSrc]").attr('value', file);
        modal.destroy();
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
                req = {
                    type: type,
                    container: $(this).attr('data-widgetContainer'),
                    parentType: $(this).attr('data-widgetParentType'),
                    parentId: $(this).attr('data-widgetParentId')
                }
                $.ajax({
                    async: true,
                    url: "/alien/ajax.php?action=widgetGenerateItem",
                    type: "POST",
                    contentType: "application/json; charset=utf-8",
                    data: JSON.stringify(req),
                    timeout: 5000,
                    success: function(data) {
                        json = jQuery.parseJSON(data);
                        console.log(json);
                        ui.item.replaceWith(json.item);
                        $ac = $("article#content");
                        $ac.css('height', 'auto');
                        $height = $ac.height();
                        $ac.css('height', $height);
                        $ac.attr('data-height', $height + 'px');
                        console.log($height);
                        $(".tabs section").height($height + 'px');
                    }
                });
            }
        });

        $(".item-creatable").draggable({
            connectToSortable: '.template-block',
            revert: 'invalid',
            helper: 'clone'
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
                        <?= $this->form->getElement('buttonSrcChoose'); ?>
                        <?= $this->form->getElement('buttonSrcMagnify'); ?>
                    </td>
                </tr><tr>
                    <td colspan="3"><div class="hr"></div></td>
                </tr><tr>
                    <td colspan="3">
                        <?= $this->form->getElement('buttonCancel'); ?>
                        <?= $this->form->getElement('buttonSubmit'); ?>
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
                <div class="template-block" data-widgetParentType="template" data-widgetParentId="<?=$this->template->getId();?>" data-widgetContainer="<?=$block->getId();?>">
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