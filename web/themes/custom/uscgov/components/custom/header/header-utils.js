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
   * Enables/disables a focus trap on the mobile menu wrapper.
   *
   * @param {boolean} focusTrapEnabled - True if the focus trap should be enabled,
   * otherwise false.
   */
  Drupal.uscgov.toggleFocusTrap = focusTrapEnabled => {
    const menuWrapper = document.querySelector('.header__menu-wrapper');

    if (Drupal.uscgov.isMobileMenuSystem() && focusTrapEnabled === true) {
      document.querySelectorAll(Drupal.uscgov.focusableElementsSelector).forEach(focusableElement => {
        if (!menuWrapper.contains(focusableElement)) {
          focusableElement.inert = true;
          focusableElement.dataset.mobileMenuInert = true;
        }
      });
    }
    else {
      document.querySelectorAll('[data-mobile-menu-inert], [data-mobile-menu-submenu-inert]').forEach(el => {
        el.inert = false;
        delete el.dataset.mobileMenuInert;
        delete el.dataset.mobileMenuSubmenuInert;
      });
    }
  }

  /**
   * Close any and all open menu items. This DOES NOT close the mobile navigation.
   */
  Drupal.uscgov.closeAllMenuItems = () => {
    const openMenuItems = document.querySelectorAll('.primary-menu--level-0 [aria-expanded="true"]');
    openMenuItems.forEach(item => item.setAttribute('aria-expanded', false));

    document.querySelectorAll('[data-mobile-menu-submenu-inert]').forEach(el => {
      el.inert = false;
      delete el.dataset.mobileMenuSubmenuInert;
    });
  }
})(Drupal);
