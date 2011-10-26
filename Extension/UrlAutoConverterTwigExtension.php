<?php

namespace Liip\UrlAutoConverterBundle\Extension;

class UrlAutoConverterTwigExtension extends \Twig_Extension
{
    protected $linkClass;
    protected $target;
    protected $debugMode;

    // @codeCoverageIgnoreStart
    public function getName()
    {
        return 'liip_urlautoconverter';
    }

    public function setLinkClass($class)
    {
        $this->linkClass = $class;
    }

    public function setTarget($target)
    {
        $this->target = $target;
    }

    public function setDebugMode($debug)
    {
        $this->debugMode = $debug;
    }
    // @codeCoverageIgnoreEnd

    public function getFilters()
    {
        return array(
            'converturls' => new \Twig_Filter_Method($this, 'autoConvertUrls', array('is_safe' => array('html')))
        );
    }

    /**
     * method that finds different occurrences of urls or email addresses in a string
     * @param string $string input string, can be long text
     * @return string string with replaced links
     */
    public function autoConvertUrls($string)
    {
        $pattern = '/[-a-zA-Z0-9@:%_\+.~#?&\/\/=]{2,256}\.[a-z]{2,4}\b(\/[-a-zA-Z0-9@:%_\+.~#?&\/\/=]*)?/';
        $stringFiltered = preg_replace_callback($pattern, array($this, 'callbackReplace'), $string);

        return $stringFiltered;
    }

    public function callbackReplace($matches)
    {
        $url = $matches[0];
        $urlWithPrefix = $matches[0];

        if (strpos($url, '@') !== false) {
            $urlWithPrefix = 'mailto:'.$url;
        } else if (strpos($url, 'https://') === 0 ) {
            $urlWithPrefix = $url;
        } else if (strpos($url, 'http://') !== 0) {
            $urlWithPrefix = 'http://'.$url;
        }

        $style = ($this->debugMode) ? ' style="color:#00ff00"' : '';

        return '<a href="'.$urlWithPrefix.'" class="'.$this->linkClass.'" target="'.$this->target.'"'.$style.'>'.$url.'</a>';
    }
}