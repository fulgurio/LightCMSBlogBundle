<?php
/**
 * Iterates individual words of DOM text and CDATA nodes
 * while keeping track of their position in the document.
 *
 * Example:
 *
 *  $doc = new DOMDocument();
 *  $doc->load('example.xml');
 *  foreach(new DOMWordsIterator($doc) as $word) echo $word;
 *
 * @author pjgalbraith http://www.pjgalbraith.com
 * @author porneL http://pornel.net (based on DOMLettersIterator available at http://pornel.net/source/domlettersiterator.php)
 * @license Public Domain
 */
namespace Fulgurio\LightCMSBlogBundle\Utils\String;

final class DOMWordsIterator implements \Iterator {

    private $start;
    private $current;
    private $offset = -1;
    private $key = 0;
    private $words;

    /**
     * expects DOMElement or DOMDocument (see DOMDocument::load and DOMDocument::loadHTML)
     */
    public function __construct(\DOMNode $el)
    {
        if ($el instanceof \DOMDocument)
        {
            $this->start = $el->documentElement;
        }
        elseif ($el instanceof \DOMElement)
        {
            $this->start = $el;
        }
        else
        {
            throw new \InvalidArgumentException('Invalid arguments, expected DOMElement or DOMDocument');
        }
    }

    /**
     * Returns position in text as DOMText node and character offset.
     * (it's NOT a byte offset, you must use mb_substr() or similar to use this offset properly).
     * node may be NULL if iterator has finished.
     *
     * @return array
     */
    public function currentWordPosition()
    {
        return array($this->current, $this->offset, $this->words);
    }

    /**
     * Returns DOMElement that is currently being iterated or NULL if iterator has finished.
     *
     * @return DOMElement
     */
    public function currentElement()
    {
        return $this->current ? $this->current->parentNode : NULL;
    }

    /**
     * (non-PHPdoc)
     * @see \Iterator:key()
     */
    public function key()
    {
        return $this->key;
    }

    /**
     * (non-PHPdoc)
     * @see \Iterator:next()
     */
    public function next()
    {
        if (!$this->current)
        {
            return;
        }

        if ($this->current->nodeType == XML_TEXT_NODE || $this->current->nodeType == XML_CDATA_SECTION_NODE)
        {
            if ($this->offset == -1)
            {
                $this->words = preg_split('/[\n\r\t [:punct:]]+/', $this->current->textContent, -1, PREG_SPLIT_NO_EMPTY|PREG_SPLIT_OFFSET_CAPTURE);
            }
            $this->offset++;

            if ($this->offset < count($this->words))
            {
                $this->key++;
                return;
            }
            $this->offset = -1;
        }

        while ($this->current->nodeType == XML_ELEMENT_NODE && $this->current->firstChild)
        {
            $this->current = $this->current->firstChild;
            if ($this->current->nodeType == XML_TEXT_NODE || $this->current->nodeType == XML_CDATA_SECTION_NODE)
            {
                return $this->next();
            }
        }

        while (!$this->current->nextSibling && $this->current->parentNode)
        {
            $this->current = $this->current->parentNode;
            if ($this->current === $this->start)
            {
                $this->current = NULL;
                return;
            }
        }

        $this->current = $this->current->nextSibling;

        return $this->next();
    }

    /**
     * (non-PHPdoc)
     * @see \Iterator:current()
     */
    public function current()
    {
        if ($this->current)
        {
            return $this->words[$this->offset][0];
        }
        return NULL;
    }

    /**
     * (non-PHPdoc)
     * @see \Iterator:valid()
     */
    public function valid()
    {
        return !!$this->current;
    }

    /**
     * (non-PHPdoc)
     * @see \Iterator:rewind()
     */
    public function rewind()
    {
        $this->offset = -1;
        $this->words = array();
        $this->current = $this->start;
        $this->next();
    }
}
