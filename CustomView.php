<?php

namespace shershennm\seo;

use Yii;
use yii\web\View;
use yii\helpers\Html;

/**
* 
*/
class CustomView extends View
{
	public
		$controllerNamespace;

	private
		$_controller;

	public function getController()
	{
		if ($this->_controller === null)
		{
			$this->_controller = Yii::$app->controller;
		}

		return $this->_controller;
	}
	
	public function init()
	{
		parent::init();
		$this->on(self::EVENT_BEGIN_PAGE, [$this, 'eventSetMeta']);
	}

	public function eventSetMeta()
	{
		$controllerName = $this->buildControllerClass();
		$actionName = $this->buildActionFunction();

		if (class_exists($controllerName))
		{
			$object = new $controllerName;

			if (method_exists($object, $actionName))
			{
				$object->controller = $this->controller;
				$meta = $object->$actionName();

				$this->addMeta($meta);
			}
		}
	}

	private function addMeta($metaArray)
	{
		foreach ($metaArray as $meta) {
			$this->metaTags[] = Html::tag('meta', '', $meta);
		}
	}

	private function buildControllerClass()
	{
		return sprintf('%s\%sController', $this->controllerNamespace, ucfirst($this->controller->id));
	}

	private function buildActionFunction()
	{
		return sprintf('action%s', ucfirst($this->controller->action->id));
	}
}