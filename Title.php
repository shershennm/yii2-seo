<?php

namespace shershennm\seo;

use yii\base\Model;

class Title extends Model
{
    /**
     * @var string|null
     */
    public $append;

    /**
     * @var string|null
     */
    public $prepend;

    /**
     * @var string
     */
    public $title;

    /**
     * @var string|null
     */
    public $defaultTitle;

    /**
     * @var string|null
     */
    public $defaultPrepend;

    /**
     * @var string|null
     */
    public $defaultAppend;

    /**
     * @return string
     */
    public function buildTitle()
    {
        return sprintf('%s%s%s', $this->getPrepend(), $this->getTitle(), $this->getAppend());
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        if ($this->title === null) {
            if ($this->defaultTitle !== null) {
                return $this->defaultTitle;
            }

            return '';
        }

        return $this->title;
    }

    /**
     * @return string
     */
    public function getPrepend()
    {
        if ($this->prepend === null) {
            $this->prepend = ($this->defaultPrepend === null) ? '' : $this->defaultPrepend;
        } elseif ($this->prepend === false) {
            $this->prepend = '';
        }

        return $this->prepend;
    }

    /**
     * @return string
     */
    public function getAppend()
    {
        if ($this->append === null) {
            $this->append = ($this->defaultAppend === null) ? '' : $this->defaultAppend;
        } elseif ($this->append === false) {
            $this->append = '';
        }

        return $this->append;
    }
}
