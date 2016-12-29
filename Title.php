<?php

namespace shershennm\seo;

use Yii;

use yii\base\Model;


class Title extends Model
{
    public
        $append,
        $prepend,
        $title,
        $defaultTitle,
        $defaultPrepend,
        $defaultAppend;

    public function buildTitle()
    {
        return sprintf('%s%s%s', $this->getPrepend(), $this->getTitle(), $this->getAppend());
    }

    public function getTitle()
    {
        if ($this->title === null)
        {
            if ($this->defaultTitle !== null)
            {
                return $this->defaultTitle;
            }

            return '';
        }

        return $this->title;
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