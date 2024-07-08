<?php

declare(strict_types=1);

namespace Stepapo\Crosstab\UI\Table;

use Nextras\Orm\Entity\IEntity;
use Stepapo\Crosstab\UI\CrosstabControlTemplate;


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
