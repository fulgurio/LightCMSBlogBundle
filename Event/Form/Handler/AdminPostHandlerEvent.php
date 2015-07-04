<?php
/*
 * This file is part of the LightCMSBlogBundle package.
 *
 * (c) Fulgurio <http://fulgurio.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fulgurio\LightCMSBlogBundle\Event\Form\Handler;

use Fulgurio\LightCMSBundle\Entity\Page;
use Fulgurio\LightCMSBlogBundle\Form\Handler\AdminPostHandler;
use Symfony\Component\EventDispatcher\Event;

class AdminPostHandlerEvent extends Event
{
    private $formHandler;

    private $page;

    /**
     * Constructor
     *
     * @param Fulgurio\LightCMSBundle\Entity\Page $page
     * @param Fulgurio\LightCMSBlogBundle\Form\Handler\AdminPostHandler $formHandler
     */
    public function __construct(Page $page, AdminPostHandler $formHandler)
    {
        $this->page = $page;
        $this->formHandler = $formHandler;
    }

    /**
     * $page getter
     *
     * @return Fulgurio\LightCMSBundle\Entity\Page
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * $formHander getter
     *
     * @return Fulgurio\LightCMSBlogBundle\Form\Handler\AdminPostHandler
     */
    public function getFormHandler()
    {
        return $this->formHandler;
    }

    /**
     * @see Fulgurio\LightCMSBlogBundle\Form\Handler\AdminPostHandler:getForm
     */
    public function getForm()
    {
        $this->formHandler->getForm();
    }

    /**
     * @see Fulgurio\LightCMSBlogBundle\Form\Handler\AdminPostHandler:getDoctrine
     */
    public function getDoctrine()
    {
        $this->formHandler->getDoctrine();
    }
}