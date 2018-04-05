# yii2-seo

Yii2 module for easy creating meta tags

# Installation

Yii <= 2.0.12

`composer require shershennm/yii2-seo:"^2.0"`

Yii >= 2.0.13

`composer require shershennm/yii2-seo:"^3.0"`
# Usage
In config file:
```php
<?php
...
'bootstrap' => ['log', 'seo'], // add seo component to application bootstrap
...
'components' => [
	...
    'seo'         => [
        'class' => 'shershennm\seo\Seo',
        'controllerMapping' => [
            'app\controllers' => 'app\seo\controllers', // controller namespace for seo module
        ],

    ],
]
```
Seo controller example:
```php
<?php

namespace app\seo\controllers;

use Yii;
use shershennm\seo\SeoController;

class SiteController extends SeoController
{
    /**
    * $viewParams array View Params from actionIndex in SiteController
    **/
    public function actionIndex($viewParams)
    {
        $this->title = 'Hello world!';

        $this->registerMetaTag(['name' => 'description', 'content' => 'Cool page!']);
        $this->registerLinkTag([['rel' => 'next', 'href' => 'https://my-cool-page.lh/article/2']]);

        return [
            ['name' => 'keywords', 'content' => $this->getKeywords()], // params for View::registerMetaTag() function
            ['name' => 'description', 'content' => 'Cool page!'],
        ];
    }

    private function getKeywords()
    {
        // $this->controller instance of current controller
        return implode($this->controller->words, ', ');
    }

	....
```
You can use ```OnePagSeoController``` for controller which have ```index``` action for different routes. Example:
```php
<?php

namespace frontend\seo\controllers;

use shershennm\seo\OnePageSeoController;

class SiteController extends OnePageSeoController
{
    protected $titles = [
        'site/info' => 'Site Info',
    ];
    protected $wildcardTitles = [
        '/site\/history/' => 'Site History',
    ];
}
```
Route of ```$titles``` will be applied only to pages with same route. ```$wildcardTitles``` use regular expression as route.
