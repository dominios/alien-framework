<div class="row">
    <div class="col-xs-12">
        <h1 id="forms" class="page-header"><?= ($this->building instanceof \Alien\Models\School\Building ? $this->building->getName() : 'Pridať budovu'); ?>
            <small><i class="fa fa-angle-double-right"></i> administrácia budovy</small>
        </h1>
    </div>
</div>

<div class="row">

    <div class="col-xs-12">

        <ul class="nav nav-tabs" role="tablist" id="myTab">
            <li role="presentation" class="active">
                <a href="#general" role="tab" data-toggle="tab"><i class="fa fa-building hidden-xs"></i> Budova</a>
            </li>
        </ul>

        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="general">

                <?= $this->form; ?>

            </div>
        </div>
    </div>

</div>