<?php

declare(strict_types=1);

namespace Stepapo\Crosstab;

use Stepapo\Data\View;
use Stepapo\Utils\Schematic;


class CrosstabView extends Schematic implements View
{
	public const array DEFAULT_VIEW = [
		'crosstabTemplate' => __DIR__ . '/Control/Crosstab/crosstab.latte',
		'tableTemplate' => __DIR__ . '/Control/Table/table.latte',
		'filterListTemplate' => __DIR__ . '/../../data/src/Control/FilterList/list.latte',
		'filterTemplate' => __DIR__ . '/../../data/src/Control/Filter/list.latte',
		'rowPickerTemplate' => __DIR__ . '/Control/RowPicker/rowPicker.latte',
		'columnPickerTemplate' => __DIR__ . '/Control/ColumnPicker/columnPicker.latte',
		'valuePickerTemplate' => __DIR__ . '/Control/ValuePicker/valuePicker.latte',
		'valueTemplate' => __DIR__ . '/../../data/src/Control/Value/value.latte'
	];
	public string $crosstabTemplate = self::DEFAULT_VIEW['crosstabTemplate'];
	public string $tableTemplate = self::DEFAULT_VIEW['tableTemplate'];
	public string $filterListTemplate = self::DEFAULT_VIEW['filterListTemplate'];
	public string $filterTemplate = self::DEFAULT_VIEW['filterTemplate'];
	public string $rowPickerTemplate = self::DEFAULT_VIEW['rowPickerTemplate'];
	public string $columnPickerTemplate = self::DEFAULT_VIEW['columnPickerTemplate'];
	public string $valuePickerTemplate = self::DEFAULT_VIEW['valuePickerTemplate'];
	public string $valueTemplate = self::DEFAULT_VIEW['valueTemplate'];


	public static function createFromArray(mixed $config = [], mixed $key = null, bool $skipDefaults = false, mixed $parentKey = null): static
	{
		return parent::createFromArray(array_merge(self::DEFAULT_VIEW, (array) $config), $key, $skipDefaults, $parentKey);
	}
}