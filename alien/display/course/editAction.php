<div class="row">
    <div class="col-xs-12">
        <h1 class="page-header"><?= $this->course instanceof \Alien\Models\School\Course ? $this->course->getName() : 'Nový kurz'; ?>
            <small><i class="fa fa-angle-double-right"></i> administrácia kurzu</small>
        </h1>
    </div>
</div>

<div class="row">
    <div class="col-xs-12">
        <ul class="nav nav-tabs" role="tablist" id="myTab">
            <li role="presentation" class="active">
                <a href="#general" role="tab" data-toggle="tab"><i class="fa fa-file hidden-xs"></i> Kurz</a>
            </li>
        </ul>

        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="general">
                <?= $this->form; ?>
            </div>
        </div>
    </div>
</div>