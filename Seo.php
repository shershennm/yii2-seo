<?php

namespace shershennm\seo;

use Yii;
use yii\base\ActionEvent;
use yii\base\Component;
use yii\base\ViewEvent;
use yii\web\Controller;
use yii\web\View;

class Seo extends Component
{
    /**
     * controller namespace => seo controller namespace.
     *
     * @var array
     */
    public $controllerMapping;

    /**
     * @var string|null
     */
    public $defaultTitle;

    /**
     * @var string|null
     */
    public $titleAppend;

    /**
     * @var string|null
     */
    public $titlePrepend;

    /**
     * @var \ReflectionClass
     */
    private $_reflectionController;

    /**
     * @return Controller
     */
    public function getController()
    {
        return Yii::$app->controller;
    }

    /**
     * @return \ReflectionClass
     */
    public function getReflectionController()
    {
        if ($this->_reflectionController === null) {
            $this->_reflectionController = $this->buildReflectionController();
        }

        return $this->_reflectionController;
    }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->initEventBranches();

        parent::init();
    }

    private function initEventBranches()
    {
        ActionEvent::on(Controller::className(), Controller::EVENT_BEFORE_ACTION, [$this, 'eventControllerBeforeAction']);
    }

    /**
     * @param $event ActionEvent
     */
    public function eventControllerBeforeAction($event)
    {
        ActionEvent::off(Controller::className(), Controller::EVENT_BEFORE_ACTION, [$this, 'eventControllerBeforeAction']);
        $event->sender->view->on(View::EVENT_BEFORE_RENDER, [$this, 'eventViewBeforeRender']);
    }

    /**
     * @param $event ViewEvent
     *
     * @throws \yii\base\InvalidConfigException
     */
    public function eventViewBeforeRender($event)
    {
        $event->sender->off(View::EVENT_BEFORE_RENDER, [$this, 'eventViewBeforeRender']);

        $this->setMeta($event);
    }

    /**
     * @param $viewEvent ViewEvent
     *
     * @throws \yii\base\InvalidConfigException
     */
    public function setMeta($viewEvent)
    {
        $set = false;

        if ($this->isValidController() && $this->isSeoControllerInMapping() && $this->isSeoControllerExists()) {
            $set = $this->executeSeoControllerAction($viewEvent);
        }

        if ($set === false) {
            $this->addTitle($viewEvent->sender);
        }
    }

    /**
     * @param $viewEvent ViewEvent
     *
     * @return bool
     *
     * @throws \yii\base\InvalidConfigException
     */
    private function executeSeoControllerAction($viewEvent)
    {
        $seoController = Yii::createObject($this->buildSeoControllerClassName());

        if (!property_exists($this->controller->action, 'actionMethod')) {
            return false;
        }

        $actionMethod = $this->controller->action->actionMethod;

        if (!method_exists($seoController, $actionMethod)) {
            return false;
        }

        $seoController->controller = $this->controller;
        $seoController->view = $viewEvent->sender;

        $meta = $seoController->$actionMethod($viewEvent->params);

        $this->addMeta($viewEvent->sender, $meta);
        $this->addTitle($viewEvent->sender, $seoController->title);

        return true;
    }

    /**
     * @param $title string
     *
     * @return string
     */
    protected function buildTitle($title)
    {
        $defaults = [
            'defaultTitle' => $this->defaultTitle,
            'defaultPrepend' => $this->titlePrepend,
            'defaultAppend' => $this->titleAppend,
        ];

        if (is_array($title)) {
            $title = new Title(array_merge($title, $defaults));
        } else {
            $title = new Title(array_merge(['title' => $title], $defaults));
        }

        return $title->buildTitle();
    }

    /**
     * @param $view View
     * @param $metaArray array
     */
    public function addMeta($view, $metaArray)
    {
        foreach ($metaArray as $meta) {
            $view->registerMetaTag($meta);
        }
    }

    /**
     * @param $view View
     * @param $title null|string
     */
    public function addTitle($view, $title = null)
    {
        $view->title = $this->buildTitle($title);
    }

    /**
     * @return \ReflectionClass
     */
    private function buildReflectionController()
    {
        return new \ReflectionClass($this->controller);
    }

    /**
     * @return bool
     */
    private function isSeoControllerInMapping()
    {
        return isset($this->controllerMapping[$this->reflectionController->getNamespaceName()]);
    }

    /**
     * @return bool
     */
    private function isSeoControllerExists()
    {
        return class_exists($this->buildSeoControllerClassName());
    }

    /**
     * @return bool
     */
    private function isValidController()
    {
        return Yii::$app->controller !== null && Yii::$app->controller->action->className() !== 'yii\web\ErrorAction';
    }

    /**
     * @return string
     */
    private function buildSeoControllerClassName()
    {
        return sprintf('%s\%s', $this->controllerMapping[$this->reflectionController->getNamespaceName()], $this->reflectionController->getShortName());
    }
}
