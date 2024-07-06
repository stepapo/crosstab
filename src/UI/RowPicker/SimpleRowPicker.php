<?php

declare(strict_types=1);

namespace Stepapo\Crosstab\UI\RowPicker;

use Nette\Application\Attributes\Persistent;
use Stepapo\Crosstab\UI\CrosstabControl;


/**
 * @property-read RowPickerTemplate $template
 * @method onPick(SimpleRowPicker $control)
 */
class SimpleRowPicker extends CrosstabControl implements RowPicker
{
	#[Persistent]
    public ?string $row = null;

    public array $onPick = [];


    public function render()
    {
        parent::render();
        $this->template->row = $this->row;
        $this->template->render($this->getView()->rowPickerTemplate);
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
