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
        // Will be usefull for comment filter
        return parent::showAction();
    }
}