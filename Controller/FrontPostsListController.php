<?php
/*
 * This file is part of the LightCMSBlogBundle package.
 *
 * (c) Fulgurio <http://fulgurio.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fulgurio\LightCMSBlogBundle\Controller;

use Fulgurio\LightCMSBundle\Controller\FrontPageController;

/**
 * Controller displaying posts list
 */
class FrontPostsListController extends FrontPageController
{
    /**
     * Display page
     */
    public function listAction()
    {
        // Page filter : only published page are loaded (menu, or any where in the page)
        $filter = $this->getDoctrine()->getManager()->getFilters()->enable('page');
        $filter->setParameter('status', 'published');
        $pageRoot = $this->getDoctrine()->getRepository('FulgurioLightCMSBundle:Page')->findOneBy(array('fullpath' => ''));
        $config = $this->container->getParameter('fulgurio_light_cms.posts');
        $currentPage = $this->get('request')->query->get('page', 1);
        $nbPerPage = $this->page->getMetaValue('nb_posts_per_page') ? $this->page->getMetaValue('nb_posts_per_page') : $config['nb_per_page'];
        $query = $this->getDoctrine()->getEntityManager()->createQuery('SELECT p FROM FulgurioLightCMSBundle:Page p WHERE p.page_type=:pageType ORDER BY p.created_at DESC');
        $query->setParameter('pageType', 'post');
        $posts = $this->get('knp_paginator')->paginate($query, $currentPage, $nbPerPage);
        $models = $this->container->getParameter('fulgurio_light_cms.models');
        $templateName = isset($models[$this->page->getModel()]['front']['template']) ? $models[$this->page->getModel()]['front']['template'] : 'FulgurioLightCMSBundle:models:standardFront.html.twig';
        return $this->render($templateName, array(
            'pageRoot' => $pageRoot,
            'currentPage' => $this->page,
            'posts' => $posts,
        ));
    }
}