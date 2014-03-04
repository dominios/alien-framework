<script type="text/javascript">
    $(document).ready(function () {
        $("#tabs").tabs();
    });
</script>

<?= $this->form->startTag(); ?>

<section class="tabs" id="userTabs">
    <header>
        <ul>
            <li class="active"><a href="#config"><span class="icon icon-service"></span>Konfigurácia</a></li>
        </ul>
    </header>
    <section>
        <article id="config" class="">
            <table class="full">
                <tr>
                    <td><span class="icon icon-file"></span>Typ widgetu:</td>
                    <td>todo</td>
                </tr>
                <tr>
                    <td><span class="icon icon-file"></span>Zobrazovač:</td>
                    <td><?= $this->form->getElement('widgetTemplate'); ?></td>
                </tr>
                <tr>
                    <td><span class="icon icon-file"></span>Viditeľnosť:</td>
                    <td><?= $this->form->getElement('widgetVisibility'); ?></td>
                </tr>
                <?=$this->unescaped('customPart'); ?>
            </table>
        </article>
    </section>
</section>
<?= $this->form->endTag(); ?>