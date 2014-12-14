<?= $this->form->startTag(); ?>
<?= $this->form->getField('action'); ?>
<?= $this->form->getField('id'); ?>

    <section class="tabs" id="roomTabs">
        <header>
            <ul>
                <li class="active"><a href="#general"><span class="icon icon-group"></span>Miestnosť</a></li>
            </ul>
        </header>
        <section>
            <article id="general">
                <?= $this->form->getFieldset('general'); ?>
                <?= $this->form->getFieldset('submit'); ?>
            </article>
        </section>
    </section>

<?= $this->form->endTag(); ?>