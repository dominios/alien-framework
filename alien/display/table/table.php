<?php

echo "<table class=\"itemList\">";
echo "<thead>";
echo "<tr class=\"itemHeaderRow\">";
foreach ($this->header as $key => $value):
    echo "<th>$value</th>";
endforeach;
echo '</tr>';
echo "</thead>";

echo "<tbody>";
foreach ($this->rows as $key => $values):
    echo "<tr>";
    foreach ($values as $value):
        echo "<td>$value</td>";
    endforeach;
    echo "</tr>";
endforeach;
echo "<tbody>";

echo "</table>";
