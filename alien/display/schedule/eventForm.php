<?= $this->form->startTag(); ?>
<?= $this->form->getField('action'); ?>
<?= $this->form->getField('id'); ?>

    <section class="tabs" id="eventTabs">
        <header>
            <ul>
                <li class="active"><a href="#general"><span class="icon icon-group"></span>Udalosť</a></li>
                <li><a href="#map"><span class="icon icon-magnifier"></span>Mapa</a></li>
            </ul>
        </header>
        <section>
            <article id="general">
                <?= $this->form->getFieldset('general'); ?>
                <?= $this->form->getFieldset('submit'); ?>
            </article>
            <article id="map">
                <img src="<?= $this->imgSrc; ?>" style="width: 100%; height: auto">
            </article>
        </section>
    </section>

<?= $this->form->endTag(); ?>