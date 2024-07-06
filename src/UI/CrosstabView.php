<?php

declare(strict_types=1);

namespace Stepapo\Crosstab;


class CrosstabView implements View
{
    public const DEFAULT_VIEW = [
        'crosstabTemplate' => __DIR__ . '/Crosstab/crosstab.latte',
        'tableTemplate' => __DIR__ . '/Table/table.latte',
        'filterListTemplate' => __DIR__ . '/FilterList/list.latte',
        'filterTemplate' => __DIR__ . '/Filter/list.latte',
        'rowPickerTemplate' => __DIR__ . '/RowPicker/rowPicker.latte',
        'columnPickerTemplate' => __DIR__ . '/ColumnPicker/columnPicker.latte',
        'valuePickerTemplate' => __DIR__ . '/ValuePicker/valuePicker.latte',
        'valueTemplate' => __DIR__ . '/Value/value.latte'
    ];


    public function __construct(
		public string $crosstabTemplate = self::DEFAULT_VIEW['crosstabTemplate'],
		public string $tableTemplate = self::DEFAULT_VIEW['tableTemplate'],
		public string $filterListTemplate = self::DEFAULT_VIEW['filterListTemplate'],
		public string $filterTemplate = self::DEFAULT_VIEW['filterTemplate'],
		public string $rowPickerTemplate = self::DEFAULT_VIEW['rowPickerTemplate'],
		public string $columnPickerTemplate = self::DEFAULT_VIEW['columnPickerTemplate'],
		public string $valuePickerTemplate = self::DEFAULT_VIEW['valuePickerTemplate'],
		public string $valueTemplate = self::DEFAULT_VIEW['valueTemplate']
    ) {}
    
    
    public static function createFromArray(?array $config = null): CrosstabView
    {
        $view = new self();
        if (isset($config['crosstabTemplate'])) {
            $view->setCrosstabTemplate($config['crosstabTemplate']);
        }
        if (isset($config['tableTemplate'])) {
            $view->setTableTemplate($config['tableTemplate']);
        }
        if (isset($config['filterListTemplate'])) {
            $view->setFilterListTemplate($config['filterListTemplate']);
        }
        if (isset($config['filterTemplate'])) {
            $view->setFilterTemplate($config['filterTemplate']);
        }
        if (isset($config['rowPicker'])) {
            $view->setRowPickerTemplate($config['rowPicker']);
        }
        if (isset($config['columnPicker'])) {
            $view->setColumnPickerTemplate($config['columnPicker']);
        }
        if (isset($config['valuePicker'])) {
            $view->setValuePickerTemplate($config['valuePicker']);
        }
        if (isset($config['value'])) {
            $view->setValueTemplate($config['value']);
        }
        return $view;
    }


    public function setCrosstabTemplate(string $crosstabTemplate): CrosstabView
    {
        $this->crosstabTemplate = $crosstabTemplate;
        return $this;
    }


    public function setTableTemplate(string $tableTemplate): CrosstabView
    {
        $this->tableTemplate = $tableTemplate;
        return $this;
    }


    public function setFilterListTemplate(string $filterListTemplate): CrosstabView
    {
        $this->filterListTemplate = $filterListTemplate;
        return $this;
    }


    public function setFilterTemplate(string $filterTemplate): CrosstabView
    {
        $this->filterTemplate = $filterTemplate;
        return $this;
    }


    public function setRowPickerTemplate(string $rowPickerTemplate): CrosstabView
    {
        $this->rowPickerTemplate = $rowPickerTemplate;
        return $this;
    }


    public function setColumnPickerTemplate(string $columnPickerTemplate): CrosstabView
    {
        $this->columnPickerTemplate = $columnPickerTemplate;
        return $this;
    }


    public function setValuePickerTemplate(string $valuePickerTemplate): CrosstabView
    {
        $this->valuePickerTemplate = $valuePickerTemplate;
        return $this;
    }


    public function setValueTemplate(string $valueTemplate): CrosstabView
    {
        $this->valueTemplate = $valueTemplate;
        return $this;
    }


    public static function createDefault(): CrosstabView
    {
        return new self();
    }
}
