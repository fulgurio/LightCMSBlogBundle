<?php
/*
 * This file is part of the LightCMSBlogBundle package.
 *
 * (c) Fulgurio <http://fulgurio.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fulgurio\LightCMSBlogBundle\Form\Handler;

use Fulgurio\LightCMSBlogBundle\FulgurioLightCMSBlogEvents;
use Fulgurio\LightCMSBlogBundle\Event\Form\Handler\AdminPostHandlerEvent;
use Fulgurio\LightCMSBundle\Entity\Page;
use Fulgurio\LightCMSBundle\Form\Handler\AdminPageHandler;
use Fulgurio\LightCMSBundle\Utils\LightCMSUtils;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class AdminPostHandler extends AdminPageHandler
{
    /**
     * Post config
     * @var array
     */
    private $config;

    /**
     * Dispatcher
     *
     * @var Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    private $dispatcher;


    /**
     * Processing form values
     *
     * @param Page $page
     * @return boolean
     */
    public function process(Page $page)
    {
        if ($this->request->getMethod() == 'POST')
        {
            $this->form->handleRequest($this->request);
            if ($this->form->isValid())
            {
                $data = $this->request->get('post');
                $this->updatePageMetas($page, $data);
                $em = $this->doctrine->getManager();
                if (isset($data['abstract']))
                {
                    $em->persist($this->initMetaEntity($page, 'abstract', trim($data['abstract'])));
                }
                // New post
                if ($page->getId() == 0)
                {
                    if (is_a($this->user, 'Symfony\Component\Security\Core\User\UserInterface'))
                    {
                        $page->setOwnerId($this->user->getId());
                    }
                    $page->setPageType('post');
                    $page->setModel('post');
                    $page->setPosition(0);
                }
                $page->setSlug(LightCMSUtils::makeSlug(isset($data['lang']) ? $data['lang'] : $page->getTitle()));
                if (isset($data['lang']))
                {
                    $page->setLang($data['lang']);
                }
                else if ($page->getParent() && $page->getLang() == FALSE)
                {
                    $page->setLang($page->getParent()->getLang());
                }
                $this->makeFullpath($page);
                $event = new AdminPostHandlerEvent($page, $this);
                $em->persist($page);
                $this->dispatcher->dispatch(FulgurioLightCMSBlogEvents::POST_FORM_HANDLER, $event);
                $em->flush();
                return TRUE;
            }
        }
        return FALSE;
    }

    /**
     * Init page full path and check if it doesn't already exist
     *
     * @param Page $page
     */
    public function makeFullpath(Page $page)
    {
        if ($page->getParent() == NULL)
        {
            $page->setFullpath('');
            $page->setSlug('');
        }
        else
        {
            $parentFullpath = $page->getParent()->getFullpath();
            $format = date($this->config['format']);
            $slug = $this->addSuffixNumber($format . '/' . $page->getSlug(), $page);
            $page->setFullpath(($parentFullpath != '' ? ($parentFullpath . '/') : '') . $slug);
            $page->setSlug($slug);
        }
    }

    /**
     * $config setter
     *
     * @param array $config
     * @return AdminPostHandler
     */
    public function setPostConfig(array $config)
    {
        $this->config = $config;

        return $this;
    }

    /**
     * Dispatcher setter
     *
     * @param EventDispatcherInterface $dispatcher
     * @return AdminPostHandler
     */
    public function setDispatcher(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;

        return $this;
    }
}