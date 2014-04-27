<table class="full fieldset">
    <tbody>
    <tr>
        <td>
            <div class="hr"></div>
        </td>
    </tr>
    <tr>
        <td>
            <? foreach ($this->fields as $field):
                if ($field instanceof \Alien\Forms\Input\Hidden):
                    echo $field;
                    continue;
                endif;
                echo $field;
            endforeach; ?>
        </td>
    </tr>
    </tbody>
</table>