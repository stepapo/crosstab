<?php

declare(strict_types=1);

namespace Stepapo\Crosstab\UI\Crosstab;

use Contributte\ImageStorage\ImageStorage;
use Nette\Localization\Translator;
use Stepapo\Crosstab\Column;
use Stepapo\Crosstab\CrosstabView;
use Stepapo\Crosstab\Option;
use Stepapo\Crosstab\UI\CrosstabControl;
use Stepapo\Crosstab\UI\CrosstabFactory;
use Stepapo\Crosstab\UI\Table\Table;
use Stepapo\Crosstab\UI\ColumnPicker\ColumnPicker;
use Stepapo\Crosstab\UI\FilterList\FilterList;
use Stepapo\Crosstab\UI\MainComponent;
use Stepapo\Crosstab\UI\RowPicker\RowPicker;
use Stepapo\Crosstab\UI\ValuePicker\ValuePicker;
use Nette\InvalidArgumentException;
use Nette\Neon\Neon;
use Nette\Utils\FileSystem;
use Nextras\Orm\Collection\ICollection;
use Stepapo\Utils\ConfigProcessor;


/**
 * @property-read CrosstabTemplate $template
 */
class Crosstab extends CrosstabControl implements MainComponent
{
    private ?CrosstabFactory $factory;

    private ?CrosstabView $view;

	private Column $rowColumn;

	private Column $columnColumn;

	private Column $valueColumn;

	private array $filter = [];


    /**
     * @param Column[]|null $columns
     */
    public function __construct(
        private ICollection $collection,
		private ?ICollection $rowTotalCollection = null,
		private ?ICollection $columnTotalCollection = null,
		private ?ICollection $totalCollection = null,
		private ?Translator $translator = null,
		private ?ImageStorage $imageStorage = null,
		private ?array $columns = null,
		?CrosstabView $view = null,
		?CrosstabFactory $factory = null,
		private ?int $valueCollapse = null
    ) {
        $this->view = $view ?: CrosstabView::createDefault();
        $this->factory = $factory ?: CrosstabFactory::createDefault();
    }
    
    
    public static function createFromNeon(string $file, array $params = []): Crosstab
    {
        $config = (array) Neon::decode(FileSystem::read($file));
        $parsedConfig = ConfigProcessor::replaceParams($config, $params);
        return self::createFromArray((array) $parsedConfig);
    }


    public static function createFromArray(array $config): Crosstab
    {
        if (!isset($config['collection'])) {
            throw new InvalidArgumentException('Crosstab collection has to be defined.');
        }
        $crosstab = new self($config['collection']);
        if (array_key_exists('rowTotalCollection', $config)) {
            $crosstab->setRowTotalCollection($config['rowTotalCollection']);
        }
        if (array_key_exists('columnTotalCollection', $config)) {
            $crosstab->setColumnTotalCollection($config['columnTotalCollection']);
        }
        if (array_key_exists('totalCollection', $config)) {
            $crosstab->setTotalCollection($config['totalCollection']);
        }
		if (array_key_exists('translator', $config)) {
			$crosstab->setTranslator($config['translator']);
		}
        if (array_key_exists('imageStorage', $config)) {
            $crosstab->setImageStorage($config['imageStorage']);
        }
        if (array_key_exists('factory', $config)) {
            $crosstab->setFactory(CrosstabFactory::createFromArray((array) $config['factory']));
        }
        if (array_key_exists('view', $config)) {
            $crosstab->setView(CrosstabView::createFromArray((array) $config['view']));
        }
        if (array_key_exists('columns', $config)) {
            foreach ((array) $config['columns'] as $columnName => $columnConfig) {
                $crosstab->addColumn(Column::createFromArray((array) $columnConfig, $columnName));
            }
        }
        if (array_key_exists('valueCollapse', $config)) {
            $crosstab->setValueCollapse($config['valueCollapse']);
        }
        return $crosstab;
    }


    public function loadState(array $params): void
    {
        parent::loadState($params);
        $this->rowColumn = $this->selectRowColumn();
        $this->columnColumn = $this->selectColumnColumn();
        $this->valueColumn = $this->selectValueColumn();
    }


    public function render()
    {
        parent::render();
        $this->filter()->sort();
        $this->template->render($this->view->crosstabTemplate);
    }



    public function getCollection(): ICollection
    {
        return $this->collection;
    }


    public function getRowTotalCollection(): ?ICollection
    {
        return $this->rowTotalCollection;
    }


    public function getColumnTotalCollection(): ?ICollection
    {
        return $this->columnTotalCollection;
    }


    public function getTotalCollection(): ?ICollection
    {
        return $this->totalCollection;
    }


	public function getTranslator(): ?Translator
	{
		return $this->translator;
	}


    public function getImageStorage(): ?ImageStorage
    {
        return $this->imageStorage;
    }


    /** @return Column[]|null */
    public function getColumns(): ?array
    {
        return $this->columns;
    }


    public function getFactory(): CrosstabFactory
    {
        return $this->factory;
    }


    public function getView(): CrosstabView
    {
        return $this->view;
    }


    public function getFilter(): array
    {
        return $this->filter;
    }


    public function getRowColumn(): Column
    {
        return $this->rowColumn;
    }


    public function getColumnColumn(): Column
    {
        return $this->columnColumn;
    }


    public function getValueColumn(): Column
    {
        return $this->valueColumn;
    }


    public function getValueCollapse(): ?int
    {
        return $this->valueCollapse;
    }


