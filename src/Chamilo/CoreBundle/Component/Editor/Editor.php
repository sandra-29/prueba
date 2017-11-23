<?php
/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Component\Editor;

use Chamilo\CoreBundle\Framework\Template;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\Translator;

/**
 * Class Editor
 * @package Chamilo\CoreBundle\Component\Editor
 */
class Editor
{
    /**
     * Name of the instance.
     *
     * @access protected
     * @var string
     */
    public $name;

    /**
     * Name of the toolbar to load.
     *
     * @var string
     */
    public $toolbarSet;

    /**
     * Initial value.
     *
     * @var string
     */
    public $value;

    /**
     * @var array
     */
    public $config;

    /** @var Translator */
    public $translator;

    /** @var RouterInterface */
    public $urlGenerator;

    /** @var \Template */
    public $template;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->toolbarSet = 'Basic';
        $this->value = '';
        $this->config = array();
        $this->setConfigAttribute('width', '100%');
        $this->setConfigAttribute('height', '200');
        $this->setConfigAttribute('fullPage', false);
        /*$this->translator = $translator;
        $this->urlGenerator = $urlGenerator;*/
        //$this->course = $course;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Return the HTML code required to run editor.
     *
     * @return string
     */
    public function createHtml()
    {
        $html = '<textarea id="'.$this->getName().'" name="'.$this->getName().'">'.$this->value.'</textarea>';
        //$html .= $this->editorReplace();
        return $html;
    }

    /**
     * @return string
     */
    public function editorReplace()
    {
        $toolbar = new Toolbar($this->toolbarSet, $this->config);
        $toolbar->setLanguage($this->getLocale());
        $config = $toolbar->getConfig();
        $javascript = $this->toJavascript($config);
        $html = "<script>
           CKEDITOR.replace('".$this->name."',
               $javascript
           );
           
           </script>";

        return $html;
    }

    /**
     * Converts a PHP variable into its Javascript equivalent.
     * The code of this method has been "borrowed" from the function drupal_to_js() within the Drupal CMS.
     * @param mixed $var  The variable to be converted into Javascript syntax
     *
     * @return string    Returns a string
     * Note: This function is similar to json_encode(),
     * in addition it produces HTML-safe strings, i.e. with <, > and & escaped.
     * @link http://drupal.org/
     */
    protected function toJavascript($var)
    {
        switch (gettype($var)) {
            case 'boolean':
                return $var ? 'true' : 'false'; // Lowercase necessary!
            case 'integer':
            case 'double':
                return (string)$var;
            case 'resource':
            case 'string':
                return '"'.str_replace(
                    array("\r", "\n", "<", ">", "&"),
                    array('\r', '\n', '\x3c', '\x3e', '\x26'),
                    addslashes($var)
                ).'"';
             case 'array':
                // Arrays in JSON can't be associative. If the array is empty or if it
                // has sequential whole number keys starting with 0, it's not associative
                // so we can go ahead and convert it as an array.
                if (empty($var) || array_keys($var) === range(0, sizeof($var) - 1)) {
                    $output = array();
                    foreach ($var as $v) {
                        $output[] = $this->toJavascript($v);
                    }

                    return '[ '.implode(', ', $output).' ]';
                }
            case 'object':
                // Otherwise, fall through to convert the array as an object.
                $output = array();
                foreach ($var as $k => $v) {
                    $output[] = $this->toJavascript(strval($k)).': '.$this->toJavascript($v);
                }
                return '{ '.implode(', ', $output).' }';
            default:
                return 'null';
        }
    }

    /**
     * @param string $key
     * @param mixed  $value
     */
    public function setConfigAttribute($key, $value)
    {
        $this->config[$key] = $value;
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function getConfigAttribute($key)
    {
        return isset($this->config[$key]) ? $this->config[$key] : null;
    }

    /**
     * @param array $config
     */
    public function processConfig($config)
    {
        if (is_array($config)) {
            foreach ($config as $key => $value) {
                switch ($key) {
                    case 'ToolbarSet':
                        $this->toolbarSet = $value;
                        break;
                    case 'Config':
                        $this->processConfig($value);
                        break;
                    case 'Width':
                        $this->setConfigAttribute('width', $value);
                        break;
                    case 'Height':
                        $this->setConfigAttribute('height', $value);
                        break;
                    case 'FullPage':
                    case 'fullPage':
                        $this->setConfigAttribute('fullPage', $value);
                        break;
                    default:
                        $this->setConfigAttribute($key, $value);
                        break;
                }
            }
        }
    }

    /**
     * @return null
     */
    public function getEditorTemplate()
    {
        return null;
    }

    /**
     * @return string
     */
    public function getEditorStandAloneTemplate()
    {
        return 'javascript/editor/elfinder_standalone.tpl';
    }

    /**
     * @return null
     */
    public function formatTemplates($templates)
    {
        return null;
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return api_get_language_isocode();
    }
}
