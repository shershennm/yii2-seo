# yii2-seo

Yii2 module for easy creating meta tags 

# Usage
in config file:
```sh
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
seo controller example:
```sh
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
        
        return [
            ['name' => 'keywords', 'content' => $this->getKeywords()], // params for Html::tag('meta', '', $params)
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