<?php
/*
 * This file is part of the LightCMSBlogBundle package.
 *
 * (c) Fulgurio <http://fulgurio.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fulgurio\LightCMSBlogBundle\Form;

use Fulgurio\LightCMSBundle\Form\AdminPageHandler;
use Fulgurio\LightCMSBundle\Entity\Page;

class AdminPostsListPageHandler extends AdminPageHandler
{
    /**
     * Update page metas
     *
     * @param Page $page
     * @param array $data
     */
    protected function updatePageMetas(Page $page, $data)
    {
        parent::updatePageMetas($page, $data);
        $em = $this->doctrine->getEntityManager();
        $em->persist($this->initMetaEntity($page, 'nb_posts_per_page', $data['nb_posts_per_page']));
    }
}