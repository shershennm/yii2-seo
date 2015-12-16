# yii2-seo

Yii2 module for easy creating meta tags 

# Usage
in config file:
```sh
'components' => [
	...
    'seo'         => [
        'class' => 'shershennm\seo\Seo',
        'controllerMapping' => [
            'app\controllers' => 'app\seo\controllers', \\controllers for seo module
        ],

    ],
    ...
    'bootstrap' => ['log', 'seo'],
    ...
]
```
seo controller example:
```sh
<?php

namespace app\seo\controllers;

use Yii;
use shershennm\seo\SeoController;

class SiteController extends SeoController
{
	public function actionIndex()
	{
		$this->title = 'Hello world!';

		return [
			['name' => 'keywords', 'value' => $this->getKeywords()],
			['name' => 'description', 'value' => 'Cool page!'],
		];
	}

	private function getKeywords()
	{
		return implode($this->controller->words, ', ');
	}

	....
```