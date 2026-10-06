(function () {
 'use strict';
 document.addEventListener('click',function (e) {
  var button=e.target.closest('form.cart button[type=submit]'); if(!button) return;
  var form=button.closest('form'), flag=form.querySelector('input[name=bst_buy_now]');
  if(button.classList.contains('bst-buy-now')) { if(!flag) {flag=document.createElement('input'); flag.type='hidden'; flag.name='bst_buy_now'; form.appendChild(flag);} flag.value='1'; }
  else if(flag) {flag.remove();}
 });
})();
