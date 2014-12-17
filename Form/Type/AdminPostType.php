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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdminPostType extends AbstractType
{
    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Form.AbstractType::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('content', 'text')
            ->add('status', 'choice', array(
                'choices'   => array(
                    'draft' => 'draft',
                    'published' => 'published'),
                'required' => TRUE,
                )
            )
            ->add('meta_keywords', null, array('required' => FALSE, 'mapped' => FALSE))
            ->add('meta_description', 'text', array('required' => FALSE, 'mapped' => FALSE))
        ;
    }

    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Form.FormTypeInterface::getName()
     */
    final public function getName()
    {
        return 'page';
    }
}