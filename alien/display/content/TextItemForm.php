<?= $this->form->startTag(); ?>
<?= $this->form->getElement('action'); ?>
<?= $this->form->getElement('itemId'); ?>

<section class="tabs" id="itemTabs">
    <header>
        <ul>
            <li><a href="#config"><span class="icon icon-document"></span>Textový objekt</a></li>
        </ul>
    </header>
    <section>
        <article id="config" class="">
            <table class="full">
                <tr>
                    <td><span class="icon icon-template"></span>Názov objektu:</td>
                    <td colspan="2"><?= $this->form->getElement('itemName'); ?></td>
                </tr>
                <tr>
                    <td colspan="2">
                        <?=$this->form->getElement('itemContent'); ?>
                    </td>
                </tr>
            </table>
        </article>
    </section>
</section>

<?= $this->form->endTag(); ?>