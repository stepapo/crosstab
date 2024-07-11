<?php

declare(strict_types=1);

namespace Stepapo\Crosstab\Control\Crosstab;

use Nette\Application\BadRequestException;
use Nextras\Orm\Collection\ICollection;
use Nextras\Orm\Model\IModel;
use Stepapo\Crosstab\Control\ColumnPicker\ColumnPickerControl;
use Stepapo\Crosstab\Control\RowPicker\RowPickerControl;
use Stepapo\Crosstab\Control\Table\TableControl;
use Stepapo\Crosstab\Control\ValuePicker\ValuePickerControl;
use Stepapo\Crosstab\Crosstab;
use Stepapo\Crosstab\CrosstabView;
use Stepapo\Data\Column;
use Stepapo\Data\Control\DataControl;
use Stepapo\Data\Control\FilterList\FilterListControl;
use Stepapo\Data\Control\MainComponent;
use Stepapo\Data\Option;


/**
 * @property-read CrosstabTemplate $template
 */
class CrosstabControl extends DataControl implements MainComponent
{
	private array $crossColumnNames;
	private ICollection $collection;
	private ICollection $rowCollection;
	private ICollection $columnCollection;
	private ICollection $totalCollection;


    public function __construct(
        private Crosstab $crosstab,
		private IModel $orm,
    ) {}


    public function render(): void
    {
		$this->template->rowColumn = $this->getRowColumn();
		$this->template->columnColumn = $this->getColumnColumn();
		$this->template->valueColumn = $this->getValueColumn();
        $this->filter()->sort();
        $this->template->render($this->getView()->crosstabTemplate);
    }


    public function getView(): CrosstabView
    {
        return $this->crosstab->view;
    }


    public function createComponentTable(): TableControl
    {
        $control = new TableControl(
			$this,
			$this->crosstab->columns,
			$this->getCollection(),
			$this->getRowCollection(),
			$this->getColumnCollection(),
			$this->getTotalCollection(),
		);
        $control->onSort[] = function (TableControl $table) {
            $this->redrawControl();
        };
        return $control;
    }


    public function createComponentFilterList(): FilterListControl
    {
		$visibleColumns = array_filter(
			$this->crosstab->columns,
			fn(Column $c) => $c->cross && !$c->hide && $c->name !== $this->getColumnColumn()->name && $c->name !== $this->getRowColumn()->name
		);
		$control = new FilterListControl($this, $this->crosstab->columns, $visibleColumns);
        $control->onFilter[] = function (FilterListControl $filterList) {
            $this->redrawControl();
        };
        return $control;
    }


    public function createComponentRowPicker(): RowPickerControl
    {
        $control = new RowPickerControl($this, $this->crosstab->columns, $this->crosstab->defaultRow);
        $control->onPick[] = function (RowPickerControl $rowPicker) {
            $this->getComponent('filterList')->getComponent('filter')->getComponent($rowPicker->row)->value = null;
            $this->redrawControl();
        };
        return $control;
    }


    public function createComponentColumnPicker(): ColumnPickerControl
    {
        $control = new ColumnPickerControl($this, $this->crosstab->columns, $this->crosstab->defaultColumn);
        $control->onPick[] = function (ColumnPickerControl $columnPicker) {
            $this->getComponent('filterList')->getComponent('filter')->getComponent($columnPicker->column)->value = null;
            $this->redrawControl();
        };
        return $control;
    }


    public function createComponentValuePicker(): ValuePickerControl
    {
        $control = new ValuePickerControl($this, $this->crosstab->columns, $this->crosstab->defaultValue, $this->crosstab->valueCollapse);
        $control->onPick[] = function (ValuePickerControl $valuePicker) {
            $this->redrawControl();
        };
        return $control;
    }


    private function filter(): self
    {
        foreach ($this->crosstab->columns as $column) {
            if (!$column->filter || $column->name === $this->getColumnColumn()->name || $column->name === $this->getRowColumn()->name) {
                continue;
            }
            $value = $this->getComponent('filterList')->getComponent('filter')->getComponent($column->name)->value;
            if (!$value) {
                continue;
            }
            if (!isset($column->filter->options[$value])) {
                $this->getComponent('filterList')->getComponent('filter')->getComponent($column->name)->value = null;
                continue;
            }
            if ($column->filter->options[$value] instanceof Option && $column->filter->options[$value]->condition) {
                $this->collection = $this->getCollection()->findBy($column->filter->options[$value]->condition);
				$this->columnCollection = $this->getColumnCollection()->findBy($column->filter->options[$value]->condition);
				$this->rowCollection = $this->getRowCollection()->findBy($column->filter->options[$value]->condition);
				$this->totalCollection = $this->getTotalCollection()->findBy($column->filter->options[$value]->condition);
            } else {
                $this->crosstab->collection = $this->getCollection()->findBy([$column->name => $value]);
				$this->columnCollection = $this->getColumnCollection()->findBy([$column->name => $value]);
				$this->rowCollection = $this->getRowCollection()->findBy([$column->name => $value]);
				$this->totalCollection = $this->getTotalCollection()->findBy([$column->name => $value]);
            }
        }
        return $this;
    }


    private function sort(): self
    {
        $this->collection = $this->getCollection()->orderBy($this->getRowColumn()->getNextrasName());
        return $this;
    }


