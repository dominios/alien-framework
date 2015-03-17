<div class="panel panel-primary">
    <? if (strlen($this->name)): ?>
        <div class="panel-heading"><?= $this->name; ?></div>
    <? endif; ?>
    <table class="table data-table">
        <thead>
        <tr>
            <?
            foreach ($this->header as $key => $value):
                echo "<th>$value</th>";
            endforeach;
            ?>
        </tr>
        </thead>
        <tbody>
        <?
        foreach ($this->rows as $key => $values):
            echo "<tr>";
            foreach ($values as $value):
                echo "<td>$value</td>";
            endforeach;
            echo "</tr>";
        endforeach;
        ?>
        </tbody>
    </table>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('.data-table').DataTable(<?= json_encode($this->options); ?>);
    });
</script>