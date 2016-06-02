<?php

namespace App\Presenters;

use Nette;


class MenuPresenter extends Nette\Application\UI\Presenter
{
    public function __construct(Nette\Database\Context $database)
    {
    }
    protected function startup()
    {
        parent::startup();

        $this->kategorieId = $this->getParam('kategorieId');
        cdd($this);
    }
}
