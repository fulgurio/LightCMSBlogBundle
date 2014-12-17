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

use Fulgurio\LightCMSBundle\Entity\Page;
use Fulgurio\LightCMSBundle\Form\Handler\AdminPageHandler;
use Fulgurio\LightCMSBundle\Utils\LightCMSUtils;

class AdminPostHandler extends AdminPageHandler
{
    /**
     * Post config
     * @var array
     */
    private $config;


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
                // New post
                if ($page->getId() == 0)
                {
                    if (is_a($this->user, 'Fulgurio\LightCMSBundle\Entity\User'))
                    {
                        $page->setOwner($this->user);
                    }
                    $page->setCreatedAt(new \DateTime());
                    $page->setPageType('post');
                    $page->setModel('post');
                    $page->setPosition(0);
                }
                else
                {
                    $page->setUpdatedAt(new \DateTime());
                }
                $page->setSlug(LightCMSUtils::makeSlug(isset($data['lang']) ? $data['lang'] : $page->getTitle()));
                if (isset($data['lang']))
                {
                    $page->setLang($data['lang']);
                }
                else if ($page->getParent())
                {
                    $page->setLang($page->getParent()->getLang());
                }
                $this->makeFullpath($page);
                $em->persist($page);
                $em->flush();
                return (TRUE);
            }
        }
        return (FALSE);
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
     */
    public function setPostConfig($config)
    {
        $this->config = $config;
    }
}