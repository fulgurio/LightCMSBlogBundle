<?php
/**
 * Tests for TruncateHTML methods
 */
namespace Fulgurio\LightCMSBlogBundle\Tests\Utils\String;

use Fulgurio\LightCMSBlogBundle\Utils\String\TruncateHTML;

class TruncateHTMLTest extends \PHPUnit_Framework_TestCase
{
    //Sentence sample "this is a test for LightCMSBlog bundle, hope it's working"
    //const HTML_SAMPLE = 'this is <p>a <strong>test</strong> <span>for LightCMSBlog</span>&nbsp;bundle</p>, hope it\'s working';
    const HTML_SAMPLE = 'this is <p>a <strong>test</strong> <span>for LightCMSBlog</span> bundle</p>, hope it\'s working';

    // Ellipsis
    const ELLIPSIS = '...';

    /**
     * TruncateHTML:truncateChars tests
     */
    public function testTruncateChars()
    {
        $this->assertEquals('thi' . self::ELLIPSIS, TruncateHTML::truncateChars(self::HTML_SAMPLE, 3, self::ELLIPSIS));
        $this->assertEquals('this' . self::ELLIPSIS, TruncateHTML::truncateChars(self::HTML_SAMPLE, 4, self::ELLIPSIS));
        $this->assertEquals('this is' . self::ELLIPSIS, TruncateHTML::truncateChars(self::HTML_SAMPLE, 7, self::ELLIPSIS));
        $this->assertEquals('this is <p>a...</p>', TruncateHTML::truncateChars(self::HTML_SAMPLE, 9, self::ELLIPSIS));
        $this->assertEquals('this is <p>a <strong>test</strong>...</p>', TruncateHTML::truncateChars(self::HTML_SAMPLE, 14, self::ELLIPSIS));
        $this->assertEquals('this is <p>a <strong>test</strong> <span>for...</span></p>', TruncateHTML::truncateChars(self::HTML_SAMPLE, 19, self::ELLIPSIS));
        $this->assertEquals('this is <p>a <strong>test</strong> <span>for LightCMSBlog...</span></p>', TruncateHTML::truncateChars(self::HTML_SAMPLE, 31, self::ELLIPSIS));
        $this->assertEquals('this is <p>a <strong>test</strong> <span>for LightCMSBlog</span> bundle...</p>', TruncateHTML::truncateChars(self::HTML_SAMPLE, 38, self::ELLIPSIS));
        $this->assertEquals('this is <p>a <strong>test</strong> <span>for LightCMSBlog</span> bundle</p>,' . self::ELLIPSIS, TruncateHTML::truncateChars(self::HTML_SAMPLE, 39, self::ELLIPSIS));
        $this->assertEquals('this is <p>a <strong>test</strong> <span>for LightCMSBlog</span> bundle</p>, hope it\'s' . self::ELLIPSIS, TruncateHTML::truncateChars(self::HTML_SAMPLE, 49, self::ELLIPSIS));
        $this->assertEquals(self::HTML_SAMPLE, TruncateHTML::truncateChars(self::HTML_SAMPLE, 57, self::ELLIPSIS));
        $this->assertEquals(self::HTML_SAMPLE, TruncateHTML::truncateChars(self::HTML_SAMPLE, 100, self::ELLIPSIS));
    }