	public function setTranslator(Translator $translator): Crosstab
	{
		$this->translator = $translator;
		return $this;
	}


    public function setCollection(ICollection $collection): Crosstab
    {
        $this->collection = $collection;
        return $this;
    }


    public function setRowTotalCollection(?ICollection $rowTotalCollection): Crosstab
    {
        $this->rowTotalCollection = $rowTotalCollection;
        return $this;
    }


    public function setColumnTotalCollection(?ICollection $columnTotalCollection): Crosstab
    {
        $this->columnTotalCollection = $columnTotalCollection;
        return $this;
    }


    public function setTotalCollection(?ICollection $totalCollection): Crosstab
    {
        $this->totalCollection = $totalCollection;
        return $this;
    }


    public function setImageStorage(?ImageStorage $imageStorage): Crosstab
    {
        $this->imageStorage = $imageStorage;
        return $this;
    }


    public function addColumn(Column $column): Crosstab
    {
        $this->columns[$column->name] = $column;
        return $this;
    }


    public function setView(CrosstabView $view): Crosstab
    {
        $this->view = $view;
        return $this;
    }


    public function createAndSetDefaultView(?string $name = null, bool $isDefault = false): CrosstabView
    {
        $this->view = CrosstabView::createDefault();
        return $this->view;
    }


    public function setFactory(CrosstabFactory $factory): Crosstab
    {
        $this->factory = $factory;
        return $this;
    }


    public function setValueCollapse(?int $valueCollapse): Crosstab
    {
        $this->valueCollapse = $valueCollapse;
        return $this;
    }


    public function createComponentTable(): Table
    {
        $control = $this->getFactory()->createTable();
        $control->onSort[] = function (Table $table) {
            $this->redrawControl();
        };
        return $control;
    }


    public function createComponentFilterList(): FilterList
    {
        $control = $this->getFactory()->createFilterList();
        $control->onFilter[] = function (FilterList $filterList) {
            $this->redrawControl();
        };
        return $control;
    }


    public function createComponentRowPicker(): RowPicker
    {
        $control = $this->getFactory()->createRowPicker();
        $control->onPick[] = function (RowPicker $rowPicker) {
            $this->getComponent('filterList')->getComponent('filter')->getComponent($rowPicker->row)->value = null;
            $this->redrawControl();
        };
        return $control;
    }


    public function createComponentColumnPicker(): ColumnPicker
    {
        $control = $this->getFactory()->createColumnPicker();
        $control->onPick[] = function (ColumnPicker $columnPicker) {
            $this->getComponent('filterList')->getComponent('filter')->getComponent($columnPicker->column)->value = null;
            $this->redrawControl();
        };
        return $control;
    }


    public function createComponentValuePicker(): ValuePicker
    {
        $control = $this->getFactory()->createValuePicker();
        $control->onPick[] = function (ValuePicker $valuePicker) {
            $this->redrawControl();
        };
        return $control;
    }


    private function filter(): Crosstab
    {
        foreach ($this->columns as $column) {
            if (!$column->filter) {
                continue;
            }
            $value = $this->getComponent('filterList')->getComponent('filter')->getComponent($column->name)->value;
            if (!$value) {
                continue;
            }
            $this->filter[$column->name] = $value;
            if (!isset($column->filter->options[$value])) {
                $this->getComponent('filterList')->getComponent('filter')->getComponent($column->name)->value = null;
                continue;
            }
            if ($column->filter->options[$value] instanceof Option && $column->filter->options[$value]->condition) {
                $this->collection = $this->collection->findBy($column->filter->options[$value]->condition);
                if ($this->columnTotalCollection) {
                    $this->columnTotalCollection = $this->columnTotalCollection->findBy($column->filter->options[$value]->condition);
                }
                if ($this->rowTotalCollection) {
                    $this->rowTotalCollection = $this->rowTotalCollection->findBy($column->filter->options[$value]->condition);
                }
                if ($this->totalCollection) {
                    $this->totalCollection = $this->totalCollection->findBy($column->filter->options[$value]->condition);
                }
            } else {
                $this->collection = $this->collection->findBy([$column->name => $value]);
                if ($this->columnTotalCollection) {
                    $this->columnTotalCollection = $this->columnTotalCollection->findBy([$column->name => $value]);
                }
                if ($this->rowTotalCollection) {
                    $this->rowTotalCollection = $this->rowTotalCollection->findBy([$column->name => $value]);
                }
                if ($this->totalCollection) {
                    $this->totalCollection = $this->totalCollection->findBy([$column->name => $value]);
                }
            }
        }
        return $this;
    }


    private function sort(): Crosstab
    {
        $this->collection = $this->collection->orderBy($this->rowColumn->getNextrasName());

        return $this;
    }


    private function selectRowColumn()
    {
        if ($column = $this->getComponent('rowPicker')->row) {
            if (isset($this->columns[$column])) {
                return $this->columns[$column];
            }
        }

        return array_values($this->columns)[0];
    }


    private function selectColumnColumn()
    {
        if ($column = $this->getComponent('columnPicker')->column) {
            if (isset($this->columns[$column])) {
                return $this->columns[$column];
            }
        }

        return array_values($this->columns)[0];
    }


    private function selectValueColumn()
    {
        if ($column = $this->getComponent('valuePicker')->value) {
            if (isset($this->columns[$column])) {
                return $this->columns[$column];
            }
        }

        return array_values($this->columns)[0];
    }
}
