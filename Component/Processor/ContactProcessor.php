<?php

/*
 * This file is part of the VinceCmsBundle.
 *
 * (c) Vincent Chalamon <vincentchalamon@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vince\Bundle\CmsBundle\Component\Processor;

use Symfony\Component\Translation\TranslatorInterface;
use Vince\Bundle\CmsBundle\Form\Type\ContactType;
use Symfony\Component\HttpFoundation\Request;

/**
 * Description of ContactProcessor.php
 *
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
class ContactProcessor extends Processor
{

    protected $mailer, $templating, $configuration, $translator, $session;

    public function process(Request $request)
    {
        $form = $this->createForm(new ContactType());
        $form->submit($request);
        if ($form->isValid()) {
            $message = \Swift_Message::newInstance()
                ->setSubject($this->translator->trans('Nouveau message de contact'))
                ->setFrom($this->configuration['noreply'])
                ->setReplyTo($form->get('email')->getData(), $form->get('name')->getData())
                ->setTo($this->configuration['recipient'])
                ->setBody($this->templating->render('VinceCmsBundle::mail.html.twig', array(
                    'message' => $form->get('message')->getData(),
                    'title' => $this->translator->trans('Nouveau message de contact')
                )))
            ;
            $this->mailer->send($message);
            $this->session->getFlashBag()->add('success', $this->translator->trans('message.form.success', array(), 'cms'));

            return true;
        }

        return $form;
    }

    public function setSession($session)
    {
        $this->session = $session;
    }

    public function setMailer($mailer)
    {
        $this->mailer = $mailer;
    }

    public function setTemplating($templating)
    {
        $this->templating = $templating;
    }

    public function setConfiguration($configuration)
    {
        $this->configuration = $configuration;
    }

    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }
}