    /**
     * TruncateHTML:truncateWords tests
     */
    public function testTruncateWords()
    {
        $this->assertEquals('this' . self::ELLIPSIS, TruncateHTML::truncateWords(self::HTML_SAMPLE, 1, self::ELLIPSIS));
        $this->assertEquals('this is' . self::ELLIPSIS, TruncateHTML::truncateWords(self::HTML_SAMPLE, 2, self::ELLIPSIS));
        $this->assertEquals('this is <p>a <strong>test</strong>...</p>', TruncateHTML::truncateWords(self::HTML_SAMPLE, 4, self::ELLIPSIS));
        $this->assertEquals('this is <p>a <strong>test</strong> <span>for...</span></p>', TruncateHTML::truncateWords(self::HTML_SAMPLE, 5, self::ELLIPSIS));
        $this->assertEquals('this is <p>a <strong>test</strong> <span>for LightCMSBlog</span> bundle...</p>', TruncateHTML::truncateWords(self::HTML_SAMPLE, 7, self::ELLIPSIS));
        $this->assertEquals('this is <p>a <strong>test</strong> <span>for LightCMSBlog</span> bundle</p>, hope' . self::ELLIPSIS, TruncateHTML::truncateWords(self::HTML_SAMPLE, 8, self::ELLIPSIS));
        $this->assertEquals('this is <p>a <strong>test</strong> <span>for LightCMSBlog</span> bundle</p>, hope it\'s' . self::ELLIPSIS, TruncateHTML::truncateWords(self::HTML_SAMPLE, 10, self::ELLIPSIS));
        $this->assertEquals(self::HTML_SAMPLE, TruncateHTML::truncateWords(self::HTML_SAMPLE, 11, self::ELLIPSIS));
        $this->assertEquals(self::HTML_SAMPLE, TruncateHTML::truncateWords(self::HTML_SAMPLE, 20, self::ELLIPSIS));
    }

    /**
     * TruncateHTML:truncateWordsAndChars tests
     */
    public function testTruncateWordsAndChars()
    {
        $this->assertEquals('this' . self::ELLIPSIS, TruncateHTML::truncateWordsAndChars(self::HTML_SAMPLE, 4, self::ELLIPSIS));
        $this->assertEquals('this' . self::ELLIPSIS, TruncateHTML::truncateWordsAndChars(self::HTML_SAMPLE, 5, self::ELLIPSIS));
        $this->assertEquals('this' . self::ELLIPSIS, TruncateHTML::truncateWordsAndChars(self::HTML_SAMPLE, 6, self::ELLIPSIS));
        $this->assertEquals('this is' . self::ELLIPSIS, TruncateHTML::truncateWordsAndChars(self::HTML_SAMPLE, 7, self::ELLIPSIS));
        $this->assertEquals('this is <p>a' . self::ELLIPSIS . '</p>', TruncateHTML::truncateWordsAndChars(self::HTML_SAMPLE, 9, self::ELLIPSIS));
        $this->assertEquals('this is <p>a <strong>test</strong>' . self::ELLIPSIS . '</p>', TruncateHTML::truncateWordsAndChars(self::HTML_SAMPLE, 14, self::ELLIPSIS));
        $this->assertEquals('this is <p>a <strong>test</strong> <span>for' . self::ELLIPSIS . '</span></p>', TruncateHTML::truncateWordsAndChars(self::HTML_SAMPLE, 18, self::ELLIPSIS));
        $this->assertEquals('this is <p>a <strong>test</strong> <span>for LightCMSBlog' . self::ELLIPSIS . '</span></p>', TruncateHTML::truncateWordsAndChars(self::HTML_SAMPLE, 31, self::ELLIPSIS));
        $this->assertEquals('this is <p>a <strong>test</strong> <span>for LightCMSBlog</span> bundle' . self::ELLIPSIS . '</p>', TruncateHTML::truncateWordsAndChars(self::HTML_SAMPLE, 38, self::ELLIPSIS));
        $this->assertEquals('this is <p>a <strong>test</strong> <span>for LightCMSBlog</span> bundle</p>, hope' . self::ELLIPSIS, TruncateHTML::truncateWordsAndChars(self::HTML_SAMPLE, 44, self::ELLIPSIS));
        $this->assertEquals('this is <p>a <strong>test</strong> <span>for LightCMSBlog</span> bundle</p>, hope it' . self::ELLIPSIS, TruncateHTML::truncateWordsAndChars(self::HTML_SAMPLE, 47, self::ELLIPSIS));
        $this->assertEquals('this is <p>a <strong>test</strong> <span>for LightCMSBlog</span> bundle</p>, hope it\'s' . self::ELLIPSIS, TruncateHTML::truncateWordsAndChars(self::HTML_SAMPLE, 49, self::ELLIPSIS));
        $this->assertEquals(self::HTML_SAMPLE, TruncateHTML::truncateWordsAndChars(self::HTML_SAMPLE, 57, self::ELLIPSIS));
    }
}
