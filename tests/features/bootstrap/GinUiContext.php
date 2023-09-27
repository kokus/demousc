<?php

use Behat\Behat\Context\Context;
use Drupal\DrupalExtension\Context\RawDrupalContext;

/**
 * Defines application features from the specific context.
 */
class GinUiContext extends RawDrupalContext implements Context {

  /**
   * Initializes context.
   *
   * Every scenario gets its own context instance.
   * You can also pass arbitrary arguments to the
   * context constructor through behat.yml.
   */
  public function __construct() {
  }

  /**
   * @When I click on admin link :link
   */
  public function iClickOnAdminLink($link) {
    //*[@id="block-gin-content"]/div/dl/div[1]/dt
    $session = $this->getSession();
    $element = $session->getPage()->find(
      'xpath',
      $session->getSelectorsHandler()->selectorToXpath('xpath', '//dt[@class="admin-item__title"][normalize-space()="' . $link . '"]/preceding-sibling::a')
    );
    if (NULL === $element) {
      throw new InvalidArgumentException(sprintf('Cannot find admin link: "%s"', $link));
    }
    $element->click();
  }

  /**
   * @When I click action :link
   */
  public function iClickAction($link) {
    $session = $this->getSession();
    $element = $session->getPage()->find(
      'xpath',
      $session->getSelectorsHandler()->selectorToXpath('xpath', '//a[@class="button button--action button--primary"][normalize-space()="' . $link . '"]')
    );
    if (NULL === $element) {
      throw new InvalidArgumentException(sprintf('Cannot find action: "%s"', $link));
    }
    $element->click();
  }

  /**
   * @When I open details on :link
   */
  public function iOpenDetailsOn($link) {
    $session = $this->getSession();
    $element = $session->getPage()->find(
      'xpath',
      $session->getSelectorsHandler()->selectorToXpath('xpath', '//summary[@class="claro-details__summary claro-details__summary--accordion-item"][normalize-space()="' . $link . '"]')
    );
    if (NULL === $element) {
      throw new InvalidArgumentException(sprintf('Cannot find details element: "%s"', $link));
    }
    $element->click();
  }

  /**
   * @When I select action :action in the :rowText row
   */
  public function iSelectActionInTheRow($action, $rowText) {
    $session = $this->getSession();
    $rows = $session->getPage()->findAll('css', 'tr');
    if (empty($rows)) {
      throw new \Exception(sprintf('No rows found on the page %s', $session->getCurrentUrl()));
    }
    foreach ($rows as $row) {
      $found_test_pos = strpos($row->getText(), $rowText);
      if ($found_test_pos !== false || $found_test_pos === 0) {
        // Open dropdown.
        $dropdown_arrow = $row->find(
          'xpath',
          $session->getSelectorsHandler()->selectorToXpath('xpath', '//button[@class="dropbutton__toggle"]')
        );
        if (NULL === $dropdown_arrow) {
          throw new InvalidArgumentException(sprintf('Cannot find dropdown arrow in row "%s"', $rowText));
        }
        $dropdown_arrow->click();
        // $session->wait(2000);
        // Select action.
        $element_action = $row->findLink($action);
        if (NULL === $element_action) {
          throw new InvalidArgumentException(sprintf('Cannot find action: "%s" in row "%s"', $action, $rowText));
        }
        $element_action->click();
        return;
      }
    }
    throw new \Exception(sprintf('Failed to find a row containing "%s" on the page %s', $rowText, $session->getCurrentUrl()));
  }

  /**
   * @When I click on tab :tab
   */
  public function iClickOnTab($tab) {
    $session = $this->getSession();
    $element = $session->getPage()->find(
      'xpath',
      $session->getSelectorsHandler()->selectorToXpath('xpath', '//a[@class="tabs__link js-tabs-link"][normalize-space()="' . $tab . '"]')
    );
    if (NULL === $element) {
      throw new InvalidArgumentException(sprintf('Cannot find tab: "%s"', $tab));
    }
    $element->click();
  }

  /**
   * @When I press :button in the :rowText row
   */
  public function pressButtonInRow($button, $rowText) {
    $session = $this->getSession();
    $rows = $session->getPage()->findAll('css', 'tr');

    foreach ($rows as $row) {
      $found_test_pos = strpos($row->getText(), $rowText);
      if ($found_test_pos !== false || $found_test_pos === 0) {
        $item = $row->find(
          'xpath',
          $session->getSelectorsHandler()->selectorToXpath('xpath', '//input[@class="button js-form-submit form-submit"][normalize-space(@value)="' . $button . '"]')
        );
        if (NULL === $item) {
          throw new InvalidArgumentException(sprintf('Cannot find item in row "%s"', $rowText));
        }
        $item->click();
      }
    }
  }

  /**
   * @When I click submit input :text
   */
  public function iClickSubmitInput($text) {
    $session = $this->getSession();
    $element = $session->getPage()->find(
      'xpath',
      $session->getSelectorsHandler()->selectorToXpath('xpath', '//input[@class="js-form-submit form-submit btn btn-primary"][normalize-space(@value)="' . $text . '"]')
    );
    if (NULL === $element) {
      throw new InvalidArgumentException(sprintf('Cannot find input submit: "%s"', $text));
    }
    $element->click();
  }

  /**
   * @When I click arial-label button :text
   */
  public function iClickArial($text) {
    $session = $this->getSession();
    $element = $session->getPage()->find(
      'xpath',
      $session->getSelectorsHandler()->selectorToXpath('xpath', '//button[normalize-space(@aria-label)="' . $text . '"]')
    );
    if (NULL === $element) {
      throw new InvalidArgumentException(sprintf('Cannot find input submit: "%s"', $text));
    }
    $element->click();
  }

  /**
   * @When I click button class :text
   */
  public function iClickButtonClass($text) {
    $session = $this->getSession();
    $element = $session->getPage()->find(
      'xpath',
      $session->getSelectorsHandler()->selectorToXpath('xpath', '//button[@class="' . $text . '"]')
    );
    if (NULL === $element) {
      throw new InvalidArgumentException(sprintf('Cannot find button with class: "%s"', $text));
    }
    $element->click();
  }

  /**
   * @When /^I fill gutenberg paragraph field with "([^"]*)"$/
   */
  public function iFillGutenbergFieldWith($value) {
    $session = $this->getSession();
    $element = $session->getPage()->find(
      'xpath',
      $session->getSelectorsHandler()->selectorToXpath('xpath', '//p[@class="block-editor-rich-text__editable block-editor-block-list__block wp-block is-selected wp-block-paragraph rich-text"]')
    );
    if (NULL === $element) {
      throw new InvalidArgumentException(sprintf('Cannot find the Gutenberg paragraph.'));
    }
    $element->setValue($value);
  }

}
