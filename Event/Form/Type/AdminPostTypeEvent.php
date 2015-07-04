<?php
/*
 * This file is part of the LightCMSBlogBundle package.
 *
 * (c) Fulgurio <http://fulgurio.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fulgurio\LightCMSBlogBundle\Event\Form\Type;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Form\FormBuilderInterface;

class AdminPostTypeEvent extends Event
{
    /**
     * @var Symfony\Component\Form\FormBuilderInterface
     */
    private $builder;


    /**
     * Constructor
     *
     * @param FormBuilderInterface $builder
     */
    public function __construct(FormBuilderInterface $builder)
    {
        $this->builder = $builder;
    }

    /**
     * Builder getter
     *
     * @return FormBuilderInterface
     */
    public function getBuilder()
    {
        return $this->builder;
    }
}