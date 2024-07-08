<?php

declare(strict_types=1);

namespace Stepapo\Crosstab\UI\FilterList;

use Nette\Application\Attributes\Persistent;
use Nette\Application\UI\Multiplier;
use Stepapo\Crosstab\UI\CrosstabControl;
use Stepapo\Crosstab\UI\Filter\Filter;


/**
 * @property-read FilterListTemplate $template
 * @method onFilter(SimpleFilterList $control)
 */
class SimpleFilterList extends CrosstabControl implements FilterList
{
	#[Persistent]
    public ?string $value = null;

    public array $onFilter = [];


    public function render()
    {
        parent::render();
        $this->template->render($this->getView()->filterListTemplate);
    }


    public function createComponentFilter()
    {
        return new Multiplier(function ($name): Filter {
            $control = $this->getFactory()->createFilter(
                $this->getColumns()[$name],
            );
            $control->onFilter[] = function (Filter $filter) {
                $this->onFilter($this);
                $this->redrawControl();
            };
            return $control;
        });
    }
}
