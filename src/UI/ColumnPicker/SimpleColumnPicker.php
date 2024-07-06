<?php

declare(strict_types=1);

namespace Stepapo\Crosstab\UI\ColumnPicker;

use Nette\Application\Attributes\Persistent;
use Stepapo\Crosstab\UI\CrosstabControl;


/**
 * @property-read ColumnPickerTemplate $template
 * @method onPick(SimpleColumnPicker $control)
 */
class SimpleColumnPicker extends CrosstabControl implements ColumnPicker
{
	#[Persistent]
    public ?string $column = null;

    public array $onPick = [];


    public function render()
    {
        parent::render();
        $this->template->column = $this->column;
        $this->template->render($this->getView()->columnPickerTemplate);
    }


    public function handlePick(?string $column = null): void
    {
        $this->column = $column;
        if ($this->presenter->isAjax()) {
            $this->onPick($this);
            $this->redrawControl();
        }
    }
}
