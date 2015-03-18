<div class="row">
    <div class="col-xs-12">
        <h1 id="forms" class="page-header">Pridať udalosť
            <small><i class="fa fa-angle-double-right"></i> kalendár</small>
        </h1>
    </div>
</div>

<div class="row">

    <div class="col-xs-12">

        <ul class="nav nav-tabs" role="tablist" id="myTab">
            <li role="presentation" class="active">
                <a href="#general" role="tab" data-toggle="tab"><i class="fa fa-fw fa-calendar hidden-xs"></i> Udalosť</a>
            </li>
        </ul>

        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="general">

                <?= $this->form; ?>

            </div>
        </div>
    </div>

</div>

<script>

    $(function () {

        $('input[name=eventRepeatWeeks]').parents('.form-group').hide();

        $("input[name=eventRepeat]").change(function () {
            if ($(this).is(':checked')) {
                $("input[name=eventRepeatWeeks]").parents('.form-group').show();
            } else {
                $('input[name=eventRepeatWeeks]').parents('.form-group').hide();
                $("input[name=eventRepeatWeeks]").val("");
            }
        });
    });

</script>

<? return; ?>

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