<?php

declare(strict_types=1);

namespace Stepapo\Crosstab;

use Stepapo\Data\Column;
use Stepapo\Utils\Attribute\ArrayOfType;
use Stepapo\Utils\Attribute\DefaultFromSchematic;
use Stepapo\Utils\Attribute\Type;
use Stepapo\Utils\Schematic;


class Crosstab extends Schematic
{
	public string $entityName;
	public string $defaultRow;
	public string $defaultColumn;
	public string $defaultValue;
	public ?int $valueCollapse = null;
	/** @var Column[] */ #[ArrayOfType(Column::class)] public array $columns;
	#[Type(CrosstabView::class), DefaultFromSchematic(CrosstabView::class)] public CrosstabView $view;
}