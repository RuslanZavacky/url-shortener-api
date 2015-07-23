Feature: URL API

  Scenario: Create short url with simple url
    Given I clean up records with URL "http://example.com"
    Then I send "POST" request to "/api/v1/urls" with content:
    """
        {
            "url": "http://example.com"
        }
    """
    And the response status code should be 201

  Scenario: Create short url with url and query parameters
    Given I clean up records with URL "http://example.com?param1=value=1&param2=value2"
    Then I send "POST" request to "/api/v1/urls" with content:
    """
        {
            "url": "http://example.com?param1=value=1&param2=value2"
        }
    """
    And the response status code should be 201

  Scenario: Check status codes returned. If record is new, status code = 201. If record already exists then = 200.
    Given I clean up records with URL "http://example2.com"
    Then I send "POST" request to "/api/v1/urls" with content:
    """
        {
            "url": "http://example2.com"
        }
    """
    And the response status code should be 201

    Then I send "POST" request to "/api/v1/urls" with content:
    """
        {
            "url": "http://example2.com"
        }
    """
    And the response status code should be 200