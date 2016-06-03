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

    protected function createComponentDaryForm()
    {
        $form = new Nette\Application\UI\Form();

        $form->addHidden('reserved');

        $form->addText('reserved_property')
            ->setType('email')
            ->addRule($form::EMAIL, 'Zadejte prosím svůj e-mail ve správaném formátu.')
            ->setRequired('Zadejte prosím svůj e-mail.');
        $form->addSubmit('send', 'Rezervovat')
            ->setAttribute('class', 'button call input');
        $form->onSuccess[] = [$this, 'daryFormSucceeded'];


        return $form;
    }

    public function daryFormSucceeded($form, $values)
    {
        $id = $this->getParameter('id');

        $post = $this->database->table('gifts')->get($id);
        $post->update($values);

        $this->sendMail($id, $values->reserved_property);

        $this->flashMessage('Dar byl rezervován. Informace budete mít brzy v e-mailové schránce.', 'success');
        $this->redirect('Dary:default');
    }

    public function actionEdit($id)
    {
        $this['daryForm']['reserved']->setValue(1);
        $post = $this->database->table('gifts')->get($id);
        if (!empty($this->database->table('gifts')->get($id)->name3p)) {
            $this->template->giftName = $this->database->table('gifts')->get($id)->name3p;
        } else {
            $this->template->giftName = $this->database->table('gifts')->get($id)->name;
        }
        $reserved = $this->database->table('gifts')->get($id)->reserved;
        if (!$post) {
            $this->error('Dar nebyl nalezen');
        } elseif ($reserved == 1) {
            $this->flashMessage('Dar si již někdo rezervoval.', 'error');
            $this->redirect('Dary:default');
        }
        $this['daryForm']->setDefaults($post->toArray());
    }

    public function sendMail($id, $target)
    {
        $item = $this->database->table('gifts')->get($id);

        if (!empty($item->name3p)) {
            $giftName = $item->name3p;
        } else {
            $giftName = $item->name;
        }

        $body = "Ahoj,<br>jsme rádi, že jste se rozhodli nám koupit " . $giftName . ".<br>";
        if (!empty($item->description)) {
            $body .= "(" . $item->description . ")<br><br>";
        }
        if (!empty($item->tips)) {
            $body .= $giftName . " můžete koupit třeba <a href='" . $item->tips . "'>zde</a>.<br><br>";
        }
        $body .= 'Hlavně si ho, až nastane náš velký den, nezapoměňte doma.<br><br>Tak na svatbě naviděnou.';

        $mail = new Message;
        $mail->setFrom('vancova.martina@seznam.cz')
            ->addTo($target)
            ->setSubject('Dar pro Martinku a Lukáše')
            ->setHtmlBody($body);

        $mailer = new SendmailMailer;
        $mailer->send($mail);
    }
}
