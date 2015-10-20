<?php

namespace shershennm\seo;

use Yii;
use yii\base\Object;


class Title extends Object
{
	public
		$append,
		$prepend,
		$title,
		$defaultPrepend,
		$defaultAppend;

	public function buildTitle()
	{
		return sprintf('%s%s%s', $this->getPrepend(), $this->title, $this->getAppend());
	}

	public function getPrepend()
	{
		if ($this->prepend === null)
		{
			$this->prepend = ($this->defaultPrepend === null) ? '' : $this->defaultPrepend;
		}
		elseif ($this->prepend === false)
		{
			$this->prepend = '';
		}

		return $this->prepend;
	}

	public function getAppend()
	{
		if ($this->append === null)
		{
			$this->append = ($this->defaultAppend === null) ? '' : $this->defaultAppend;
		}
		elseif ($this->append === false)
		{
			$this->append = '';
		}

		return $this->append;
	}

}