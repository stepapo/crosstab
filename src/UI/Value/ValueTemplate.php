<?php

declare(strict_types=1);

namespace Stepapo\Crosstab\UI\Value;

use Stepapo\Crosstab\Column;
use Stepapo\Crosstab\UI\CrosstabControlTemplate;
use Nextras\Orm\Entity\IEntity;


class ValueTemplate extends CrosstabControlTemplate
{
    public IEntity $entity;

    /** @var mixed */
    public $value;

    public Column $column;
}
