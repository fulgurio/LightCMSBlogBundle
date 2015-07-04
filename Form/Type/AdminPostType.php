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

use Fulgurio\LightCMSBlogBundle\FulgurioLightCMSBlogEvents;
use Fulgurio\LightCMSBlogBundle\Event\Form\Type\AdminPostTypeEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class AdminPostType extends AbstractType
{
    /**
     * Availables status page
     *
     * @var array
     */
    private $status;

    /**
     * Dispatcher
     *
     * @var Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    private $dispatcher;


    /**
     * Constructor
     *
     * @param $container
     */
    public function __construct(ContainerInterface $container)
    {
        if ($container->hasParameter('fulgurio_light_cms.languages'))
        {
            $this->langs = $container->getParameter('fulgurio_light_cms.languages');
        }
        $this->status = array(
            'draft' => 'draft',
            'published' => 'published'
        );
    }

    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Form.AbstractType::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array(
                'error_bubbling' => TRUE,
                'constraints' => array(
                    new NotBlank(array('message' => 'fulgurio.lightcms.pages.add_form.title_is_required'))
                )
            ))
            ->add('content', 'text')
            ->add('status', 'choice', array(
                'choices'  => $this->status,
                'required' => TRUE,
                'error_bubbling' => TRUE,
                'invalid_message' => 'fulgurio.lightcms.pages.add_form.status_is_required'
            ))
            ->add('meta_keywords', null, array(
                'required' => FALSE,
                'mapped' => FALSE)
            )
            ->add('meta_description', 'text', array(
                'required' => FALSE,
                'mapped' => FALSE)
            )
        ;
        $event = new AdminPostTypeEvent($builder);
        $this->dispatcher->dispatch(FulgurioLightCMSBlogEvents::POST_FORM_TYPE, $event);
    }

    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Form.FormTypeInterface::getName()
     */
    final public function getName()
    {
        return 'post';
    }

    /**
     * Dispatcher setter
     *
     * @param EventDispatcherInterface $dispatcher
     */
    public function setDispatcher(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }
}