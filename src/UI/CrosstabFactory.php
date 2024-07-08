<?php

declare(strict_types=1);

namespace Stepapo\Crosstab\UI;

use Stepapo\Crosstab\Column;
use Stepapo\Crosstab\Factory;
use Stepapo\Crosstab\UI\ColumnPicker\ColumnPicker;
use Stepapo\Crosstab\UI\ColumnPicker\SimpleColumnPicker;
use Stepapo\Crosstab\UI\Filter\Filter;
use Stepapo\Crosstab\UI\Filter\SimpleFilter;
use Stepapo\Crosstab\UI\FilterList\FilterList;
use Stepapo\Crosstab\UI\FilterList\SimpleFilterList;
use Stepapo\Crosstab\UI\RowPicker\RowPicker;
use Stepapo\Crosstab\UI\RowPicker\SimpleRowPicker;
use Stepapo\Crosstab\UI\Table\SimpleTable;
use Stepapo\Crosstab\UI\Table\Table;
use Stepapo\Crosstab\UI\ValuePicker\SimpleValuePicker;
use Stepapo\Crosstab\UI\ValuePicker\ValuePicker;


class CrosstabFactory implements Factory
{
    public function __construct(
		public string $tableClass = SimpleTable::class,
		public string $filterListClass = SimpleFilterList::class,
		public string $filterClass = SimpleFilter::class,
		public string $rowPickerClass = SimpleRowPicker::class,
		public string $columnPickerClass = SimpleColumnPicker::class,
		public string $valuePickerClass = SimpleValuePicker::class
    ) {}


    public static function createFromArray(array $config): CrosstabFactory
    {
        $factory = new self();
        if (isset($config['tableClass'])) {
            $factory->setTableClass($config['tableClass']);
        }
        if (isset($config['filterListClass'])) {
            $factory->setFilterListClass($config['filterListClass']);
        }
        if (isset($config['filterClass'])) {
            $factory->setFilterClass($config['filterClass']);
        }
        if (isset($config['rowPickerClass'])) {
            $factory->setRowPickerClass($config['rowPickerClass']);
        }
        if (isset($config['columnPickerClass'])) {
            $factory->setColumnPickerClass($config['columnPickerClass']);
        }
        if (isset($config['valuePickerClass'])) {
            $factory->setValuePickerClass($config['valuePickerClass']);
        }
        return $factory;
    }


    public static function createDefault(): CrosstabFactory
    {
        return new self();
    }


    public function createTable(): Table
    {
        return new $this->tableClass();
    }


    public function createFilterList(): FilterList
    {
        return new $this->filterListClass();
    }


    public function createFilter(Column $column): Filter
    {
        return new $this->filterClass($column);
    }


    public function createRowPicker(): RowPicker
    {
        return new $this->rowPickerClass();
    }


    public function createColumnPicker(): ColumnPicker
    {
        return new $this->columnPickerClass();
    }


    public function createValuePicker(): ValuePicker
    {
        return new $this->valuePickerClass();
    }


    public function setTableClass(string $tableClass): CrosstabFactory
    {
        $this->tableClass = $tableClass;
        return $this;
    }


    public function setFilterListClass(string $filterListClass): CrosstabFactory
    {
        $this->filterListClass = $filterListClass;
        return $this;
    }


    public function setFilterClass(string $filterClass): CrosstabFactory
    {
        $this->filterClass = $filterClass;
        return $this;
    }


    public function setRowPickerClass(string $rowPickerClass): CrosstabFactory
    {
        $this->rowPickerClass = $rowPickerClass;
        return $this;
    }


    public function setColumnPickerClass(string $columnPickerClass): CrosstabFactory
    {
        $this->columnPickerClass = $columnPickerClass;
        return $this;
    }


    public function setValuePickerClass(string $valuePickerClass): CrosstabFactory
    {
        $this->valuePickerClass = $valuePickerClass;
        return $this;
    }
}
