<?php

namespace shershennm\seo;

use Yii;

use yii\base\ActionEvent;
use yii\base\Component;
use yii\base\ViewEvent;

use yii\web\Controller;
use yii\web\View;

use yii\helpers\Html;

use shershennm\seo\Title;

/**
 *
 */
class Seo extends Component
{
    public
        $controllerMapping,

        $defaultTitle,
        $titleAppend,
        $titlePrepend;

    private
        $_reflectionController;

    public function getController()
    {
        return Yii::$app->controller;
    }

    public function getReflectionController()
    {
        if ($this->_reflectionController === null)
        {
            $this->_reflectionController = $this->buildReflectionController();
        }

        return $this->_reflectionController;
    }

    public function init()
    {
        $this->initEventBranches();

        parent::init();
    }

    private function initEventBranches()
    {
        ActionEvent::on(Controller::className(), Controller::EVENT_BEFORE_ACTION, [$this, 'eventControllerBeforeAction']);
    }

    public function eventControllerBeforeAction($event)
    {
        ActionEvent::off(Controller::className(), Controller::EVENT_BEFORE_ACTION, [$this, 'eventControllerBeforeAction']);

        $event->sender->view->on(View::EVENT_BEFORE_RENDER, [$this, 'eventViewBeforeRender']);
    }

    public function eventViewBeforeRender($event)
    {
        $event->sender->off(View::EVENT_BEFORE_RENDER, [$this, 'eventViewBeforeRender']);

        $this->setMeta($event);
    }

    public function setMeta($viewEvent)
    {
        $set = false;

        if ($this->isValidController() && $this->isSeoControllerInMapping() && $this->isSeoControllerExists())
        {
            $set = $this->executeSeoControllerAction($viewEvent);
        }

        if ($set === false)
        {
            $this->addTitle($viewEvent->sender);
        }
    }

    private function executeSeoControllerAction($viewEvent)
    {
        $seoController = Yii::createObject($this->buildSeoControllerClassName());
        $actionMethod = $this->controller->action->actionMethod;

        if (method_exists($seoController, $actionMethod))
        {
            $seoController->controller = $this->controller;

            $meta = $seoController->$actionMethod($viewEvent->params);

            $this->addMeta($viewEvent->sender, $meta);
            $this->addTitle($viewEvent->sender, $seoController->title);

            return true;
        }

        return false;
    }

    private function buildTitle($title)
    {
        $defaults = [
            'defaultTitle' => $this->defaultTitle,
            'defaultPrepend' => $this->titlePrepend,
            'defaultAppend' => $this->titleAppend,
        ];

        if(is_array($title)) {
            $title = new Title(array_merge($title, $defaults));
        }
        else
        {
            $title = new Title(array_merge(['title' => $title], $defaults));
        }

        return $title->buildTitle();
    }

    public function addMeta($view, $metaArray)
    {
        foreach ($metaArray as $meta) {
            $view->metaTags[] = Html::tag('meta', '', $meta);
        }
    }

    public function addTitle($view, $title = null)
    {
        $view->title = $this->buildTitle($title);
    }

    private function buildReflectionController()
    {
        return (new \ReflectionClass($this->controller));
    }

    private function isSeoControllerInMapping()
    {
        return isset($this->controllerMapping[$this->reflectionController->getNamespaceName()]);
    }

    private function isSeoControllerExists()
    {
        return class_exists($this->buildSeoControllerClassName());
    }

    private function isValidController()
    {
        return (Yii::$app->controller !== null && Yii::$app->controller->action->className() !== 'yii\web\ErrorAction');
    }

    private function buildSeoControllerClassName()
    {
        return sprintf('%s\%s', $this->controllerMapping[$this->reflectionController->getNamespaceName()], $this->reflectionController->getShortName());
    }
}