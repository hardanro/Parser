<?php

class HtmlBuilder
{

    /**
     * Load the code based on the type (url,string or file). If new source types are added, it's changed only here
     *
     * @param string $source
     * @param string $type
     * @return string
     */
    public static function build($source, $type)
    {
        switch ($type) {
            case 'url':
                $return = file_get_contents($source);
                break;
            case 'string':
                $return = $source;
                break;
            case 'file':
                if ($fp = fopen($source,"r")) {
                    $return = '';
                    while (!feof($fp)) {
                        $return .= fread($fp, 8192);
                    }
                    fclose($fp);
                } else {
                    throw new Exception('Unable to open source file');
                }
                break;
        }
        if (empty($return)) {
            throw new Exception('Empty source');
        }
        return $return;
    }
}

class HtmlParser
{
    protected $htmlcode;
    protected $dom;
    protected $htmlElements;

    /**
     * HtmlParser constructor
     *
     * @param string $source
     * @param string $type
     */
    public function __construct($source, $type = 'url')
    {
        //supress warnings for poorly formated html
        libxml_use_internal_errors(true);
        //Load the html based on type
        $this->htmlcode = HtmlBuilder::build($source, $type);
        $this->htmlcode = mb_convert_encoding($this->htmlcode, 'HTML-ENTITIES', "UTF-8");

        $this->dom = new DOMDocument();
        $this->dom->loadHTML($this->htmlcode);


    }

    /**
     * getElements method - get element collection found in the source
     *
     * @param string $elementCode
     * @return string
     */
    public function getElements($elementCode)
    {
        $this->htmlElements = new HtmlElements();
        $elements = $this->dom->getElementsByTagName($elementCode);
        foreach ($elements as $element) {
            $this->htmlElements->addChild($element);
        }
        return $this->htmlElements;
    }
}

class HtmlElements
{
    protected $elements = array();

    /**
     * addChild elements to the collection
     *
     * @param DOMElement $elementCode
     */
    public function addChild($element)
    {
        
        $this->elements[] = $element;
    }

    /**
     * getElements method - get element collection found in the source
     *
     * @return int
     */
    public function count()
    {
        return count($this->elements);
    }
}
try {
    
    $dom = new HtmlParser('http://www.bbc.com/');
    $elements = $dom->getElements('div');
    echo $elements->count();
} catch (Exception $e){
    echo $e->getMessage();
}
?>