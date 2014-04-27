<table class="full fieldset">
    <tbody>
    <tr>
        <td>
            <hr>
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