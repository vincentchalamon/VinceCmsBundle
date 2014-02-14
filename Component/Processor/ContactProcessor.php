<?php

/*
 * This file is part of the VinceCms bundle.
 *
 * (c) Vincent Chalamon <vincentchalamon@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vince\Bundle\CmsBundle\Component\Processor;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Vince\Bundle\CmsBundle\Form\Type\ContactType;
use Symfony\Component\HttpFoundation\Request;

/**
 * Process Contact form
 *
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
class ContactProcessor extends Processor
{

    /**
     * Mailer
     *
     * @var \Swift_Mailer
     */
    protected $mailer;

    /**
     * Templating
     *
     * @var EngineInterface
     */
    protected $templating;

    /**
     * Configuration
     *
     * @var array
     */
    protected $configuration;

    /**
     * Translator
     *
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * Session
     *
     * @var Session
     */
    protected $session;

    /**
     * {@inheritdoc}
     */
    public function process(Request $request)
    {
        $form = $this->createForm(new ContactType());
        $form->submit($request);
        if ($form->isValid()) {
            $message = \Swift_Message::newInstance()
                        ->setSubject($this->translator->trans('contact.subject', array(), 'VinceCms'))
                        ->setFrom($this->configuration['noreply'])
                        ->setReplyTo($form->get('email')->getData(), $form->get('name')->getData())
                        ->setTo($this->configuration['recipient'])
                        ->setBody($this->templating->render('VinceCmsBundle::mail.html.twig', array(
                            'message' => $form->get('message')->getData(),
                            'title' => $this->translator->trans('contact.subject', array(), 'VinceCms')
                        )
                    )
                )
            ;
            $this->mailer->send($message);
            $this->session->getFlashBag()->add('success', $this->translator->trans('contact.confirmation', array(), 'VinceCms'));

            return true;
        }

        return $form;
    }

    /**
     * Set Session
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     *
     * @param Session $session
     */
    public function setSession(Session $session)
    {
        $this->session = $session;
    }

    /**
     * Set Mailer
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     *
     * @param \Swift_Mailer $mailer
     */
    public function setMailer(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Set Templating
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     *
     * @param EngineInterface $templating
     */
    public function setTemplating(EngineInterface $templating)
    {
        $this->templating = $templating;
    }

    /**
     * Set Configuration
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     *
     * @param array $configuration
     */
    public function setConfiguration(array $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Set Translator
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     *
     * @param TranslatorInterface $translator
     */
    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }
}