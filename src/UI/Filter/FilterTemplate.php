<?php

declare(strict_types=1);

namespace Stepapo\Crosstab\UI\Filter;

use Stepapo\Crosstab\Column;
use Stepapo\Crosstab\UI\CrosstabControlTemplate;


class FilterTemplate extends CrosstabControlTemplate
{
    public Column $column;

    public ?string $value;
}
