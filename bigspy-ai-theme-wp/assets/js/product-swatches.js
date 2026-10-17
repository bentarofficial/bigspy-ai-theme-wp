/* Accessible variation buttons keep the native select as the source of truth. */
(function ($) {
 'use strict';
 $('.variations_form').each(function () {
  var form = $(this);
  form.find('.variations select').each(function () {
   var select = this, holder = document.createElement('div');
   var swatchMap = {};
   try { swatchMap = JSON.parse(select.getAttribute('data-bst-swatch-map') || '{}'); } catch (e) { swatchMap = {}; }
   holder.className = 'bst-swatches'; holder.setAttribute('role','group');
   var label = form.find('label[for="'+select.id.replace(/"/g,'')+'"]').text();
   holder.setAttribute('aria-label', label || select.name);
   function update() {
    holder.replaceChildren();
    Array.from(select.options).forEach(function (option) {
     if (!option.value) return;
     var button = document.createElement('button'), swatch = swatchMap[option.value] || {};
     button.type='button'; button.className='bst-swatch bst-swatch--'+(swatch.type || 'label');
     button.setAttribute('aria-label', option.textContent);
     if (swatch.type === 'color' && swatch.color) {
      button.style.setProperty('--bst-swatch-color', swatch.color);
      var colorLabel = document.createElement('span'); colorLabel.className='bst-swatch__screen-reader'; colorLabel.textContent=swatch.label || option.textContent; button.appendChild(colorLabel);
     } else if (swatch.type === 'label') button.textContent = swatch.label || option.textContent;
     if (swatch.type === 'image' && swatch.image) {
      var image = document.createElement('img'); image.src=swatch.image; image.alt=''; image.loading='lazy'; button.appendChild(image);
      var label = document.createElement('span'); label.textContent=swatch.label || option.textContent; button.appendChild(label);
     }
     button.disabled=option.disabled; button.setAttribute('aria-pressed',String(select.value===option.value));
     button.addEventListener('click',function () { select.value=select.value===option.value?'':option.value; $(select).trigger('change'); });
     holder.appendChild(button);
    });
   }
   select.insertAdjacentElement('afterend',holder); select.classList.add('bst-swatches-native'); select.setAttribute('aria-hidden','true'); select.tabIndex=-1; update();
   $(select).on('change',update); form.on('woocommerce_update_variation_values reset_data',update);
  });
 });
})(jQuery);
