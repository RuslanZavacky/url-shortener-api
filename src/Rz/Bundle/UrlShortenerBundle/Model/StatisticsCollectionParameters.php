<?php

namespace Rz\Bundle\UrlShortenerBundle\Model;

use Doctrine\Common\Util\Inflector;

class StatisticsCollectionParameters
{
   /**
     * Current page
     *
     * @var int
     */
    public $page = 1;

    /**
     * Max number of contact to show per page
     *
     * @var int
     */
    public $limit = 20;

    public function __construct($params = [])
    {
        if (empty($params)) {
            return;
        }

        $inflector = new Inflector();

        foreach ($params as $key => $value) {
            $method = $inflector->camelize('set-' . $key);
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }

    public function getPage()
    {
        return $this->page;
    }

    public function setPage($page)
    {
        $this->page = (int) $page;
        return $this;
    }

    public function getLimit()
    {
        return $this->limit;
    }

    public function setLimit($limit)
    {
        $this->limit = (int) $limit;
        return $this;
    }

    public function getFirstResult()
    {
        $page = $this->page - 1;
        return $page * $this->limit;
    }

    public function getMaxResults()
    {
        return $this->limit;
    }

    public function getPages($count)
    {
        if ($count == 0) {
            return 1;
        }
        return ceil($count / max(1, $this->getLimit()));
    }
}
