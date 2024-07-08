<?php

declare(strict_types=1);

namespace Stepapo\Crosstab\UI;

use Contributte\ImageStorage\ImageStorage;
use Nette\Application\UI\Control;
use Nette\Application\UI\Presenter;
use Nette\Bridges\ApplicationLatte\Template;
use Nette\Security\User;
use Stepapo\Crosstab\Column;
use Stepapo\Crosstab\View;


abstract class DataControlTemplate extends Template
{
    public Presenter $presenter;

    public Control $control;

    public User $user;

    public string $basePath;

    /** @var Column[] */
    public array $columns;

    public View $view;

    public ?ImageStorage $imageStorage;
}
