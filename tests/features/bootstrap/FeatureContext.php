<?php

use Behat\Behat\Context\Context;
use Drupal\DrupalExtension\Context\RawDrupalContext;

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends RawDrupalContext implements Context {

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
   * @When I hover on link :link
   */
  public function iHoverOnAdminLink($link) {
    //*[@id="block-gin-content"]/div/dl/div[1]/dt
    $session = $this->getSession();
    $element = $session->getPage()->find(
      'xpath',
      $session->getSelectorsHandler()->selectorToXpath('xpath', '//a[normalize-space()="' . $link . '"]')
    );
    if (NULL === $element) {
      throw new InvalidArgumentException(sprintf('Cannot find link: "%s"', $link));
    }
    $element->mouseOver();
  }

  /**
   * Cleans up after claim profile settings changed.
   *
   * @AfterScenario @claim_profile_settings
   */
  public function cleanUpClaimProfile() {
    $claim_profile_settings = \Drupal::keyValue('claim_profile_settings');
    $claim_profile_settings->deleteAll();
    \Drupal::service("router.builder")->rebuild();
  }

  /**
   * @When I swith to new tab
   */
  public function iSwithToNewTab() {
    $session = $this->getSession();
    $windowNames = $session->getWindowNames();
    if(sizeof($windowNames) < 2){
      throw new \ErrorException("Expected to see at least 2 windows opened.");
    }

    // Switch to that window.
    $session->switchToWindow($windowNames[1]);
  }

  /**
   * @Given /^I close the current (?:window|tab)$/
   */
  public function closeCurrentWindow() {
    $session = $this->getSession();
    // Switch to that window.
    $session->switchToWindow();
  }

}
