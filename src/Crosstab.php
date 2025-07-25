<?php

declare(strict_types=1);

namespace Stepapo\Crosstab;

use Stepapo\Data\Column;
use Stepapo\Utils\Attribute\ArrayOfType;
use Stepapo\Utils\Attribute\DefaultFromConfig;
use Stepapo\Utils\Attribute\Type;
use Stepapo\Utils\Config;


class Crosstab extends Config
{
	public string $entityName;
	public string $defaultRow;
	public string $defaultColumn;
	public string $defaultValue;
	public string $defaultSort = 'header';
	public string $defaultDirection = 'asc';
	public ?int $valueCollapse = null;
	/** @var Column[] */ #[ArrayOfType(Column::class)] public array $columns;
	#[Type(CrosstabView::class), DefaultFromConfig(CrosstabView::class)] public CrosstabView $view;
}