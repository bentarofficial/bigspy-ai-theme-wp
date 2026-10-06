/* Accessible variation buttons keep the native select as the source of truth. */
(function ($) {
 'use strict';
 $('.variations_form').each(function () {
  var form = $(this);
  form.find('.variations select').each(function () {
   var select = this, holder = document.createElement('div');
   holder.className = 'bst-swatches'; holder.setAttribute('role','group');
   var label = form.find('label[for="'+select.id.replace(/"/g,'')+'"]').text();
   holder.setAttribute('aria-label', label || select.name);
   function update() {
    holder.replaceChildren();
    Array.from(select.options).forEach(function (option) {
     if (!option.value) return;
     var button = document.createElement('button'); button.type='button'; button.textContent=option.textContent;
     button.disabled=option.disabled; button.setAttribute('aria-pressed',String(select.value===option.value));
     button.addEventListener('click',function () { select.value=select.value===option.value?'':option.value; $(select).trigger('change'); });
     holder.appendChild(button);
    });
   }
   select.insertAdjacentElement('afterend',holder); select.classList.add('bst-swatches-native'); update();
   $(select).on('change',update); form.on('woocommerce_update_variation_values reset_data',update);
  });
 });
})(jQuery);
