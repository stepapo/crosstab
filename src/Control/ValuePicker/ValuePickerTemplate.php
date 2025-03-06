<?php

declare(strict_types=1);

namespace Stepapo\Crosstab\Control\ValuePicker;

use Stepapo\Data\Control\DataTemplate;


class ValuePickerTemplate extends DataTemplate
{
	public ?string $value;
	public array $columns;
	public ?int $valueCollapse;
}
