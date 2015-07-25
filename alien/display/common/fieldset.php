<? foreach ($this->fields as $field): ?>

    <? if ($field instanceof \Alien\Form\Input\Hidden):
        echo $field;
        continue;
    endif;?>

    <div class="form-group">

        <label for="<?= $field->getName(); ?>" class="col-sm-2 control-label">
            <? if (strlen($field->getIcon())): ?><i class="<?= $field->getIcon(); ?>"></i><? endif; ?>
            <?= $field->getLabel(); ?>
        </label>

        <div class="col-sm-10">
            <?= $field; ?>
        </div>

    </div>
    
<? endforeach; ?>