<?php

declare(strict_types=1);

namespace Stepapo\Crosstab\Control\Table;

use Nextras\Orm\Collection\ICollection;
use Nextras\Orm\Entity\IEntity;
use Stepapo\Crosstab\CrosstabView;
use Stepapo\Data\Column;
use Stepapo\Data\Control\DataTemplate;


class TableTemplate extends DataTemplate
{
	/** @var IEntity[][] */ public array $items;
	/** @var IEntity[] */ public array $columnHeaderItems;
	/** @var IEntity[] */ public array $columnTotals;
	public ?IEntity $total;
	public ICollection $rowTotalCollection;
	public ICollection $columnTotalCollection;
	public ICollection $totalCollection;
	public CrosstabView $view;
	public array $filter;
	public mixed $min;
	public mixed $max;
	public mixed $rowMin;
	public mixed $rowMax;
	public mixed $columnMin;
	public mixed $columnMax;
	public ?string $sort;
	public ?string $direction;
	public Column $rowColumn;
	public Column $columnColumn;
	public Column $valueColumn;
	public TableControl $control;
}
