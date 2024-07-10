<?php

declare(strict_types=1);

namespace Stepapo\Crosstab\Control\RowPicker;

use Stepapo\Data\Column;
use Stepapo\Data\Control\DataTemplate;


class RowPickerTemplate extends DataTemplate
{
    public ?string $row;
	public array $columns;
	public Column $columnColumn;
}
