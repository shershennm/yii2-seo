<?php

namespace shershennm\seo;

use Yii;
use yii\web\View;
use yii\helpers\Html;
use shershennm\seo\Title;

/**
* 
*/
class CustomView extends View
{
	public
		$controllerNamespace,

		$titleAppend,
		$titlePrepend;

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

				if ($object->title !== null)
				{
					$this->title = $this->buildTitle($object->title);
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
			$this->metaTags[] = Html::tag('meta', '', $meta);
		}
	}

	private function buildControllerClass()
	{
		preg_match('/\\w*$/', $this->controller->className(), $result);
		$className = $result[0];
		return sprintf('%s\%s', $this->controllerNamespace, $className);
	}

	private function buildActionFunction()
	{
		return sprintf('action%s', ucfirst($this->controller->action->id));
	}
}