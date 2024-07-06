<?php

declare(strict_types=1);

namespace Stepapo\Crosstab\UI\Filter;


/**
 * @method onFilter(Filter $control)
 */
interface Filter
{
    public function handleFilter($value = null): void;
}
