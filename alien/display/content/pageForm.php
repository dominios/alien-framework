<script type="text/javascript">

    function pageShowTemplateBrowser() {
        $.ajax({
            async: true,
            url: "/alien/ajax.php",
            type: "GET",
            data: "action=pageShowTemplateBrowser",
            timeout: 5000,
            success: function (data) {
                json = jQuery.parseJSON(data);
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
                json = jQuery.parseJSON(data);
                $("input[name=pageSeolink]").attr('value', json.seolink);
            }
        });
    }

    $(function () {
        $("input[name=pageName]").on('focusout', function () {
            makeSeolinkFromName($(this).val());
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
            echo '<pre>';
            print_r($page->getUsedVariables(true));
            echo '</pre>';

            ?>


        </article>
    </section>
</section>

<?= $this->form->endTag(); ?>