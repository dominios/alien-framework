<table class="full fieldset">
    <tbody>
    <? foreach ($this->fields as $field): ?>

        <? if ($field instanceof \Alien\Forms\Input\Hidden):
            echo $field;
            continue;
        endif;?>

        <tr>
            <td>
                <? if (strlen($field->getIcon())): ?><span class="icon <?= $field->getIcon(); ?>"></span><? endif; ?>
                <?= $field->getLabel(); ?>:
            </td>
            <td>
                <? if ($field->hasLinkedInputs()):
                    echo "<div class=\"buttonField\">";
                    foreach ($field->getLinkedInputs() as $linked):
                        echo $linked;
                    endforeach;
                    echo "</div>";
                endif;
                ?>
                <div class="inputField">
                    <?= $field; ?>
                </div>
            </td>
        </tr>
    <? endforeach; ?>
    </tbody>
</table>