<?php
/*
 * This file is part of the LightCMSBlogBundle package.
 *
 * (c) Fulgurio <http://fulgurio.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Fulgurio\LightCMSBlogBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class FulgurioLightCMSBlogExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $container->setParameter('fulgurio_light_cms.posts', $config['posts']);
        if ($container->hasParameter('fulgurio_light_cms.models'))
        {
            $models = $container->getParameter('fulgurio_light_cms.models');
        }
        else
        {
            $models = array();
        }
        $models['postsList'] = array(
                'name' => 'posts_list',
                'back' => array(
                        'form' =>       'Fulgurio\LightCMSBlogBundle\Form\AdminPostsListPageType',
                        'handler' =>    'Fulgurio\LightCMSBlogBundle\Form\AdminPostsListPageHandler',
                        'template' =>   'FulgurioLightCMSBlogBundle:models:postsListAdminAddForm.html.twig',
                        'view' =>       'FulgurioLightCMSBlogBundle:models:postsListAdminView.html.twig',
                ),
                'front' => array(
                        'template' =>   'FulgurioLightCMSBlogBundle:models:postsListFront.html.twig',
                        'controller' => 'Fulgurio\LightCMSBlogBundle\Controller\FrontPostsListController::list',
                ),
                'allow_childrens' => FALSE,
                'is_unique'       => TRUE
        );
        $models['post'] = array(
                'name' => 'post',
                'back' => array(
                        'form' =>       'Fulgurio\LightCMSBlogBundle\Form\AdminPostType',
                        'handler' =>    'Fulgurio\LightCMSBlogBundle\Form\AdminPostHandler',
                        'template' =>   'FulgurioLightCMSBlogBundle:models:postAdminAddForm.html.twig',
                ),
                'front' => array(
                        'template' =>   'FulgurioLightCMSBlogBundle:models:postFront.html.twig',
                        'controller' => 'Fulgurio\LightCMSBlogBundle\Controller\FrontPostController::show',
                ),
                'allow_childrens' => FALSE,
                'is_unique'       => FALSE,
                'hidden'          => TRUE
        );
        $container->setParameter('fulgurio_light_cms.models', $models);
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
}
