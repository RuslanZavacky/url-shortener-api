<?php

namespace Rz\Bundle\UrlShortenerBundle\Model;

use Ecentria\Libraries\EcentriaRestBundle\Validator\Constraints as EcentriaAssert;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;

class StatisticsCollectionParameters
{
   /**
     * Current page
     *
     * @var int
     *
     * @JMS\Type("integer")
     */
    public $page = 1;

    /**
     * Max number of contact to show per page
     *
     * @var int
     *
     * @JMS\Type("integer")
     */
    public $limit = 20;

    public function getPage()
    {
        return $this->page;
    }

    public function setPage($page)
    {
        $this->page = $page;
        return $this;
    }

    public function getLimit()
    {
        return $this->limit;
    }

    public function setLimit($limit)
    {
        $this->limit = $limit;
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
        return ceil($count / $this->getLimit());
    }
}
