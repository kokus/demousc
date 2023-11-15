((Drupal) => {
  Drupal.uscgov = Drupal.uscgov || {};

  /**
   * Is the mobile navigation system active?
   *
   * @returns boolean
   */
  Drupal.uscgov.isMobileMenuSystem = () => {
    const mobileNavButton = document.querySelector('.header__menu-button');
    return mobileNavButton?.clientHeight !== 0;
  }
})(Drupal);
