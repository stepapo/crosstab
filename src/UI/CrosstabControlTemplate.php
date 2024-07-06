<?php

declare(strict_types=1);

namespace Stepapo\Crosstab\UI;

use Stepapo\Crosstab\Column;


abstract class CrosstabControlTemplate extends DataControlTemplate
{
    public ?Column $columnColumn;

    public ?Column $rowColumn;

    public ?Column $valueColumn;
}
