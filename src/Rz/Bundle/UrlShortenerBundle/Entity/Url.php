<?php

namespace Rz\Bundle\UrlShortenerBundle\Entity;

use Ecentria\Libraries\EcentriaRestBundle\Entity\AbstractCrudEntity;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\Table(
 *   name="url",
 *   indexes={
 *     @ORM\Index(name="code_idx", columns={"code"})
 *   }
 * )
 */
class Url extends AbstractCrudEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * If URL have query parameters, they are going to be sorted alphabetically,
     * so we always have the same URL even if parameters are in the wrong order.
     *
     * @var string
     *
     * @Assert\NotBlank(
     *   message="URL should not be blank"
     * )
     *
     * @JMS\Type("string")
     *
     * @ORM\Column(type="string", length=4000, unique=false, nullable=false)
     */
    private $url;

    /**
     * Original Url
     *
     * @var string
     *
     * @Assert\NotBlank(
     *   message="URL should not be blank"
     * )
     *
     * @JMS\Type("string")
     *
     * @ORM\Column(type="string", length=4000, unique=false, nullable=false)
     */
    private $originalUrl;

    /**
     * @var array
     *
     * @JMS\Type("array")
     *
     * @ORM\Column(name="data", type="json_array", nullable=true)
     */
    private $data;

    /**
     * @var array
     *
     * @ORM\Column(type="json_array", nullable=true)
     *
     * @JMS\Type("array")
     */
    private $queryParam;

    /**
     * @var string
     *
     * @JMS\Type("string")
     *
     * @ORM\Column(type="string", length=128, unique=true, nullable=true)
     */
    private $code;

    /**
     * @var string
     *
     * @JMS\Type("string")
     *
     * @ORM\Column(type="string", length=200, unique=true, nullable=true)
     */
    private $shortUrl;

    /**
     * @var integer
     *
     * @JMS\Type("integer")
     *
     * @ORM\Column(type="integer", unique=false, nullable=false)
     */
    private $sequence = 1;

    /**
     * @var \DateTime $created
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @var \DateTime $updated
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    private $updated;

    /**
     * @var integer
     *
     * @JMS\Type("integer")
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    private $redirectCount = 0;

    /**
     * @var integer
     *
     * @JMS\Type("integer")
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    private $uniqueRedirectCount = 0;

    /**
     * @var \DateTime $updated
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastRedirectOn;

    /**
     * If new, than it never existed in database before.
     * Set to false otherwise.
     *
     * @var bool
     */
    private $new = false;

    /**
     * Get Primary Key
     *
     * @return integer
     */
    public function getPrimaryKey()
    {
        return $this->getId();
    }

    /**
     * Ids getter
     *
     * @return array
     */
    public function getIds()
    {
        return array(
            'id'  => $this->getId()
        );
    }

    public function setIds($ids)
    {
        return $this;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param $url
     * @return $this
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param data
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param $code
     * @return $this
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @return string
     */
    public function getShortUrl()
    {
        return $this->shortUrl;
    }

    /**
     * @param string $shortUrl
     */
    public function setShortUrl($shortUrl)
    {
        $this->shortUrl = $shortUrl;
    }

    /**
     * @return int
     */
    public function getSequence()
    {
        return $this->sequence;
    }

    /**
     * @param int $sequence
     */
    public function setSequence($sequence)
    {
        $this->sequence = $sequence;
    }

    /**
     * @return bool
     */
    public function isDefaultSequence()
    {
        return $this->sequence === 1;
    }

    /**
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @return array
     */
    public function getQueryParam()
    {
        return $this->queryParam;
    }

    /**
     * @param array $queryParam
     */
    public function setQueryParam($queryParam)
    {
        $this->queryParam = $queryParam;
    }

    /**
     * @return string
     */
    public function getOriginalUrl()
    {
        return $this->originalUrl;
    }

    /**
     * @param string $originalUrl
     */
    public function setOriginalUrl($originalUrl)
    {
        $this->originalUrl = $originalUrl;
    }

    /**
     * @return boolean
     */
    public function isNew()
    {
        return $this->new;
    }

    /**
     * @param boolean $new
     */
    public function setNew($new)
    {
        $this->new = $new;
    }

    /**
     * @return int
     */
    public function getRedirectCount()
    {
        return $this->redirectCount;
    }

    /**
     * @param int $redirectCount
     */
    public function setRedirectCount($redirectCount)
    {
        $this->redirectCount = $redirectCount;
    }

    /**
     * @return int
     */
    public function getUniqueRedirectCount()
    {
        return $this->uniqueRedirectCount;
    }

    /**
     * @param int $uniqueRedirectCount
     */
    public function setUniqueRedirectCount($uniqueRedirectCount)
    {
        $this->uniqueRedirectCount = $uniqueRedirectCount;
    }

    public function incrementUniqueRedirectCount()
    {
        $this->uniqueRedirectCount++;
    }

    public function incrementRedirectCount()
    {
        $this->redirectCount++;
    }

    /**
     * @return \DateTime
     */
    public function getLastRedirectOn()
    {
        return $this->lastRedirectOn;
    }

    /**
     * @param \DateTime $lastRedirectOn
     */
    public function setLastRedirectOn($lastRedirectOn)
    {
        $this->lastRedirectOn = $lastRedirectOn;
    }

    public function toArray()
    {
        return [
            'short_url' => $this->getShortUrl(),
            'code' => $this->getCode(),
            'url' => $this->getUrl(),
            'data' => $this->getData() ? $this->getData() : null,
            'query_param' => $this->getQueryParam() ? $this->getQueryParam() : null
        ];
    }
}