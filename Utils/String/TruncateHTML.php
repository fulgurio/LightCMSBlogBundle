<?php
/**
 * HTML truncate methods
 *
 * @author http://www.pjgalbraith.com/2011/11/truncating-text-html-with-php/
 * @author
 */
namespace Fulgurio\LightCMSBlogBundle\Utils\String;

use Fulgurio\LightCMSBlogBundle\Utils\String\DOMLettersIterator;
use Fulgurio\LightCMSBlogBundle\Utils\String\DOMWordsIterator;

class TruncateHTML
{
    /**
     * Cut string to wanted number characters
     *
     * @param string $html
     * @param number $limit
     * @param string $ellipsis
     * @return string
     */
    public static function truncateChars($html, $limit, $ellipsis = '...')
    {
        if ($limit <= 0 || $limit >= strlen(strip_tags($html)))
        {
            return $html;
        }

        // If $html don't start with a html tag, DOMDocument adds a <p> tag ...
        $html = '<div>' . $html . '</div>';

        $dom = new \DOMDocument();
        $dom->loadHTML($html);

        $body = $dom->getElementsByTagName('body')->item(0);

        $it = new DOMLettersIterator($body);

        foreach ($it as $key => $letter)
        {
            if ($it->key() >= $limit)
            {
                $currentText = $it->currentTextPosition();
                $currentText[0]->nodeValue = substr($currentText[0]->nodeValue, 0, $currentText[1] + 1);
                self::removeProceedingNodes($currentText[0], $body);
                self::insertEllipsis($currentText[0], $ellipsis);
                break;
            }
        }

        $generatedHtml = preg_replace('~<(?:!DOCTYPE|/?(?:html|head|body))[^>]*>\s*~i', '', $dom->saveHTML());
        // We remove <div> and </div> tags
        return substr($generatedHtml, 5, -6);
    }

    /**
     * Cut string to wanted number words
     *
     * @param string $html
     * @param number $limit
     * @param string $ellipsis
     * @return string
     */
    public static function truncateWords($html, $limit, $ellipsis = '...')
    {
        if ($limit <= 0 || $limit >= self::countWords(strip_tags($html)))
        {
            return $html;
        }

        // If $html don't start with a html tag, DOMDocument adds a <p> tag ...
        $html = '<div>' . $html . '</div>';

        $dom = new \DOMDocument();
        $dom->loadHTML($html);

        $body = $dom->getElementsByTagName('body')->item(0);
        $it = new DOMWordsIterator($body);

        foreach ($it as $word)
        {
            if ($it->key() >= $limit)
            {
                $currentWordPosition = $it->currentWordPosition();
                $curNode = $currentWordPosition[0];
                $offset = $currentWordPosition[1];
                $words = $currentWordPosition[2];

                $curNode->nodeValue = substr($curNode->nodeValue, 0, $words[$offset][1] + strlen($words[$offset][0]));

                self::removeProceedingNodes($curNode, $body);
                self::insertEllipsis($curNode, $ellipsis);
                break;
            }
        }

        $generatedHtml = preg_replace('~<(?:!DOCTYPE|/?(?:html|head|body))[^>]*>\s*~i', '', $dom->saveHTML());
        // We remove <div> tag
        return substr($generatedHtml, 5, -6);
    }

    public static function truncateWordsAndChars($html, $limit, $ellipsis = '...')
    {
        if ($limit <= 0 || $limit >= strlen(strip_tags($html)))
        {
            return $html;
        }

        // If $html don't start with a html tag, DOMDocument adds a <p> tag ...
        $html = '<div>' . $html . '</div>';
        $dom = new \DOMDocument();
        $dom->loadHTML($html);

        $body = $dom->getElementsByTagName('body')->item(0);

        $it = new DOMLettersIterator($body);

        foreach ($it as $key => $letter)
        {
            if ($it->key() >= $limit)
            {
                $currentText = $it->currentTextPosition();
                $words = preg_split('/[\n\r\t \']+/', $currentText[0]->nodeValue, -1, PREG_SPLIT_NO_EMPTY);
                $totalLength = 0;
                if (empty($words))
                {
                    $str = substr($currentText[0]->nodeValue, 0, $currentText[1] + 1);
                }
                else
                {
                    foreach ($words as $word)
                    {
                        $len = strlen($word);
                        if ($totalLength != 0 && $totalLength + $len > $currentText[1] + 1)
                        {
                            // We cut the string
                            $str = substr($currentText[0]->nodeValue, 0, $totalLength);
                            break;
                        }
                        // Add $len and the space
                        $totalLength += $len + 1;
                    }
                    $str = substr($currentText[0]->nodeValue, 0, $totalLength);
                }
                // Cut string on "'" character is not pretty, so we remove it
                if ($str[strlen($str) - 1] == '\'')
                {
                    $str = substr($str, 0, -1);
                }
                $currentText[0]->nodeValue = $str;
                self::removeProceedingNodes($currentText[0], $body);
                self::insertEllipsis($currentText[0], $ellipsis);
                break;
            }
        }

        $generatedHtml = preg_replace('~<(?:!DOCTYPE|/?(?:html|head|body))[^>]*>\s*~i', '', $dom->saveHTML());
        // We remove <div> tag
        return substr($generatedHtml, 5, -6);
    }

    /**
     *
     * @param \DOMNode $domNode
     * @param \DOMNode $topNode
     */
    private static function removeProceedingNodes(\DOMNode $domNode, \DOMNode $topNode)
    {
        $nextNode = $domNode->nextSibling;

        if ($nextNode !== NULL)
        {
            self::removeProceedingNodes($nextNode, $topNode);
            $domNode->parentNode->removeChild($nextNode);
        }
        else
        {
            //scan upwards till we find a sibling
            $curNode = $domNode->parentNode;
            while ($curNode !== $topNode)
            {
                if ($curNode->nextSibling !== NULL)
                {
                    $curNode = $curNode->nextSibling;
                    self::removeProceedingNodes($curNode, $topNode);
                    $curNode->parentNode->removeChild($curNode);
                    break;
                }
                $curNode = $curNode->parentNode;
            }
        }
    }

    /**
     * Insert ellipsis
     *
     * @param \DOMNode $domNode
     * @param string $ellipsis
     */
    private static function insertEllipsis(\DOMNode $domNode, $ellipsis)
    {
        $avoid = array('a', 'strong', 'em', 'h1', 'h2', 'h3', 'h4', 'h5'); //html tags to avoid appending the ellipsis to

        if (in_array($domNode->parentNode->nodeName, $avoid) && $domNode->parentNode->parentNode !== NULL)
        {
            // Append as text node to parent instead
            $textNode = new \DOMText($ellipsis);

            if ($domNode->parentNode->parentNode->nextSibling)
            {
                $domNode->parentNode->parentNode->insertBefore($textNode, $domNode->parentNode->parentNode->nextSibling);
            }
            else
            {
                $domNode->parentNode->parentNode->appendChild($textNode);
            }
        }
        else
        {
            // Append to current node
            $domNode->nodeValue = rtrim($domNode->nodeValue).$ellipsis;
        }
    }

    /**
     * Count words
	 *
     * @param string $text
     * @return number
     */
    private static function countWords($text)
    {
        $words = preg_split('/[\n\r\t [:punct:]]+/', $text, -1, PREG_SPLIT_NO_EMPTY);
        return count($words);
    }

}