<?php

declare(strict_types=1);

namespace Stepapo\Crosstab\UI;

use Stepapo\Crosstab\Column;
use Stepapo\Crosstab\UI\Crosstab\Crosstab;
use Nextras\Orm\Collection\ICollection;


abstract class CrosstabControl extends DataControl
{   
    public function render()
    {
        parent::render();
        $this->template->rowColumn = $this->getRowColumn();
        $this->template->columnColumn = $this->getColumnColumn();
        $this->template->valueColumn = $this->getValueColumn();
    }


    public function getMainComponent(): ?Crosstab
    {
        return $this->lookup(Crosstab::class, false);
    }


    public function getRowTotalCollection(): ?ICollection
    {
        return $this->getMainComponent()->getRowTotalCollection();
    }


    public function getColumnTotalCollection(): ?ICollection
    {
        return $this->getMainComponent()->getColumnTotalCollection();
    }


    public function getTotalCollection(): ?ICollection
    {
        return $this->getMainComponent()->getTotalCollection();
    }


    public function getRowColumn(): ?Column
    {
        return $this->getMainComponent()->getRowColumn();
    }


    public function getColumnColumn(): ?Column
    {
        return $this->getMainComponent()->getColumnColumn();
    }


    public function getValueColumn(): ?Column
    {
        return $this->getMainComponent()->getValueColumn();
    }
}
