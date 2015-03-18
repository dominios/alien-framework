<div class="hr hr-dashed col-xs-offset-2"></div>

<div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
        <div class="btn-group" role="group">
            <? foreach ($this->fields as $field): ?>

                <? if ($field instanceof \Alien\Forms\Input\Hidden):
                    echo $field;
                    continue;
                endif;

                if ($field instanceof \Alien\Forms\Input\Hidden):
                    echo $field;
                    continue;
                endif;
                echo $field;

            endforeach; ?>
        </div>
    </div>
</div>

