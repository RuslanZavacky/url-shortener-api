<?php
namespace Rz\Bundle\UrlShortenerBundle\Features\Context;

use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Session;
use Behat\MinkExtension\Context\MinkContext;

class FeatureContext extends MinkContext implements SnippetAcceptingContext
{
    public function __construct()
    {
    }
}