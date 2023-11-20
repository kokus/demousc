/**
 * @file
 * Dynamic header interaction.
 */
((Drupal, once) => {
  let mobileNavButton;
  let closeButton;
  let menuWrapper;
  let isNavOpen;
  let overlay;

  function init(header) {
    mobileNavButton = header.querySelector('.header__menu-button');
    closeButton = header.querySelector('.header__menu-wrapper-close-button');
    menuWrapper = header.querySelector('.header__menu-wrapper');
    isNavOpen = () => mobileNavButton.getAttribute('aria-expanded') === 'true';
    overlay = header.querySelector('.header__menu-overlay');

    mobileNavButton.addEventListener('click', () => {
      changeMenuVisibility(!isNavOpen());
    });

    closeButton.addEventListener('click', () => {
      changeMenuVisibility(false);
    });

    overlay.addEventListener('click', () => {
      changeMenuVisibility(false);
    });

    document.addEventListener('keyup', (e) => {
      if (e.key === 'Escape') {
        changeMenuVisibility(false);
      }
    });
  }

  /**
   * Show/hide menu.
   *
   * @param {boolean} toState
   */
  function changeMenuVisibility(toState) {
    const menuWrapperContainsFocus = menuWrapper.contains(document.activeElement);

    menuWrapper.classList.toggle('is-active', toState);
    mobileNavButton.setAttribute('aria-expanded', toState);
    toggleFocusTrap(toState);

    // Close any open submenus when mobile menu is closed.
    if (toState === false && Drupal.uscgov.isMobileMenuSystem()) {
      Drupal.uscgov.closeAllMenuItems();
    }

    if (Drupal.uscgov.isMobileMenuSystem()) {
      // On opening, set the focus to the first focusable element in the menu.
      if (toState) {
        menuWrapper.addEventListener('transitionend', () => {
          menuWrapper.querySelector(Drupal.uscgov.focusableElementsSelector)?.focus();
        }, { once: true });
      }
      // On close. set focus back to button only if an element within the mobile menu has focus.
      else if (menuWrapperContainsFocus) {
        mobileNavButton.focus();
      }
    }
  }

  /**
   * Enables/disables a focus trap on the mobile menu wrapper.
   *
   * @param {boolean} focusTrapEnabled - True if the focus trap should be enabled,
   * otherwise false.
   */
  function toggleFocusTrap(focusTrapEnabled) {
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

  Drupal.behaviors.menuWrapper = {
    attach(context) {
      once('menu-wrapper', '.header', context).forEach(init);
    },
  };
})(Drupal, once);
