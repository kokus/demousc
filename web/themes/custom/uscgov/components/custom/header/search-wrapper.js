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

    searchWrapper.addEventListener('focusout', (e) => {
      if (!Drupal.uscgov.isMobileMenuSystem()) {
        if (!searchWrapper.contains(e.relatedTarget)) {
          changeSearchVisibility(false);
        }
      }
    }, true);
  }

  /**
   * Show/hide search.
   *
   * @param {boolean} toState
   */
  function changeSearchVisibility(toState) {
    const searchWrapperContainsFocus = searchWrapper.contains(document.activeElement);

    searchWrapper.classList.toggle('is-active', toState);
    desktopSearchButton.setAttribute('aria-expanded', toState);

    if (toState === false && !Drupal.uscgov.isMobileMenuSystem() && searchWrapperContainsFocus) {
      desktopSearchButton.focus();
    }

    if (toState === true && !Drupal.uscgov.isMobileMenuSystem()) {
      searchWrapper.querySelector('button, input')?.focus();
    }
  }

  Drupal.behaviors.searchWrapper = {
    attach(context) {
      once('search-wrapper', '.header', context).forEach(init);
    },
  };
})(Drupal, once);