    public function getRowColumn(): Column
    {
		$column = $this->getComponent('rowPicker')->row;
		if (!isset($this->crosstab->columns[$column])) {
			throw new BadRequestException;
		}
		return $this->crosstab->columns[$column];
    }


	public function getColumnColumn(): Column
    {
		$column = $this->getComponent('columnPicker')->column;
		if (!isset($this->crosstab->columns[$column])) {
			throw new BadRequestException;
		}
		return $this->crosstab->columns[$column];
    }


	public function getValueColumn(): Column
    {
		$column = $this->getComponent('valuePicker')->value;
		if (!isset($this->crosstab->columns[$column])) {
			throw new BadRequestException;
		}
		return $this->crosstab->columns[$column];
    }


	private function getCrossColumnNames(): array
	{
		if (!isset($this->crossColumnNames)) {
			$this->crossColumnNames = array_map(
				fn(Column $column) => $column->name,
				array_filter($this->crosstab->columns, fn(Column $column) => $column->cross)
			);
		}
		return $this->crossColumnNames;
	}


	public function getCollection(): ICollection
	{
		if (!isset($this->collection)) {
			$repositoryName = $this->crosstab->entityName;
			$row = $this->getComponent('rowPicker')->row ?? $this->crosstab->defaultRow;
			$column = $this->getComponent('columnPicker')->column ?? $this->crosstab->defaultColumn;
			$addBy = true;
			foreach ($this->getCrossColumnNames() as $columnName) {
				$c = $this->crosstab->columns[$columnName];
				if ($c->filter && $c->name !== $this->getColumnColumn()->name && $c->name !== $this->getRowColumn()->name) {
					$filterValue = $this->getComponent('filterList')->getComponent('filter')->getComponent($columnName)->value;
				} else {
					$filterValue = null;
				}
				if ($row === $columnName || $column === $columnName || $filterValue) {
					if ($addBy) {
						$repositoryName .= 'By';
						$addBy = false;
					}
					$repositoryName .= ucfirst($columnName);
				}
			}
			$repositoryName .= 'Repository';
			$this->collection = $this->orm->getRepositoryByName($repositoryName)->findAll();
		}
		return $this->collection;
	}


	public function getRowCollection(): ICollection
	{
		if (!isset($this->rowCollection)) {
			$repositoryName = $this->crosstab->entityName;
			$row = $this->getComponent('rowPicker')->row ?? $this->crosstab->defaultRow;
			$addBy = true;
			foreach ($this->getCrossColumnNames() as $columnName) {
				$c = $this->crosstab->columns[$columnName];
				if ($c->filter && $c->name !== $this->getColumnColumn()->name && $c->name !== $this->getRowColumn()->name) {
					$filterValue = $this->getComponent('filterList')->getComponent('filter')->getComponent($columnName)?->value;
				} else {
					$filterValue = null;
				}
				if ($row === $columnName || $filterValue) {
					if ($addBy) {
						$repositoryName .= 'By';
						$addBy = false;
					}
					$repositoryName .= ucfirst($columnName);
				}
			}
			$repositoryName .= 'Repository';
			$this->rowCollection = $this->orm->getRepositoryByName($repositoryName)->findAll();
		}
		return $this->rowCollection;
	}


	public function getColumnCollection(): ICollection
	{
		if (!isset($this->columnCollection)) {
			$repositoryName = $this->crosstab->entityName;
			$column = $this->getComponent('columnPicker')->column ?? $this->crosstab->defaultColumn;
			$addBy = true;
			foreach ($this->getCrossColumnNames() as $columnName) {
				$c = $this->crosstab->columns[$columnName];
				if ($c->filter && $c->name !== $this->getColumnColumn()->name && $c->name !== $this->getRowColumn()->name) {
					$filterValue = $this->getComponent('filterList')->getComponent('filter')->getComponent($columnName)?->value;
				} else {
					$filterValue = null;
				}
				if ($column === $columnName || $filterValue) {
					if ($addBy) {
						$repositoryName .= 'By';
						$addBy = false;
					}
					$repositoryName .= ucfirst($columnName);
				}
			}
			$repositoryName .= 'Repository';
			$this->columnCollection = $this->orm->getRepositoryByName($repositoryName)->findAll();
		}
		return $this->columnCollection;
	}


	public function getTotalCollection(): ICollection
	{
		if (!isset($this->totalCollection)) {
			$repositoryName = $this->crosstab->entityName;
			$addBy = true;
			foreach ($this->getCrossColumnNames() as $columnName) {
				$c = $this->crosstab->columns[$columnName];
				if ($c->filter && $c->name !== $this->getColumnColumn()->name && $c->name !== $this->getRowColumn()->name) {
					$filterValue = $this->getComponent('filterList')->getComponent('filter')->getComponent($columnName)?->value;
				} else {
					$filterValue = null;
				}
				if ($filterValue) {
					if ($addBy) {
						$repositoryName .= 'By';
						$addBy = false;
					}
					$repositoryName .= ucfirst($columnName);
				}
			}
			$repositoryName .= $repositoryName === $this->crosstab->entityName ? 'TotalRepository' : 'Repository';
			$this->totalCollection = $this->orm->getRepositoryByName($repositoryName)->findAll();
		}
		return $this->totalCollection;
	}
}
