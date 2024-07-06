<?php

declare(strict_types=1);

namespace Stepapo\Crosstab\UI\ValuePicker;

use Nette\Application\Attributes\Persistent;
use Stepapo\Crosstab\UI\CrosstabControl;


/**
 * @property-read ValuePickerTemplate $template
 * @method onPick(SimpleValuePicker $control)
 */
class SimpleValuePicker extends CrosstabControl implements ValuePicker
{
	#[Persistent]
    public ?string $value = null;

    public array $onPick = [];


    public function render()
    {
        parent::render();
        $this->template->value = $this->value;
        $this->template->valueCollapse = $this->getMainComponent()->getValueCollapse();
        $this->template->render($this->getView()->valuePickerTemplate);
    }


    public function handlePick(?string $value = null): void
    {
        $this->value = $value;
        if ($this->presenter->isAjax()) {
            $this->onPick($this);
            $this->redrawControl();
        }
    }
}
