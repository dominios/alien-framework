<div class="row">
    <div class="col-xs-12">
        <h1 id="forms" class="page-header"><?= ($this->room instanceof \Alien\Models\School\Room ? $this->room->getName() : 'Pridať miestnosť'); ?>
            <small><i class="fa fa-angle-double-right"></i> administrácia miestnosti</small>
        </h1>
    </div>
</div>

<div class="row">

    <div class="col-xs-12">

        <?= $this->form->startTag(); ?>
        <?= $this->form->getField('action'); ?>
        <?= $this->form->getField('id'); ?>

        <ul class="nav nav-tabs" role="tablist" id="myTab">
            <li role="presentation" class="active">
                <a href="#general" role="tab" data-toggle="tab"><i class="fa fa-building hidden-xs"></i> Budova</a>
            </li>
        </ul>

        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="general">

                <?= $this->form->getFieldset('general'); ?>
                <?= $this->form->getFieldset('submit'); ?>

            </div>
        </div>
    </div>

</div>