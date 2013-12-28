<?

namespace Alien\Models\Content\Template;

use Alien\Models\Content\TemplateBlock;

echo 'Pridať box:';
$items = TemplateBlock::getList(true);
foreach ($items as $item):
    echo '<div class="button">';
    echo '<span class="icon icon-varx"></span>' . $item->getName();
    echo '</div>';
endforeach;

echo '<p>Pridať widget:';
echo '<p>todo...';
