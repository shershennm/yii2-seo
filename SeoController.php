<?php

namespace shershennm\seo;

use Yii;
use yii\base\Object;

abstract class SeoController extends Object
{
    /**
     * @var $title string Page <title> tag value
     */
	public $title;

    /**
     * @var $controller \yii\web\Controller Web Controller instance
     */
	public $controller;
}