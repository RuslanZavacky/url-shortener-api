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
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->kernel->getContainer()->get('doctrine.orm.default_entity_manager');
    }

    /**
     * @param string $url
     * @Given I clean up records with URL :url
     */
    public function iCleanUpRecordsWithUrl($url)
    {
        $em = $this->getEntityManager();

        $repository = $this->getEntityManager()->getRepository('Rz\Bundle\UrlShortenerBundle\Entity\Url');

        $record = $repository->findOneBy([ 'originalUrl' => $url]);

        if ($record) {
            $em->remove($record);
            $em->flush();
        }
    }

//    /**
//     * @Given /^response item "([^"]*)" should (?P<not>|not )exist$/
//     */
//    public function responseItemShouldExist($propertyPathKey, $not = '')
//    {
//        $shouldNotExist = !empty($not);
//        $content = $this->decodeJson($this->lastResponse->getContent());
//        $propertyPath = PropertyAccess::createPropertyAccessor();
//        if ($shouldNotExist) {
//            \PHPUnit_Framework_Assert::assertNull($propertyPath->getValue($content, $propertyPathKey));
//        } else {
//            \PHPUnit_Framework_Assert::assertNotNull($propertyPath->getValue($content, $propertyPathKey));
//        }
//    }
//
//    /**
//     * Response Item Should be equal to
//     *
//     * @param string $propertyPathKey Property Path Key
//     * @param string $not             Not or not not
//     * @param string $expectedValue   What we expect to get
//     *
//     * @Given /^response item "([^"]*)" should (?P<not>|not )be equal to "([^"]*)"$/
//     */
//    public function responseItemShouldBeEqualTo($propertyPathKey, $not = '', $expectedValue)
//    {
//        $shouldNotExist = !empty($not);
//        $this->getFeatureContext()->processAliases($expectedValue);
//
//        $content = $this->decodeJson($this->lastResponse->getContent());
//        $propertyPath = PropertyAccess::createPropertyAccessor();
//        $value = $propertyPath->getValue($content, $propertyPathKey);
//        $value = is_array($value) ? array_keys($value) : $value;
//        $value = is_array($value) ? $value[0] : $value;
//
//        if ($shouldNotExist) {
//            \PHPUnit_Framework_Assert::assertNotSame($expectedValue, (string) $value);
//        } else {
//            \PHPUnit_Framework_Assert::assertSame($expectedValue, (string) $value);
//        }
//
//    }
//
//    /**
//     * @Given /^response item "([^"]*)" should (?P<not>|not )contain "([^"]*)"$/
//     */
//    public function responseItemShouldContain($propertyPathKey, $not = '', $expectedValue)
//    {
//        $shouldNotExist = !empty($not);
//        $this->getFeatureContext()->processAliases($expectedValue);
//
//        $content = $this->decodeJson($this->lastResponse->getContent());
//        $propertyPath = PropertyAccess::createPropertyAccessor();
//        $value = $propertyPath->getValue($content, $propertyPathKey);
//        $value = is_array($value) ? array_keys($value) : $value;
//        $value = is_array($value) ? $value[0] : $value;
//
//        if ($shouldNotExist) {
//            \PHPUnit_Framework_Assert::assertNotContains($expectedValue, (string) $value);
//        } else {
//            \PHPUnit_Framework_Assert::assertContains($expectedValue, (string) $value);
//        }
//
//    }
//
//    /**
//     * @Given /^response item "([^"]*)" should contain current date "([^"]*)"$/
//     *
//     * @return void
//     */
//    public function responseItemShouldContainCurrentDate($propertyPathKey, $dateFormat)
//    {
//        $now = new \DateTime();
//
//        $expectedValue = $now->format($dateFormat);
//
//        $content = $this->decodeJson($this->lastResponse->getContent());
//        $propertyPath = PropertyAccess::createPropertyAccessor();
//        $value = $propertyPath->getValue($content, $propertyPathKey);
//        $value = is_array($value) ? array_keys($value) : $value;
//        $value = is_array($value) ? $value[0] : $value;
//        $contains = strpos($value, $expectedValue) !== false;
//        \PHPUnit_Framework_Assert::assertTrue($contains);
//    }
//
//    /**
//     * @Given /^response item "([^"]*)" should be empty$/
//     */
//    public function responseItemShouldBeEmpty($propertyPathKey)
//    {
//        $content = $this->decodeJson($this->lastResponse->getContent());
//        $propertyPath = PropertyAccess::createPropertyAccessor();
//        $value = $propertyPath->getValue($content, $propertyPathKey);
//        \PHPUnit_Framework_Assert::assertTrue(is_string($value));
//        \PHPUnit_Framework_Assert::assertTrue($value === '');
//    }
//
//    /**
//     * @Given /^response item "([^"]*)" should (?P<not>|not )be empty array$/
//     */
//    public function responseItemShouldBeEmptyArray($propertyPathKey, $not = '')
//    {
//        $content = $this->decodeJson($this->lastResponse->getContent());
//        $propertyPath = PropertyAccess::createPropertyAccessor();
//        $value = $propertyPath->getValue($content, $propertyPathKey);
//        \PHPUnit_Framework_Assert::assertTrue(is_array($value));
//
//        $shouldNotExist = !empty($not);
//        if ($shouldNotExist) {
//            \PHPUnit_Framework_Assert::assertTrue($value !== []);
//        } else {
//            \PHPUnit_Framework_Assert::assertTrue($value === []);
//        }
//    }
//
//    /**
//     * @Given /^response item "([^"]*)" should be array with (\d+) items$/
//     */
//    public function responseItemShouldArrayWithItems($propertyPathKey, $count)
//    {
//        $content = $this->decodeJson($this->lastResponse->getContent());
//        $propertyPath = PropertyAccess::createPropertyAccessor();
//        $value = $propertyPath->getValue($content, $propertyPathKey);
//        \PHPUnit_Framework_Assert::assertTrue(is_array($value));
//        \PHPUnit_Framework_Assert::assertTrue(count($value) == $count);
//    }
//
//    /**
//     * @Given /^response item "([^"]*)" should be true$/
//     */
//    public function responseItemShouldBeTrue($propertyPathKey)
//    {
//        $content = $this->decodeJson($this->lastResponse->getContent());
//        $propertyPath = PropertyAccess::createPropertyAccessor();
//        \PHPUnit_Framework_Assert::assertTrue($propertyPath->getValue($content, $propertyPathKey));
//    }
//
//    /**
//     * @Given /^response item "([^"]*)" should be false$/
//     */
//    public function responseItemShouldBeFalse($propertyPathKey)
//    {
//        $content = $this->decodeJson($this->lastResponse->getContent());
//        $propertyPath = PropertyAccess::createPropertyAccessor();
//        \PHPUnit_Framework_Assert::assertFalse($propertyPath->getValue($content, $propertyPathKey));
//    }
//
//    /**
//     * @Given /^response item "([^"]*)" should be null$/
//     */
//    public function responseItemShouldBeNull($propertyPathKey)
//    {
//        $content = $this->decodeJson($this->lastResponse->getContent());
//        $propertyPath = PropertyAccess::createPropertyAccessor();
//        \PHPUnit_Framework_Assert::assertNull($propertyPath->getValue($content, $propertyPathKey));
//    }

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

