<?php

declare(strict_types=1);

namespace Stepapo\Crosstab\Control\Crosstab;

use Stepapo\Data\Column;
use Stepapo\Data\Control\DataTemplate;


class CrosstabTemplate extends DataTemplate
{
	public ?Column $columnColumn;
	public ?Column $rowColumn;
	public ?Column $valueColumn;
}
