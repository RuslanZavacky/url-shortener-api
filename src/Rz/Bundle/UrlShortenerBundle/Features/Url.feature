Feature: URL API
  In order to have shortener URLs
  As a API client
  I need to be able to send API requests for URL minification

#  Scenario: Create short url with simple url
#    Given I clean up records with URL "http://example.com"
#    Then I send "POST" request to "/api/v1/urls" with content:
#    """
#        {
#            "url": "http://example.com"
#        }
#    """
#    And the response status code should be 201
#
#  Scenario: Create short url with url and query parameters
#    Given I clean up records with URL "http://example.com?param1=value=1&param2=value2"
#    Then I send "POST" request to "/api/v1/urls" with content:
#    """
#        {
#            "url": "http://example.com?param1=value=1&param2=value2"
#        }
#    """
#    And the response status code should be 201
#
#  Scenario: Check status codes returned. If record is new, status code = 201. If record already exists then = 200.
#    Given I clean up records with URL "http://example2.com"
#    Then I send "POST" request to "/api/v1/urls" with content:
#    """
#        {
#            "url": "http://example2.com"
#        }
#    """
#    And the response status code should be 201
#
#    Then I send "POST" request to "/api/v1/urls" with content:
#    """
#        {
#            "url": "http://example2.com"
#        }
#    """
#    And the response status code should be 200
#
#  Scenario: Send batch of URLs to be shortened
#    Given I clean up records with URL "http://example.com?batch=1"
#    Given I clean up records with URL "http://example.com?batch=2"
#    Given I clean up records with URL "http://example.com?batch=3"
#
#    Then I send "POST" request to "/api/v1/urls/batches" with content:
#    """
#        [
#          {
#            "url": "http://example.com?batch=1"
#          },
#          {
#            "url": "http://example.com?batch=2"
#          },
#          {
#            "url": "http://example.com?batch=3"
#          }
#        ]
#    """
#    And the response status code should be 200
#
#    Then I send "POST" request to "/api/v1/urls" with content:
#    """
#        {
#            "url": "http://example.com?batch=2"
#        }
#    """
#    And the response status code should be 200

  Scenario: Validate response of shortened URL
    Given I clean up records with URL "http://example.com?decoded=action"

    Then I send "POST" request to "/api/v1/urls" with content:
    """
        {
          "url": "http://example.com?decoded=action"
        }
    """
    And the response status code should be 201

    And response should contain "id"
    And response should contain "url"
    And response should contain "original_url"
    And response should contain "query_param"
    And response should contain "code"
    And response should contain "short_url"
    And response should contain "sequence"
    And response should contain "created"
    And response should contain "updated"
    And response should contain "redirect_count"
    And response should contain "unique_redirect_count"
    And response should contain "new"