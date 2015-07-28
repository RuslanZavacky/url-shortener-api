<?php

namespace Rz\Bundle\UrlShortenerBundle\Features\Context;

use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

use Behat\Mink\Session;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use Behat\Symfony2Extension\Driver\KernelDriver;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

class ApiContext extends RawMinkContext implements SnippetAcceptingContext, KernelAwareContext
{
    /**
     * @var Response
     */
    private $lastResponse;

    public function __construct()
    {
    }

    /**
     * Kernel
     *
     * @var Kernel
     */
    protected $kernel;

    /**
     * Sets HttpKernel instance.
     * This method will be automatically called by Symfony2Extension ContextInitializer.
     *
     * @param KernelInterface $kernel kernel
     * @return void
     */
    public function setKernel(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @param string $url
     * @Given I clean up records with URL :url
     */
    public function iCleanUpRecordsWithUrl($url)
    {
        if ($record = $this->getUrlRepository()->findOneBy([ 'originalUrl' => $url])) {
            $em = $this->getEntityManager();
            $em->remove($record);
            $em->flush();
        }
    }

    /**
     * @Given /^response item "([^"]*)" should (?P<not>|not )contain "([^"]*)"$/
     */
    public function responseItemShouldContain($propertyPathKey, $not = '', $expectedValue)
    {
        $shouldNotExist = !empty($not);

        $content = $this->decodeJson($this->lastResponse->getContent());
        $propertyPath = PropertyAccess::createPropertyAccessor();
        $value = $propertyPath->getValue($content, $propertyPathKey);
        $value = is_array($value) ? array_keys($value) : $value;
        $value = is_array($value) ? $value[0] : $value;

        if ($shouldNotExist) {
            \PHPUnit_Framework_Assert::assertNotContains($expectedValue, (string) $value);
        } else {
            \PHPUnit_Framework_Assert::assertContains($expectedValue, (string) $value);
        }

    }

    /**
     * @Given /^response should (?P<not>|not )contain "([^"]*)"$/
     */
    public function responseShouldContain($not = '', $expectedValue)
    {
        $shouldNotExist = !empty($not);

        $value = $this->decodeJson($this->lastResponse->getContent());
        $value = is_array($value) ? array_keys($value) : $value;

        if ($shouldNotExist) {
            \PHPUnit_Framework_Assert::assertNotContains($expectedValue, $value);
        } else {
            \PHPUnit_Framework_Assert::assertContains($expectedValue, $value);
        }

    }

    /**
     *
     * @Given /^response item "([^"]*)" should be array with (\d+) items$/
     */
    public function responseItemShouldArrayWithItems($propertyPathKey, $count)
    {
        $content = $this->decodeJson($this->lastResponse->getContent());
        $propertyPath = PropertyAccess::createPropertyAccessor();
        $value = $propertyPath->getValue($content, $propertyPathKey);
        \PHPUnit_Framework_Assert::assertTrue(is_array($value));
        \PHPUnit_Framework_Assert::assertTrue(count($value) == $count);
    }

    /**
     * Sending request, replace aliases
     *
     * @param string $method
     * @param string $uri
     * @param array $server
     * @param null $content
     * @return null|object
     * @throws \RuntimeException
     * @throws \Behat\Mink\Exception\UnsupportedDriverActionException
     */
    private function request($method, $uri, array $server = array(), $content = null)
    {

        /** @var KernelDriver $driver */
        $driver = $this->getSession()->getDriver();

        $client = $driver->getClient();
        $client->getCookieJar()->clear();

        if (in_array($method, array('PUT', 'POST', 'PATCH', 'DELETE'))) {
            $server['Content-Type'] = 'application/x-www-form-urlencoded';
        }

        $client->request($method, $uri, array(), array(), $server, $content, false);

        $this->lastResponse = $client->getResponse();
    }

    /**
     * @Given /^(?:|I )send "(?P<method>[^"]*)" request to "(?P<uri>[^"]*)" with content:$/
     */
    public function iSendRequestToWithContent($method, $uri, $node)
    {
        $this->request(strtoupper($method), $this->locatePath($uri), array(), $node);
    }

    /**
     * Decoding JSON
     *
     * @param $json
     *
     * @return mixed
     * @throws \RuntimeException
     */
    private function decodeJson($json)
    {
        if (empty($json)) {
            throw new \RuntimeException('JSON can not be an empty string');
        }

        if (!$content = json_decode($json, true)) {
            throw new \RuntimeException(
                sprintf('Failed to parse JSON. Error: %s; Content: %s', $this->jsonErrorToText(), $json)
            );
        }

        return $content;
    }

    /**
     * JSON Error Code to Text
     *
     * @return string
     */
    private function jsonErrorToText()
    {
        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                return 'No errors';
            case JSON_ERROR_DEPTH:
                return 'Maximum stack depth exceeded';
            case JSON_ERROR_STATE_MISMATCH:
                return 'Underflow or the modes mismatch';
            case JSON_ERROR_CTRL_CHAR:
                return 'Unexpected control character found';
            case JSON_ERROR_SYNTAX:
                return 'Syntax error, malformed JSON';
            case JSON_ERROR_UTF8:
                return 'Malformed UTF-8 characters, possibly incorrectly encoded';
            default:
                return 'Unknown error';
        }
    }

    /**
     * @return ContainerInterface
     */
    private function getContainer()
    {
        return $this->kernel->getContainer();
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    private function getUrlRepository()
    {
        return $this->getEntityManager()->getRepository('Rz\Bundle\UrlShortenerBundle\Entity\Url');
    }

    /**
     * @return EntityManager
     */
    private function getEntityManager()
    {
        return $this->getContainer()->get('doctrine.orm.default_entity_manager');
    }
}
