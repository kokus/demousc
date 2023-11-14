/**
 * @file
 * Dynamic header interaction.
 */
((Drupal, once) => {
  function init(header) {
    const mobileNavButton = header.querySelector('.header__menu-button');
    const closeButton = header.querySelector('.header__menu-wrapper-close-button');
    const menuWrapper = header.querySelector('.header__menu-wrapper');
    const isNavOpen = () => mobileNavButton.getAttribute('aria-expanded') === 'true';

    mobileNavButton.addEventListener('click', () => {
      menuWrapper.classList.toggle('is-active', !isNavOpen());
      mobileNavButton.setAttribute('aria-expanded', !isNavOpen());
    });

    closeButton.addEventListener('click', () => {
      menuWrapper.classList.remove('is-active');
      mobileNavButton.setAttribute('aria-expanded', false);
    });
  }

  Drupal.behaviors.header = {
    attach(context) {
      once('header', '.header', context).forEach(
        init,
      );
    },
  };
})(Drupal, once);
