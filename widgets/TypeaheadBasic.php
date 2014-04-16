<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2013
 * @package yii2-widgets
 * @version 1.0.0
 */

namespace kartik\widgets;

use yii\web\View;
use yii\web\JsExpression;
use yii\helpers\Json;
use yii\helpers\Html;
use yii\base\InvalidConfigException;

/**
 * Typeahead widget is a Yii2 wrapper for the Twitter typeahead.js plugin. This
 * input widget is a jQuery based replacement for text inputs providing search
 * and typeahead functionality. It is inspired by twitter.com's autocomplete search
 * functionality and based on Twitter's typeahead.js which Twitter mentions as
 * a fast and fully-featured autocomplete library.
 *
 * This is a basic implementation of typeahead.js without using any suggestion engine.
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 * @see http://twitter.github.com/typeahead.js/examples
 */
class TypeaheadBasic extends InputWidget
{
    /**
     * @var bool whether the dropdown menu is scrollable
     */
    public $scrollable = false;

    /**
     * @var bool whether RTL support is to be enabled
     */
    public $rtl = false;

    /**
     * @var array the HTML attributes for container enclosing the input
     */
    public $container = [];

    /**
     * Runs the widget
     * @return string|void
     * @throws \yii\base\InvalidConfigException
     */
    public function run() {
        if (empty($this->data) || !is_array($this->data)) {
            throw new InvalidConfigException("You must define the 'data' property for Typeahead which must be a single dimensional array.");
        }
        $this->registerAssets();
        $this->initOptions();
        echo Html::tag('div', $this->getInput('textInput'), $this->container);
    }

    /**
     * Initializes options
     */
    protected function initOptions() {
        Html::addCssClass($this->options, 'form-control');
        if ($this->scrollable) {
            Html::addCssClass($this->container, 'tt-scrollable-menu');
        }
        if ($this->rtl) {
            $this->options['dir'] = 'rtl';
            Html::addCssClass($this->container, 'tt-rtl');
        }
    }

    /**
     * Registers the needed assets
     */
    public function registerAssets()
    {
        $view = $this->getView();
        TypeaheadBasicAsset::register($view);
        $this->hashPluginOptions('typeahead');
        if (!empty($this->_encOptions)) {
            $view->registerJs("var {$this->_hashVar} = {$this->_encOptions};\n", $view::POS_HEAD);
        }
        else {
            $view->registerJs("var {$this->_hashVar} = {};\n", $view::POS_HEAD);
        }

        $dataVar = str_replace('-', '_', $this->options['id'] . '_data');
        $view->registerJs('var ' . $dataVar . ' = ' . Json::encode(array_values($this->data)) . ';', View::POS_HEAD);
        $dataset = Json::encode(['name' => $dataVar, 'source' => new JsExpression('substringMatcher(' . $dataVar . ')')]);
        $view->registerJs('$("#' . $this->options['id'] . '").typeahead(' . $this->_hashVar . ',' . $dataset . ');');
    }

}