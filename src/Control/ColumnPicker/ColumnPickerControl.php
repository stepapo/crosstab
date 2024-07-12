<?php

declare(strict_types=1);

namespace Stepapo\Crosstab\Control\ColumnPicker;

use Nette\Application\Attributes\Persistent;
use Nette\Application\BadRequestException;
use Stepapo\Crosstab\Control\Crosstab\CrosstabControl;
use Stepapo\Data\Control\DataControl;


/**
 * @property-read ColumnPickerTemplate $template
 * @method onPick(ColumnPickerControl $control)
 */
class ColumnPickerControl extends DataControl
{
	#[Persistent] public ?string $column = null;
    public array $onPick = [];


	public function __construct(
		private CrosstabControl $main,
		private array $columns,
		private string $defaultColumn,
	) {}


	public function loadState(array $params): void
	{
		parent::loadState($params);
		$this->column = $this->column ?: $this->defaultColumn;
	}


    public function render(): void
    {
        $this->template->column = $this->column;
		$this->template->columns = $this->columns;
		$this->template->rowColumn = $this->main->getRowColumn();
        $this->template->render($this->main->getView()->columnPickerTemplate);
    }


    public function handlePick(?string $column = null): void
    {
        $this->column = $column;
		if (!isset($this->columns[$column]) || $this->columns[$column]->hide) {
			throw new BadRequestException;
		}
        if ($this->presenter->isAjax()) {
            $this->onPick($this);
            $this->redrawControl();
        }
    }
}
