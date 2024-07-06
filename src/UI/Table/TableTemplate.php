<?php

declare(strict_types=1);

namespace Stepapo\Crosstab\UI\Table;

use Stepapo\Crosstab\UI\CrosstabControlTemplate;
use Nextras\Orm\Entity\IEntity;


class TableTemplate extends CrosstabControlTemplate
{
    /** @var IEntity[][] */
    public array $items;

    /** @var IEntity[] */
    public array $columnHeaderItems;

    /** @var IEntity[] */
    public array $rowTotals;

    /** @var IEntity[] */
    public array $columnTotals;

    public IEntity $total;
}
