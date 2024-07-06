<?php

declare(strict_types=1);

namespace Stepapo\Crosstab\UI;

use Contributte\ImageStorage\ImageStorage;
use Nette\Localization\Translator;
use Stepapo\Crosstab\Column;
use Stepapo\Crosstab\Factory;
use Stepapo\Crosstab\View;
use Nextras\Orm\Collection\ICollection;


interface MainComponent
{
    function getCollection(): ICollection;

	function getTranslator(): ?Translator;

    function getImageStorage(): ?ImageStorage;

    /** @return Column[]|null */
    function getColumns(): ?array;

    function getView(): View;

    function getFactory(): Factory;

    function getFilter(): array;
}
