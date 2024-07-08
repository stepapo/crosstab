<?php

declare(strict_types=1);

namespace Stepapo\Crosstab;


class Column
{
    public const ALIGN_LEFT = 'left';
    
    public const ALIGN_CENTER = 'center';
    
    public const ALIGN_RIGHT = 'right';


    public function __construct(
		public string $name,
		public ?string $label = null,
		public ?string $description = null,
		public ?int $width = null,
		public string $align = self::ALIGN_LEFT,
		public ?string $columnName = null,
		public ?LatteFilter $latteFilter = null,
		public ?string $prepend = null,
		public ?string $append = null,
		public ?Link $link = null,
		public ?string $valueTemplateFile = null,
		public ?Sort $sort = null,
		public ?Filter $filter = null,
		public bool $hide = false,
		public bool $cross = false,
		public bool $aggregation = false,
		public ?Chart $chart = null,
		public ?string $class = null,
		public ?float $multiply = null,
    ) {
        $this->columnName = $columnName ?: $name;
    }
    
    
    public static function createFromArray(array $config, string $name): Column
    {
        $column = new self($name);
        if (array_key_exists('label', $config)) {
            $column->setLabel($config['label']);
        }
        if (array_key_exists('description', $config)) {
            $column->setDescription($config['description']);
        }
        if (array_key_exists('width', $config)) {
            $column->setWidth($config['width']);
        }
        if (array_key_exists('align', $config)) {
            $column->setAlign($config['align']);
        }
        if (array_key_exists('columnName', $config)) {
            $column->setColumnName($config['columnName']);
        }
        if (array_key_exists('latteFilter', $config)) {
            $column->setLatteFilter(LatteFilter::createFromArray((array) $config['latteFilter']));
        }
        if (array_key_exists('prepend', $config)) {
            $column->setPrepend($config['prepend']);
        }
        if (array_key_exists('append', $config)) {
            $column->setAppend($config['append']);
        }
        if (array_key_exists('link', $config)) {
            $column->setLink(Link::createFromArray((array) $config['link']));
        }
        if (array_key_exists('valueTemplateFile', $config)) {
            $column->setValueTemplateFile($config['valueTemplateFile']);
        }
		if (array_key_exists('sort', $config)) {
			$column->setSort(Sort::createFromArray((array) $config['sort']));
		}
        if (array_key_exists('filter', $config)) {
            $column->setFilter(Filter::createFromArray((array) $config['filter']));
        }
        if (array_key_exists('hide', $config)) {
            $column->setHide($config['hide']);
        }
        if (array_key_exists('cross', $config)) {
            $column->setCross($config['cross']);
        }
        if (array_key_exists('aggregation', $config)) {
            $column->setAggregation($config['aggregation']);
        }
        if (array_key_exists('chart', $config)) {
            $column->setChart(Chart::createFromArray((array) $config['chart']));
        }
        if (array_key_exists('class', $config)) {
            $column->setClass($config['class']);
        }
		if (array_key_exists('multiply', $config)) {
			$column->setMultiply($config['multiply']);
		}
        return $column;
    }


    public function setName(string $name): Column
    {
        $this->name = $name;
        return $this;
    }


    public function setLabel(?string $label): Column
    {
        $this->label = $label;
        return $this;
    }


    public function setDescription(?string $description): Column
    {
        $this->description = $description;
        return $this;
    }


    public function setWidth(?int $width): Column
    {
        $this->width = $width;
        return $this;
    }


    public function setAlign(string $align): Column
    {
        $this->align = $align;
        return $this;
    }


    public function setColumnName(string $columnName): Column
    {
        $this->columnName = $columnName;
        return $this;
    }


    public function setLatteFilter(LatteFilter $latteFilter): Column
    {
        $this->latteFilter = $latteFilter;
        return $this;
    }


    public function setPrepend(?string $prepend): Column
    {
        $this->prepend = $prepend;
        return $this;
    }


    public function setAppend(?string $append): Column
    {
        $this->append = $append;
        return $this;
    }


    public function setLink(Link $link): Column
    {
        $this->link = $link;
        return $this;
    }


    public function setValueTemplateFile(?string $valueTemplateFile): Column
    {
        $this->valueTemplateFile = $valueTemplateFile;
        return $this;
    }


	public function setSort(Sort $sort): Column
	{
		$this->sort = $sort;
		return $this;
	}


    public function setFilter(Filter $filter): Column
    {
        $this->filter = $filter;
        return $this;
    }


    public function setHide(bool $hide): Column
    {
        $this->hide = $hide;
        return $this;
    }


    public function setCross(bool $cross): Column
    {
        $this->cross = $cross;
        return $this;
    }


    public function setAggregation(bool $aggregation): Column
    {
        $this->aggregation = $aggregation;
        return $this;
    }


    public function setChart(Chart $chart): Column
    {
        $this->chart = $chart;
        return $this;
    }


    public function setClass(?string $class): Column
    {
        $this->class = $class;
        return $this;
    }


	public function setMultiply(?float $multiply): Column
	{
		$this->multiply = $multiply;
		return $this;
	}


    public function getNextrasName()
    {
        if (str_contains($this->columnName, '.')) {
            return str_replace('.', '->', $this->columnName);
        }

        if (str_contains($this->columnName, '_')) {
            return str_replace('_', '->', $this->columnName);
        }

        return $this->columnName;
    }


    public function getLatteName()
    {
        if (str_contains($this->columnName, '.')) {
            return str_replace('.', '_', $this->columnName);
        }

        return $this->columnName;
    }
}