//    /**
//     * @Given /^I print last response$/
//     */
//    public function iPrintLastResponse()
//    {
//        if (!$this->lastResponse instanceof Response) {
//            throw new \Exception("Last response was not created.");
//        }
//        $this->printDebug($this->lastResponse->getContent());
//    }
//
//    /**
//     * @Given /^I ld last response$/
//     */
//    public function ldLastResponse()
//    {
//        if (!$this->lastResponse instanceof Response) {
//            throw new \Exception("Last response was not created.");
//        }
//        ldd(json_decode($this->lastResponse->getContent(), 1));
//    }
//
//    /**
//     * @Given /^(?:|I )send "(?P<method>[^"]*)" request to "(?P<uri>[^"]*)"$/
//     */
//    public function iSendRequestTo($method, $uri)
//    {
//        $method = strtoupper($method);
//        foreach ($this->getFeatureContext()->getAliases() as $alias => $value) {
//            $uri = str_replace($alias, $value, $uri);
//        }
//        $this->request($method, $this->getContext()->locatePath($uri));
//    }
//
    /**
     * @Given /^(?:|I )send "(?P<method>[^"]*)" request to "(?P<uri>[^"]*)" with content:$/
     */
    public function iSendRequestToWithContent($method, $uri, $node)
    {
        $this->request(strtoupper($method), $this->locatePath($uri), array(), $node);
    }

//    /**
//     * Decoding JSON
//     *
//     * @param $json
//     *
//     * @return mixed
//     * @throws \RuntimeException
//     */
//    private function decodeJson($json)
//    {
//        if (empty($json)) {
//            throw new \RuntimeException('JSON can not be an empty string');
//        }
//
//        if (!$content = json_decode($json, true)) {
//            throw new \RuntimeException(
//                sprintf('Failed to parse JSON. Error: %s; Content: %s', $this->jsonErrorToText(), $json)
//            );
//        }
//
//        return $content;
//    }
//
//    /**
//     * JSON Error Code to Text
//     *
//     * @return string
//     */
//    private function jsonErrorToText()
//    {
//        switch (json_last_error()) {
//            case JSON_ERROR_NONE:
//                return 'No errors';
//            case JSON_ERROR_DEPTH:
//                return 'Maximum stack depth exceeded';
//            case JSON_ERROR_STATE_MISMATCH:
//                return 'Underflow or the modes mismatch';
//            case JSON_ERROR_CTRL_CHAR:
//                return 'Unexpected control character found';
//            case JSON_ERROR_SYNTAX:
//                return 'Syntax error, malformed JSON';
//            case JSON_ERROR_UTF8:
//                return 'Malformed UTF-8 characters, possibly incorrectly encoded';
//            default:
//                return 'Unknown error';
//        }
//    }
//
//    /**
//     * Gets feature context
//     *
//     * @return FeatureContext
//     */
//    public function getFeatureContext()
//    {
//        return parent::getMainContext();
//    }
}
