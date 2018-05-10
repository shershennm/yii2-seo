<?php

namespace shershennm\seo;

use yii\base\BaseObject;

abstract class SeoController extends BaseObject
{
    /**
     * @var string Page <title> tag value
     */
    public $title;

    /**
     * @var \yii\web\Controller Web Controller instance
     */
    public $controller;

    /**
     * @var \yii\web\View Controller View
     */
    public $view;

    /**
     * Register <meta> tag in current view.
     *
     * @param array $options params for View::registerMetaTag method
     */
    public function registerMetaTag($options)
    {
        return $this->view->registerMetaTag($options);
    }

    /**
     * Register <link> tag in current view.
     *
     * @param array $options params for View::registerLinkTag method
     */
    public function registerLinkTag($options)
    {
        return $this->view->registerLinkTag(array_merge([
            'type' => null,
        ], $options));
    }
}
