<?php

declare(strict_types=1);

namespace Stepapo\Crosstab\Control\RowPicker;

use Nette\Application\Attributes\Persistent;
use Stepapo\Crosstab\Control\Crosstab\CrosstabControl;
use Stepapo\Data\Control\DataControl;


/**
 * @property-read RowPickerTemplate $template
 * @method onPick(RowPickerControl $control)
 */
class RowPickerControl extends DataControl
{
	#[Persistent] public ?string $row = null;
    public array $onPick = [];


	public function __construct(
		private CrosstabControl $main,
		private array $columns,
		private string $defaultRow,
	) {}


	public function loadState(array $params): void
	{
		parent::loadState($params);
		$this->row = $this->row ?: $this->defaultRow;
	}


	public function render()
    {
        $this->template->row = $this->row;
		$this->template->columns = $this->columns;
		$this->template->columnColumn = $this->main->getColumnColumn();
        $this->template->render($this->main->getView()->rowPickerTemplate);
    }


    public function handlePick(?string $row = null): void
    {
        $this->row = $row;
        if ($this->presenter->isAjax()) {
            $this->onPick($this);
            $this->redrawControl();
        }
    }
}
