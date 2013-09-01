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
 * Controller displaying post
 */
class FrontPostController extends FrontPageController
{
    /**
     * Display page
     *
     */
    public function showAction()
    {
        $templateName = isset($models[$this->page->getModel()]['front']['template']) ? $models[$this->page->getModel()]['front']['template'] : 'FulgurioLightCMSBundle:models:standardFront.html.twig';
        $pageRoot = $this->page->getSlug() == '' ? $this->page : $this->getDoctrine()->getRepository('FulgurioLightCMSBundle:Page')->findOneByFullpath('');
        return $this->render($templateName, array(
            'pageRoot' => $pageRoot,
            'currentPage' => $this->page
        ));
    }
}