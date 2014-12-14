<?= $this->form->startTag(); ?>
<?= $this->form->getField('action'); ?>
<?= $this->form->getField('id'); ?>

    <section class="tabs" id="groupTabs">
        <header>
            <ul>
                <li class="active"><a href="#general"><span class="icon icon-group"></span>Budova</a></li>
            </ul>
        </header>
        <section>
            <article id="group">
                <?= $this->form->getFieldset('general'); ?>
                <?= $this->form->getFieldset('submit'); ?>
            </article>
        </section>
    </section>

<?= $this->form->endTag(); ?>