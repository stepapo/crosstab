<?php

declare(strict_types=1);

namespace Stepapo\Crosstab\Control\ColumnPicker;

use Stepapo\Data\Column;
use Stepapo\Data\Control\DataTemplate;


class ColumnPickerTemplate extends DataTemplate
{
	public ?string $column;
	public array $columns;
	public Column $rowColumn;
}
