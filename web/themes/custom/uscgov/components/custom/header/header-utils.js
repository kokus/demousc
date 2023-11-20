((Drupal) => {
  Drupal.uscgov = Drupal.uscgov || {};

  /**
   * A selector that will return all focusable elements.
   */
  Drupal.uscgov.focusableElementsSelector = ':is(audio, button, canvas, details, iframe, input, select, summary, textarea, video, [accesskey], [contenteditable], [href], [tabindex]:not([tabindex*="-"])):not(:is([disabled], [inert]))';

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
