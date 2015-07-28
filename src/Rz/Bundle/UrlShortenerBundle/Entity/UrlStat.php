<?php
namespace Rz\Bundle\UrlShortenerBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="Rz\Bundle\UrlShortenerBundle\Entity\Repository\UrlStatRepository")
 * @ORM\Table(
 *   name="url_stat"
 * )
 */
class UrlStat
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="Url", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="url_id", referencedColumnName="id")
     */
    private $Url;

    /**
     * IP Address
     *
     * @var string
     *
     * @Assert\NotBlank(
     *   message="IP should not be blank"
     * )
     *
     * @ORM\Column(type="string", length=100, nullable=false)
     */
    private $ip;

    /**
     * User Agent
     *
     * @var string
     *
     * @ORM\Column(type="string", length=2000, nullable=true)
     */
    private $userAgent;

    /**
     * @var \DateTime $created
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $created;

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
     * @return string
     */
    public function getUrl()
    {
        return $this->Url;
    }

    /**
     * @param string $Url
     */
    public function setUrl($Url)
    {
        $this->Url = $Url;
    }

    /**
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param \DateTime $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @param string $ip
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
    }

    /**
     * @return string
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * @param string $userAgent
     */
    public function setUserAgent($userAgent)
    {
        $this->userAgent = $userAgent;
    }

    public function toArray()
    {
        return [
            'id' => $this->getId(),
            'ip' => $this->getIp(),
            'user-agent' => $this->getUserAgent(),
            'created' => $this->getCreated()
        ];
    }
} 