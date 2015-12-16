<?php

namespace shershennm\seo;

use Yii;
use yii\base\Component;
use yii\helpers\Html;
use shershennm\seo\Title;

/**
* 
*/
class Seo extends Component
{
	public
		$controllerMapping,

		$titleAppend,
		$titlePrepend;

	private
		$_controller,
		$_reflectionController;

	public function getController()
	{
		if ($this->_controller === null)
		{
			$this->_controller = Yii::$app->controller;
		}

		return $this->_controller;
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
		parent::init();

		if (Yii::$app->view !== null)
		{
			Yii::$app->view->on(yii\web\View::EVENT_BEGIN_PAGE, [$this, 'eventSetMeta']);
		}
	}

	public function eventSetMeta()
	{
		if ($this->isValidController() && $this->isSeoControllerInMapping() && $this->isSeoControllerExists())
		{
			$seoController = Yii::createObject($this->buildSeoControllerClassName());
			$actionMethod = $this->controller->action->actionMethod;

			if (method_exists($seoController, $actionMethod))
			{
				$seoController->controller = $this->controller;

				$meta = $seoController->$actionMethod();
				$this->addMeta($meta);

				if ($seoController->title !== null)
				{
					Yii::$app->view->title = $this->buildTitle($seoController->title);
				}
			}
		}
	}

	private function buildTitle($title)
	{
		$defaults = [
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

	private function addMeta($metaArray)
	{
		foreach ($metaArray as $meta) {
			Yii::$app->view->metaTags[] = Html::tag('meta', '', $meta);
		}
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

	private function buildActionFunction()
	{
		return sprintf('action%s', ucfirst($this->controller->action->id));
	}
}