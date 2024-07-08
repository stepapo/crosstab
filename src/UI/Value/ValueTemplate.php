<?php

declare(strict_types=1);

namespace Stepapo\Crosstab\UI\Value;

use Nextras\Orm\Entity\IEntity;
use Stepapo\Crosstab\Column;
use Stepapo\Crosstab\UI\CrosstabControlTemplate;


class ValueTemplate extends CrosstabControlTemplate
{
    public IEntity $entity;

    /** @var mixed */
    public $value;

    public Column $column;
}
