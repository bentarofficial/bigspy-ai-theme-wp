(function () {
  'use strict';
  var search = document.querySelector('[data-bst-welcome-search]');
  if (!search) { return; }
  var cards = Array.prototype.slice.call(document.querySelectorAll('[data-bst-welcome-card]'));
  var results = document.querySelector('[data-bst-welcome-results]');
  function normalize(value) { return value.toLocaleLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/đ/g, 'd'); }
  function filter() {
    var query = normalize(search.value.trim()), count = 0;
    cards.forEach(function (card) { card.hidden = normalize(card.textContent).indexOf(query) < 0; if (!card.hidden) { count++; } });
    results.textContent = results.dataset.label + count;
  }
  search.addEventListener('input', filter);
  filter();
})();
