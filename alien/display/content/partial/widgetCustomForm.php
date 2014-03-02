<? foreach ($this->formElements as $input): ?>
    <tr>
        <td><span class="icon <?= $input->getIcon(); ?>"></span><?= $input->getLabel(); ?>:</td>
        <td><?= $input; ?></td>
    </tr>
<? endforeach; ?>