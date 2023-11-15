/**
 * @file
 * Dynamic header interaction.
 */
((Drupal, once) => {
  let desktopSearchButton;
  let searchWrapper;
  let isSearchOpen;
  let closeButton;

  function init(header) {
    desktopSearchButton = header.querySelector('.header__search-button');
    closeButton = header.querySelector('.header__search-wrapper-close-button');
    searchWrapper = header.querySelector('.header__search-wrapper');
    isSearchOpen = () => desktopSearchButton.getAttribute('aria-expanded') === 'true';

    desktopSearchButton.addEventListener('click', () => {
      changeSearchVisibility(!isSearchOpen());
    });

    closeButton.addEventListener('click', () => {
      changeSearchVisibility(false);
    });

    document.addEventListener('keyup', (e) => {
      if (e.key === 'Escape') {
        changeSearchVisibility(false);
      }
    });
  }

  /**
   * Show/hide search.
   *
   * @param {boolean} toState
   */
  function changeSearchVisibility(toState) {
    searchWrapper.classList.toggle('is-active', toState);
    desktopSearchButton.setAttribute('aria-expanded', toState);
  }

  Drupal.behaviors.searchWrapper = {
    attach(context) {
      once('search-wrapper', '.header', context).forEach(init);
    },
  };
})(Drupal, once);
