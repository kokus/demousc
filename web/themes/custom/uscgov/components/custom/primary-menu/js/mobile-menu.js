/**
 * @file
 * Mobile menu interactions.
 */
((Drupal, once) => {
  /**
   * Initialize the mobile menu.
   *
   * @param {Element} menu - The top-level primary menu <ul>.
   */
  function init(menu) {
    const menuButtons = menu.querySelectorAll('.primary-menu__button');
    const backButtons = menu.querySelectorAll('.primary-menu__mobile-back-button');

    menuButtons.forEach(menuButton => {
      menuButton.addEventListener('click', () => {
        if (Drupal.uscgov.isMobileMenuSystem()) {
          expandMenu(menuButton);
        }
      });
    });

    backButtons.forEach(backButton => {
      if (backButton.closest('.primary-menu__level-2-wrapper')) {
        parentMenuText =
          backButton
            .closest('.primary-menu__dropdown')
            .querySelector('.primary-menu__dropdown-title')
            .textContent
            .trim();
        backButton.innerHTML = `
          <span class="visually-hidden">${ Drupal.t('Back to') }</span>
          ${ parentMenuText }
        `;
      }

      backButton.addEventListener('click', () => {
        if (Drupal.uscgov.isMobileMenuSystem()) {
          navigateToParent(backButton);
        }
      });
    });
  }
  /**
   * Expands the submenu controlled by passed in <button> element.
   *
   * @param {Element} button - The button element that will be expanded.
   */
  function expandMenu(button) {
    button.setAttribute('aria-expanded', true);
  }

  /**
   * Closes the submenu that is controlled by a passed in <button> element.
   *
   * @param {Element} backButton
   */
  function navigateToParent(backButton) {
    const closestMenuItem = backButton.closest('.primary-menu__menu-item');
    const parentMenuButton = closestMenuItem.querySelector('.primary-menu__button');

    parentMenuButton.setAttribute('aria-expanded', false);
  }

  Drupal.behaviors.mobileMenu = {
    attach(context) {
      once('mobile-menu', '.primary-menu--level-0', context).forEach(init);
    },
  };
})(Drupal, once);
