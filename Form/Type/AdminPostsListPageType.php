<?php
/*
 * This file is part of the LightCMSBlogBundle package.
 *
 * (c) Fulgurio <http://fulgurio.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fulgurio\LightCMSBlogBundle\Form\Type;

use Fulgurio\LightCMSBundle\Form\Type\AdminPageType;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormBuilderInterface;

class AdminPostsListPageType extends AdminPageType
{
    /**
     * Default post number per page
     *
     * @var number
     */
    private $defaultNbPost = 10;


    /**
     * Constructor
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        if ($container->hasParameter('fulgurio_light_cms.posts'))
        {
            $postsModelData = $container->getParameter('fulgurio_light_cms.posts');
            $this->defaultNbPost = $postsModelData['nb_per_page'];
        }
    }

    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Form.AbstractType::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->add('nb_posts_per_page', 'number', array(
                    'required' => FALSE,
                    'mapped' => FALSE,
                    'invalid_message' => 'fulgurio.lightcms.posts.add_form.invalid_nb_posts_per_page',
                    'data' => $this->defaultNbPost
            ));
    }
}