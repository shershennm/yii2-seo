<?php

namespace shershennm\seo;

use Yii;

abstract class OnePageSeoController extends SeoController
{
    /**
     * @var array Array of routes with titles
     */
    protected $titles = [];

    /**
     * @var array Array of routes with titles. Routes are regular expressions in this case
     */
    protected $wildcardTitles = [];

    /**
     * Default action for one page controller.
     *
     * @return array
     */
    public function actionIndex()
    {
        $this->setTitle(Yii::$app->request->pathInfo);

        return [];
    }

    /**
     * @param $route
     */
    protected function setTitle($route)
    {
        if (isset($this->titles[$route])) {
            $this->title = $this->titles[$route];

            return;
        }

        foreach ($this->wildcardTitles as $wildcardRoute => $wildcardTitle) {
            if (preg_match($wildcardRoute, $route)) {
                $this->title = $wildcardTitle;

                return;
            }
        }
    }
}
