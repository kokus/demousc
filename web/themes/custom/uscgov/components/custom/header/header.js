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
    menuWrapper.classList.toggle('is-active', toState);
    mobileNavButton.setAttribute('aria-expanded', toState);
  }

  Drupal.behaviors.header = {
    attach(context) {
      once('header', '.header', context).forEach(
        init,
      );
    },
  };
})(Drupal, once);
