<?php

declare(strict_types=1);

namespace Stepapo\Crosstab\Control\ValuePicker;

use Nette\Application\Attributes\Persistent;
use Stepapo\Crosstab\Control\Crosstab\CrosstabControl;
use Stepapo\Data\Control\DataControl;


/**
 * @property-read ValuePickerTemplate $template
 * @method onPick(ValuePickerControl $control)
 */
class ValuePickerControl extends DataControl
{
	#[Persistent] public ?string $value = null;
    public array $onPick = [];


	public function __construct(
		private CrosstabControl $main,
		private array $columns,
		private string $defaultValue,
		private ?int $valueCollapse,
	) {}


	public function loadState(array $params): void
	{
		parent::loadState($params);
		$this->value = $this->value ?: $this->defaultValue;
	}


	public function render(): void
    {
        $this->template->value = $this->value;
		$this->template->columns = $this->columns;
        $this->template->valueCollapse = $this->valueCollapse;
        $this->template->render($this->main->getView()->valuePickerTemplate);
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
