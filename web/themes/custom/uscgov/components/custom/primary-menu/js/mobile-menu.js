/**
 * @file
 * Mobile menu interactions
 */
((Drupal, once) => {
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
      backButton.addEventListener('click', () => {
        if (Drupal.uscgov.isMobileMenuSystem()) {
          navigateToParent(backButton);
        }
      });
    });


    // @todo Debug code.
    // document.querySelector('[aria-controls="id-about-federal-courts-dropdown"]').click();
  }

  function expandMenu(button) {
    button.setAttribute('aria-expanded', true);
  }

  function navigateToParent(backButton) {
    const closestMenuItem = backButton.closest('.primary-menu__menu-item');
    const parentMenuButton = closestMenuItem.querySelector('.primary-menu__button');

    parentMenuButton.setAttribute('aria-expanded', false);
  }

  function getMenuParentTitle() {

  }


  Drupal.behaviors.mobileMenu = {
    attach(context) {
      once('mobile-menu', '.primary-menu--level-0', context).forEach(init);
    },
  };
})(Drupal, once);
