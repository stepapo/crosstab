<?php

declare(strict_types=1);

namespace Stepapo\Crosstab\Control\Table;

use Collator;
use Nette\Application\Attributes\Persistent;
use Nette\Forms\Form;
use Nextras\Orm\Collection\ICollection;
use Nextras\Orm\Entity\IEntity;
use Nextras\Orm\Relationships\HasMany;
use Stepapo\Crosstab\Control\Crosstab\CrosstabControl;
use Stepapo\Data\Column;
use Stepapo\Data\Control\DataControl;


/**
 * @property-read TableTemplate $template
 */
class TableControl extends DataControl
{
	#[Persistent] public ?string $sort = null;
	#[Persistent] public ?string $direction = null;
    /** @var callable[] */ public array $onSort = [];


	/** @param Column[] $columns */
	public function __construct(
		private CrosstabControl $main,
		private array $columns,
		private ICollection $collection,
		private ICollection $rowTotalCollection,
		private ICollection $columnTotalCollection,
		private ICollection $totalCollection,
	) {}


	public function loadState(array $params): void
	{
		parent::loadState($params);
		if (!$this->sort && !$this->direction) {
			foreach ($this->columns as $column) {
				if ($column->sort?->isDefault) {
					$this->sort = $column->name;
					$this->direction = $column->sort->direction;
					break;
				}
			}
		}
	}


	public function render(): void
    {
		Form::
        $items = [];
        $columnHeaderItems = [];
        $min = null;
        $max = null;
        $rowMin = null;
        $rowMax = null;
        $columnMin = null;
        $columnMax = null;
        foreach ($this->collection as $item) {
            $rowValue = $item->getRawValue($this->main->getRowColumn()->name);
            $columnValue = $item->getRawValue($this->main->getColumnColumn()->name);
            if (!isset($items[$rowValue])) {
                $items[$rowValue]['header'] = $item;
            }
            $items[$rowValue][$columnValue] = $item;
            if (!isset($columnHeaderItems[$columnValue])) {
                $columnHeaderItems[$columnValue] = $item;
            }
            $value = $this->getValue($item, $this->main->getValueColumn()->columnName) ? $this->getValue($item, $this->main->getValueColumn()->columnName)[0] : null;
            if ($value !== null && ($min === null || $value < $min)) {
                $min = $value;
            }
            if ($value !== null && ($max === null || $value > $max)) {
                $max = $value;
            }
        }
        if ($this->rowTotalCollection) {
            foreach ($this->rowTotalCollection as $item) {
                $items[$item->getRawValue($this->main->getRowColumn()->name)]['total'] = $item;
                $value = $this->getValue($item, $this->main->getValueColumn()->columnName) ? $this->getValue($item, $this->main->getValueColumn()->columnName)[0] : null;
                if ($value !== null && ($rowMin === null || $value < $rowMin)) {
                    $rowMin = $value;
                }
                if ($value !== null && ($rowMax === null || $value > $rowMax)) {
                    $rowMax = $value;
                }
            }
        }
        if ($this->columnTotalCollection) {
            $columnTotals = [];
            foreach ($this->columnTotalCollection as $item) {
                $columnTotals[$item->getRawValue($this->main->getColumnColumn()->name)] = $item;
                $value = $this->getValue($item, $this->main->getValueColumn()->columnName) ? $this->getValue($item, $this->main->getValueColumn()->columnName)[0] : null;
                if ($value !== null && ($columnMin === null || $value < $columnMin)) {
                    $columnMin = $value;
                }
                if ($value !== null && ($columnMax === null || $value > $columnMax)) {
                    $columnMax = $value;
                }
            }
            $this->template->columnTotals = $columnTotals;
        }
        if ($this->totalCollection) {
            $this->template->total = $this->totalCollection->limitBy(1)->fetch();
        }
        $collator = new Collator('cs_CZ');
        uasort($columnHeaderItems, function($a, $b) use($collator) {
            $aHeader = $this->getValue($a, $this->main->getColumnColumn()->columnName)[0];
            $bHeader = $this->getValue($b, $this->main->getColumnColumn()->columnName)[0];
            return is_numeric($aHeader) && is_numeric($bHeader)
                ? $aHeader <=> $bHeader
                : $collator->compare($aHeader, $bHeader);
        });
        if ($this->sort) {
            uasort($items, function($a, $b) use ($collator) {
                $aHeader = $this->getValue($a['header'], $this->main->getRowColumn()->columnName)[0];
                $bHeader = $this->getValue($b['header'], $this->main->getRowColumn()->columnName)[0];
                if ($this->sort == 'header') {
                    if (is_numeric($aHeader) && is_numeric($bHeader)) {
                        return $this->direction == ICollection::ASC
                            ? $aHeader <=> $bHeader
                            : $bHeader <=> $aHeader;
                    } else {
                        return $collator->compare(
                                $aHeader,
                                $bHeader
                            ) * ($this->direction == ICollection::ASC ? 1 : -1);
                    }
                } else {
                    $aVal = (isset($a[$this->sort]) ? $this->getValue($a[$this->sort], $this->main->getValueColumn()->columnName) : 0);
                    $bVal = (isset($b[$this->sort]) ? $this->getValue($b[$this->sort], $this->main->getValueColumn()->columnName) : 0);
                    if ($aVal == $bVal) {
                        return is_numeric($aHeader) && is_numeric($bHeader)
                            ? $aHeader <=> $bHeader
                            : $collator->compare($aHeader, $bHeader);
                    } else {
                        return $this->direction == ICollection::ASC
                            ? $aVal <=> $bVal
                            : $bVal <=> $aVal;
                    }
                }
            });
        }
		$this->template->rowTotalCollection = $this->rowTotalCollection;
		$this->template->columnTotalCollection = $this->columnTotalCollection;
		$this->template->totalCollection = $this->totalCollection;
		$this->template->view = $this->main->getView();
		$this->template->columnColumn = $this->main->getColumnColumn();
		$this->template->rowColumn = $this->main->getRowColumn();
		$this->template->valueColumn = $this->main->getValueColumn();
        $this->template->max = $max;
        $this->template->min = $min;
        $this->template->rowMax = $rowMax;
        $this->template->rowMin = $rowMin;
        $this->template->columnMax = $columnMax;
        $this->template->columnMin = $columnMin;
        $this->template->columnHeaderItems = $columnHeaderItems;
        $this->template->items = $items;
        $this->template->sort = $this->sort;
        $this->template->direction = $this->direction;
		$this->template->control = $this;
        $this->template->render($this->main->getView()->tableTemplate);
    }


    public function getValue(IEntity $entity, $columnName): ?array
    {
        $columnNames = explode('.', $columnName);
        $values = [$entity];
        foreach ($columnNames as $columnName) {
            $newValues = [];
            foreach ($values as $value) {
                if ($value instanceof HasMany) {
                    foreach ($value as $v) {
                        if (!isset($v->{$columnName})) {
                            return null;
                        }
                        $newValues[] = $v->{$columnName};
                    }
                } else {
                    if (!isset($value->{$columnName})) {
                        return null;
                    }
                    $newValues[] = $value->{$columnName};
                }
            }
            $values = $newValues;
        }
        return $values;
    }


    public function handleSort(?string $sort = null, ?string $direction = ICollection::ASC): void
    {
        $this->sort = $sort;
        $this->direction = $direction;
        if ($this->presenter->isAjax()) {
            $this->onSort($this);
            $this->redrawControl();
        }
    }
}
