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

  /**
   * Close any and all open menu items.
   */
  Drupal.uscgov.closeAllMenuItems = () => {
    const openMenuItems = document.querySelectorAll('.primary-menu--level-0 [aria-expanded="true"]');
    openMenuItems.forEach(item => item.setAttribute('aria-expanded', false));
  }
})(Drupal);
