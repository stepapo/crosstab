<?php

declare(strict_types=1);

namespace Stepapo\Crosstab;

use Nextras\Orm\Collection\ICollection;
use Stepapo\Data\OrmFunction;


class Sort
{
    public bool $isDefault;

    public string $direction;

    public ?OrmFunction $function;


    public function __construct(
        bool $isDefault = false,
        string $direction = ICollection::ASC,
        ?OrmFunction $function = null,
    ) {
        $this->isDefault = $isDefault;
        $this->direction = $direction;
        $this->function = $function;
    }


    public static function createFromArray(?array $config): Sort
    {
        return new self(
            $config['isDefault'] ?? false,
            $config['direction'] ?? ICollection::ASC,
            $config['function'] ?? null,
        );
    }
}
