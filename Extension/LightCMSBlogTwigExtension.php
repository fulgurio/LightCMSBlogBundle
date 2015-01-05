<?php
/*
 * This file is part of the LightCMSBlogundle package.
 *
 * (c) Fulgurio <http://fulgurio.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fulgurio\LightCMSBlogBundle\Extension;

use Fulgurio\LightCMSBlogBundle\Utils\String\TruncateHTML;
use Symfony\Bridge\Doctrine\RegistryInterface;

class LightCMSBlogTwigExtension extends \Twig_Extension
{
    /**
     * Doctrine object
     *
     * @var RegistryInterface
     */
    private $doctrine;

    /**
     * Constructor
     *
     * @param UrlGeneratorInterface $doctrine
     */
    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine  = $doctrine;
    }

    /**
     * (non-PHPdoc)
     * @see Twig_Extension::getFunctions()
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('getLastestPosts', array($this, 'getLastestPosts'))
        );
    }

    /**
     * (non-PHPdoc)
     * @see Twig_Extension::getFilters()
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('excerpt', array($this, 'cutContentToExcerpt'))
        );
    }

    /**
     * Get lastest posts
     *
     * @param number $limit
     * @return array|NULL
     */
    public function getLastestPosts($limit = 10)
    {
        $em = $this->doctrine->getManager();
        return $em->getRepository('FulgurioLightCMSBundle:Page')
                ->findBy(array('page_type' => 'post'), array('updated_at' => 'DESC', 'created_at' => 'DESC'), $limit);
    }

    /**
     * Cut content to make excerpt
     *
     * @param string $content
     * @param number $lineNb
     * @param number $charNb
     * @param string $ellipsis
     */
    public function cutContentToExcerpt($content, $lineNb, $charNb, $ellipsis = '...')
    {
        return TruncateHTML::truncateWordsAndChars($content, $charNb, $ellipsis);
    }

    /**
     * (non-PHPdoc)
     * @see Twig_ExtensionInterface::getName()
     */
    public function getName()
    {
        return 'LightCMSBlog_extension';
    }
}