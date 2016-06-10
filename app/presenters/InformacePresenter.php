<?php

namespace App\Presenters;

use Nette;
use Tracy\Debugger;
use Nette\Mail\Message;
use Nette\Mail\SendmailMailer;


class DaryPresenter extends Nette\Application\UI\Presenter
{
    /** @var Nette\Database\Context */
    private $database;

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }

    public function renderDefault()
    {
        $this->template->absPath = $this->context->parameters['wwwDir'];
        $this->template->gifts = $this->database->table('gifts')
            ->order('id DESC');
    }
}
