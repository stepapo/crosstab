<?php

declare(strict_types=1);

namespace Stepapo\Crosstab\UI\Filter;

use Nette\Application\Attributes\Persistent;
use Stepapo\Crosstab\Column;
use Stepapo\Crosstab\UI\CrosstabControl;


/**
 * @property-read FilterTemplate $template
 * @method onFilter(SimpleFilter $control)
 */
class SimpleFilter extends CrosstabControl implements Filter
{
	#[Persistent]
    public ?string $value = null;

    public array $onFilter = [];


    public function __construct(
        private Column $column
    ) {}


    public function render()
    {
        parent::render();
        $this->template->column = $this->column;
        $this->template->value = $this->value;
        $this->template->render($this->getView()->filterTemplate);
    }


    public function handleFilter($value = null): void
    {
        $this->value = $value;
        if ($this->presenter->isAjax()) {
            $this->onFilter($this);
            $this->redrawControl();
        }
    }
}
