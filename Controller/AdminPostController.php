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

use Fulgurio\LightCMSBundle\Entity\Page;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Controller managing posts
 */
class AdminPostController extends Controller
{
    /**
     * Nb of posts per page
     *
     * @var Number
     */
    const DEFAULT_POST_PER_PAGE = 10;


    /**
     * Posts list
     */
    public function listAction()
    {
        $currentPage = $this->get('request')->query->get('page', 1);
        try
        {
            $parent = $this->getPostsListPage();
        }
        catch (\Exception $e)
        {
             $parent = NULL;
        }
        $query = $this->getDoctrine()->getManager()->createQuery('SELECT p FROM FulgurioLightCMSBundle:Page p WHERE p.page_type=:pageType ORDER BY p.created_at DESC');
        $query->setParameter('pageType', 'post');
        $posts = $this->get('knp_paginator')->paginate($query, $currentPage, self::DEFAULT_POST_PER_PAGE);
        return $this->render('FulgurioLightCMSBlogBundle:AdminPost:list.html.twig',
            array(
                'posts' => $posts,
                'hasNoPostRoot' => is_null($parent)
            )
        );
    }

    /**
     * Add post
     */
    public function addAction()
    {
        $post = new Page();
        $parent = $this->getPostsListPage();
        $post->setParent($parent);
        return $this->createPage($post, array());
    }

    /**
     * Edit page
     *
     * @param integer $pageId if specified, we are on edit page form
     */
    function editAction($pageId)
    {
        $options = array(
            'pageId' => $pageId,
            'pageMetas' => $this->getPageMetas($pageId)
        );
        $post = $this->getPost($pageId);
        return $this->createPage($post, $options);
    }

    /**
     * Create form for page entity, use for edit or add page
     *
     * @param Page $post
     * @param array $options
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function createPage(Page $post, $options)
    {
        $models = $this->container->getParameter('fulgurio_light_cms.models');
        $formClassName = isset($models['post']['back']['form']) ? $models['post']['back']['form'] : '\Fulgurio\LightCMSBlogBundle\Form\AdminPostType';
        $formHandlerClassName = isset($models['post']['back']['handler']) ? $models['post']['back']['handler'] : '\Fulgurio\LightCMSBlogBundle\Form\AdminPostHandler';
        $formType = new $formClassName($this->container);
        $formType->setDispatcher($this->get('event_dispatcher'));
        $form = $this->createForm($formType, $post);
        $formHandler = new $formHandlerClassName();
        $formHandler->setForm($form)
                ->setRequest($this->get('request'))
                ->setDoctrine($this->getDoctrine())
                ->setUser($this->getUser())
                ->setSlugSuffixSeparator($this->container->getParameter('fulgurio_light_cms.slug_suffix_separator'))
                ->setPostConfig($this->container->getParameter('fulgurio_light_cms.posts'))
                ->setDispatcher($this->get('event_dispatcher'));
        if ($formHandler->process($post))
        {
            $this->get('session')->getFlashBag()->add(
                    'success',
                    $this->get('translator')->trans(
                            isset($options['pageId']) ? 'fulgurio.lightcmsblog.posts.edit_form.success_msg' : 'fulgurio.lightcmsblog.posts.add_form.success_msg',
                            array(),
                            'admin'
                    )
            );
            return $this->redirect($this->generateUrl('AdminPosts'));
        }
        $options['form'] = $form->createView();
        if ($this->container->getParameter('fulgurio_light_cms.wysiwyg') && $this->container->hasParameter($this->container->getParameter('fulgurio_light_cms.wysiwyg')))
        {
            $options['wysiwyg'] = $this->container->getParameter($this->container->getParameter('fulgurio_light_cms.wysiwyg'));
        }
        $templateName = isset($models['post']['back']['template']) ? $models['post']['back']['template'] : 'FulgurioLightCMSBlogBundle:models:postAdminAddForm.html.twig';
        return $this->render($templateName, $options);
    }

    /**
     * Remove page, with confirm form
     *
     * @param integer $pageId
     */
    public function removeAction($pageId)
    {
        $post = $this->getPost($pageId);
        $request = $this->container->get('request');
        if ($request->request->get('confirm') === 'yes')
        {
            $em = $this->getDoctrine()->getManager();
            $em->remove($post);
            $em->flush();
            return $this->redirect($this->generateUrl('AdminPosts'));
        }
        else if ($request->request->get('confirm') === 'no')
        {
            return $this->redirect($this->generateUrl('AdminPosts'));
        }
        $templateName = $request->isXmlHttpRequest() ? 'FulgurioLightCMSBundle::adminConfirmAjax.html.twig' : 'FulgurioLightCMSBundle::adminConfirm.html.twig';
        return $this->render($templateName, array(
            'action' => $this->generateUrl('AdminPostsRemove', array('pageId' => $pageId)),
            'confirmationMessage' => $this->get('translator')->trans('fulgurio.lightcmsblog.posts.delete_confirm_message', array('%title%' => $post->getTitle()), 'admin'),
        ));
    }

    /**
     * Get posts list page
     *
     * @return Page
     * @throws NotFoundHttpException
     */
    private function getPostsListPage()
    {
        if (!$page = $this->getDoctrine()->getRepository('FulgurioLightCMSBundle:Page')->findOneBy(array('model' => 'postsList', 'page_type' => 'page')))
        {
            throw new NotFoundHttpException(
                $this->get('translator')->trans('fulgurio.lightcmsblog.posts.posts_list_page_not_found', array(), 'admin')
            );
        }
        return ($page);
    }

    /**
     * Get page from given ID, and ckeck if it exists
     *
     * @param integer $pageId
     * @throws NotFoundHttpException
     */
    private function getPost($pageId)
    {
        if (!$page = $this->getDoctrine()->getRepository('FulgurioLightCMSBundle:Page')->findOneBy(array('id' => $pageId, 'page_type' => 'post')))
        {
            throw new NotFoundHttpException(
                $this->get('translator')->trans('fulgurio.lightcms.post_not_found')
            );
        }
        return ($page);
    }

    /**
     * Get meta data from given ID page
     *
     * @param number $pageId
     * @return array
     */
    private function getPageMetas($pageId)
    {
        $pageMetas = array();
        $metas = $this->getDoctrine()->getRepository('FulgurioLightCMSBundle:PageMeta')->findByPage($pageId);
        foreach ($metas as $meta)
        {
            $pageMetas[$meta->getMetaKey()] = $meta;
        }
        return $pageMetas;
    }
}