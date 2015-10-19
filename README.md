# yii2-seo

Yii2 module for easy creating meta tags 

# Usage
in config file:
```sh
'components' => [
	...
    'view'         => [
        'class' => 'shershennm\seo\CustomView',
        'controllerNamespace' => \\seo controllers namespace
    ],
    ...
]
```
seo controller example:
```sh
class AppController
{
	public function actionIndex()
	{
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