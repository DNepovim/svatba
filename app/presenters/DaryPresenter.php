<?php

namespace App\Presenters;

use Nette;
use Tracy\Debugger;


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

    protected function createComponentDaryForm()
    {
        $form = new Nette\Application\UI\Form();
        $form->addText('id', 'ID:');

        $form->addHidden('reserved');

        $form->addText('reserved_property', 'Váš e-mail:')
            ->setType('email')
            ->addRule($form::EMAIL, 'Zadejte prosím svůj e-mail ve správaném formátu.')
            ->setRequired('Zadejte prosím svůj e-mail.');
        $form->addSubmit('send', 'Rezervovat');
        $form->onSuccess[] = [$this, 'daryFormSucceeded'];

        return $form;
    }

    public function daryFormSucceeded($form, $values)
    {
        $id = $this->getParameter('id');

        $post = $this->database->table('gifts')->get($id);
        $post->update($values);

        $this->flashMessage('Dar byl rezervován.', 'success');
        if (!$this->isAjax()) {
            $this->redirect('this');
        }
    }

    public function actionEdit($id)
    {
        $this['daryForm']['reserved']->setValue(1);
        $post = $this->database->table('gifts')->get($id);
        if (!$post) {
            $this->error('Příspěvek nebyl nalezen');
        }
        $this['daryForm']->setDefaults($post->toArray());
    }
}
