/**
 * @file
 * Mobile menu interactions
 */
((Drupal, once) => {
  function init(menu) {
    const menuButtons = menu.querySelectorAll('.primary-menu__button');

    menuButtons.forEach(menuButton => {
      menuButton.addEventListener('click', () => {
        const menuButtonExpanded = menuButton.getAttribute('aria-expanded') === 'true';
        menuButton.setAttribute('aria-expanded', !menuButtonExpanded);
      })
    });

    // @todo Debug code.
    document.querySelector('[aria-controls="id-about-federal-courts-dropdown"]').click();
  }


  Drupal.behaviors.mobileMenu = {
    attach(context) {
      once('mobile-menu', '.primary-menu--level-0', context).forEach(init);
    },
  };
})(Drupal, once);
