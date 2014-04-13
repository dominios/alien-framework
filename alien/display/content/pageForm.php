<script type="text/javascript">

    function pageShowTemplateBrowser() {
        $.ajax({
            async: true,
            url: "/alien/ajax.php",
            type: "GET",
            data: "action=pageShowTemplateBrowser",
            timeout: 5000,
            success: function (data) {
                var json = jQuery.parseJSON(data);
                createModal(json);
            }
        });
    }

    function chooseTemplate(id, name) {
        if (!id || !name)
            return;
        $("input[name=pageTemplateHelper]").attr('value', name);
        $("input[name=pageTemplate]").attr('value', id);
        modal.destroy();
    }

    function makeSeolinkFromName(name) {
        if (!name) return;
        $.ajax({
            async: true,
            url: "/alien/ajax.php",
            type: "GET",
            data: "action=pageMakeSeolinkFromName&name=" + name,
            timeout: 5000,
            success: function (data) {
                var json = jQuery.parseJSON(data);
                $("input[name=pageSeolink]").attr('value', json.seolink);
            }
        });
    }

    $(function () {

        $("aside#rightFloatPanel").removeClass('disabled');

        $("section.tabs").find('li a').live('click', function () {
            if ($(this).attr('href') === '#content') {
                $("aside#rightFloatPanel").removeClass('disabled');
            } else {
                $("aside#rightFloatPanel").addClass('disabled');
            }
        });

        $("input[name=pageName]").on('focusout', function () {
            makeSeolinkFromName($(this).val());
        });

        $(".page-block").sortable({
            items: ".item",
            cursor: "move",
            placeholder: "ui-state-highlight",
            delay: 200,
            opacity: 0.65,
            revert: 200,
            scroll: true,
            stop: function (ev, ui) {
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
                    success: function (data) {
                        json = jQuery.parseJSON(data);
                        ui.item.replaceWith(json.item);
                        $ac = $("article#content");
                        $ac.css('height', 'auto');
                        $height = $ac.height();
                        $ac.css('height', $height);
                        $ac.attr('data-height', $height + 'px');
                        $(".tabs section").height($height + 'px');
                    }
                });
            }
        });

        $(".item-creatable").draggable({
            connectToSortable: '.page-block',
            revert: 'invalid',
            helper: 'clone'
        });
    });

</script>

<?= $this->form->startTag(); ?>
<?= $this->form->getElement('action'); ?>
<?= $this->form->getElement('pageId'); ?>

<section class="tabs" id="pageTabs">
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
                    <td><span class="icon icon-template"></span>Názov stránky:</td>
                    <td colspan="2"><?= $this->form->getElement('pageName'); ?></td>
                </tr>
                <tr>
                    <td><span class="icon icon-link"></span>Seolink:</td>
                    <td colspan="2"><?= $this->form->getElement('pageSeolink'); ?></td>
                </tr>
                <tr>
                    <td><span class="icon icon-note"></span>Popis stránky:</td>
                    <td colspan="2"><?= $this->form->getElement('pageDescription'); ?></td>
                </tr>
                <tr>
                    <td><span class="icon icon-template"></span>Šablóna:</td>
                    <td>
                        <?= $this->form->getElement('pageTemplateHelper'); ?>
                        <?= $this->form->getElement('pageTemplate'); ?>
                        <?= $this->form->getElement('buttonTemplateChoose'); ?>
                        <?= $this->form->getElement('buttonTemplatePreview'); ?>
                    </td>
                </tr>
                <tr>
                    <td><span class="icon icon-template"></span>Kľúčové slová:</td>
                    <td colspan="2"><?= $this->form->getElement('pageKeywords'); ?></td>
                </tr>
                <tr>
                    <td colspan="3">
                        <hr>
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <?= $this->form->getElement('buttonCancel'); ?>
                        <?= $this->form->getElement('buttonSubmit'); ?>
                    </td>
                </tr>
            </table>
        </article>
        <article id="content">
            <?
            $page = $this->page;
            $variables = $page->getUsedVariables(true);
            foreach ($variables as $variableWidget):
                $variableWidget->setPageToRender($page);
                $variableWidget->fetchContainerContent();
                ?>
                <div class="page-block" data-widgetParentType="page"
                     data-widgetParentId="<?= $this->page->getId(); ?>"
                     data-widgetContainer="<?= $variableWidget->getId() ?>">
                    <h2><?= $variableWidget->getParam('name'); ?></h2>
                    <?
                    $params = array(
                        'layout' => 'row',
                        'sortable' => true,
                        'items' => $variableWidget->getWidgetContainer()
                    );
                    echo $this->partial('display/content/viewList.php', $params);
                    ?>
                </div>
            <?
            endforeach;
            ?>
        </article>
    </section>
</section>

<?= $this->form->endTag(); ?>