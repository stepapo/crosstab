<?php

declare(strict_types=1);

namespace Stepapo\Crosstab\UI\Table;

use Nette\Application\Attributes\Persistent;
use Nextras\Orm\Collection\ICollection;
use Nextras\Orm\Entity\IEntity;
use Nextras\Orm\Relationships\HasMany;
use Stepapo\Crosstab\UI\CrosstabControl;


/**
 * @property-read TableTemplate $template
 */
class SimpleTable extends CrosstabControl implements Table
{
    /** @var callable[] */
    public array $onSort = [];

	#[Persistent]
    public ?string $sort = null;

	#[Persistent]
    public ?string $direction = null;


    public function render()
    {
        parent::render();
        $items = [];
        $columnHeaderItems = [];
        $min = null;
        $max = null;
        $rowMin = null;
        $rowMax = null;
        $columnMin = null;
        $columnMax = null;
        foreach ($this->getCollection() as $item) {
            $rowValue = $item->getRawValue($this->getRowColumn()->name);
            $columnValue = $item->getRawValue($this->getColumnColumn()->name);
            if (!isset($items[$rowValue])) {
                $items[$rowValue]['header'] = $item;
            }
            $items[$rowValue][$columnValue] = $item;
            if (!isset($columnHeaderItems[$columnValue])) {
                $columnHeaderItems[$columnValue] = $item;
            }
            $value = $this->getValue($item, $this->getValueColumn()->columnName) ? $this->getValue($item, $this->getValueColumn()->columnName)[0] : null;
            $type = isset($item->type);
            if ($value !== null && ($min === null || $value < $min)) {
                $min = $value;
            }
            if ($value !== null && ($max === null || $value > $max)) {
                $max = $value;
            }
        }
        if ($this->getRowTotalCollection()) {
            foreach ($this->getRowTotalCollection() as $item) {
                $items[$item->getRawValue($this->getRowColumn()->name)]['total'] = $item;
                $value = $this->getValue($item, $this->getValueColumn()->columnName) ? $this->getValue($item, $this->getValueColumn()->columnName)[0] : null;
                $type = isset($item->type);
                if ($value !== null && ($rowMin === null || $value < $rowMin)) {
                    $rowMin = $value;
                }
                if ($value !== null && ($rowMax === null || $value > $rowMax)) {
                    $rowMax = $value;
                }
            }
        }
        if ($this->getColumnTotalCollection()) {
            $columnTotals = [];
            foreach ($this->getColumnTotalCollection() as $item) {
                $columnTotals[$item->getRawValue($this->getColumnColumn()->name)] = $item;
                $value = $this->getValue($item, $this->getValueColumn()->columnName) ? $this->getValue($item, $this->getValueColumn()->columnName)[0] : null;
                $type = isset($item->type);
                if ($value !== null && ($columnMin === null || $value < $columnMin)) {
                    $columnMin = $value;
                }
                if ($value !== null && ($columnMax === null || $value > $columnMax)) {
                    $columnMax = $value;
                }
            }
            $this->template->columnTotals = $columnTotals;
        }
        if ($this->getTotalCollection()) {
            $this->template->total = $this->getTotalCollection()->limitBy(1)->fetch();
        }
        $collator = new \Collator('cs_CZ');
        uasort($columnHeaderItems, function($a, $b) use($collator) {
            $aHeader = $this->getValue($a, $this->getColumnColumn()->columnName)[0];
            $bHeader = $this->getValue($b, $this->getColumnColumn()->columnName)[0];
            return is_numeric($aHeader) && is_numeric($bHeader)
                ? $aHeader <=> $bHeader
                : $collator->compare($aHeader, $bHeader);
        });
        if ($this->sort) {
            uasort($items, function($a, $b) use ($collator) {
                $aHeader = $this->getValue($a['header'], $this->getRowColumn()->columnName)[0];
                $bHeader = $this->getValue($b['header'], $this->getRowColumn()->columnName)[0];
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
                    $aVal = (isset($a[$this->sort]) ? $this->getValue($a[$this->sort], $this->getValueColumn()->columnName) : 0);
                    $bVal = (isset($b[$this->sort]) ? $this->getValue($b[$this->sort], $this->getValueColumn()->columnName) : 0);
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
        $this->template->render($this->getView()->tableTemplate);
    }

    public function getValue(IEntity $entity, $columnName)
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

    public function handleSort(?string $sort = null, ?string $direction = ICollection::ASC)
    {
        $this->sort = $sort;
        $this->direction = $direction;
        if ($this->presenter->isAjax()) {
            $this->onSort($this);
            $this->redrawControl();
        }
    }
}
