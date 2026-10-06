(function () {
	'use strict';
	if (!window.fetch || !window.DOMParser || !document.querySelector('[data-bst-shop-results]')) return;
	var active = 0;
	var config = window.bstShopAjax || {mode:'pages'}, loading = false, observer;
	function setupMore() {
		if(observer)observer.disconnect();
		document.querySelectorAll('[data-bst-load-more]').forEach(function(b){b.remove();});
		if(config.mode==='pages')return;
		var pagination=document.querySelector('[data-bst-shop-results] .woocommerce-pagination'), next=pagination && pagination.querySelector('a.next');
		if(!next)return;
		var button=document.createElement('button');button.type='button';button.className='button';button.textContent='Tải thêm sản phẩm';button.dataset.bstLoadMore=next.href;
		pagination.insertAdjacentElement('afterend',button);
		if(config.mode==='infinite' && window.IntersectionObserver){observer=new IntersectionObserver(function(entries){if(entries[0].isIntersecting&&!loading)navigate(button.dataset.bstLoadMore,false,true);},{rootMargin:'100px'});observer.observe(button);}
	}
	var navigate = async function (url, push, append) {
		if(loading && append)return;
		loading=true;
		var ticket = ++active;
		var current = document.querySelector('[data-bst-shop-results]');
		if (!current) { location.assign(url); return; }
		current.setAttribute('aria-busy', 'true');
		try {
			var response = await fetch(url, { credentials: 'same-origin' });
			if (!response.ok || new URL(response.url).origin !== location.origin) throw new Error('Navigation unavailable');
			var doc = new DOMParser().parseFromString(await response.text(), 'text/html');
			var next = doc.querySelector('[data-bst-shop-results]');
			if (!next || next.querySelector('script')) throw new Error('Full navigation required');
			if (ticket !== active) return;
			if(append){
				var oldProducts=current.querySelector('ul.products'), newProducts=next.querySelector('ul.products');
				if(!oldProducts||!newProducts)throw new Error('Full navigation required');
				Array.from(newProducts.children).forEach(function(item){oldProducts.appendChild(item);});
				var oldNav=current.querySelector('.woocommerce-pagination'), newNav=next.querySelector('.woocommerce-pagination');
				if(oldNav){if(newNav)oldNav.replaceWith(newNav);else oldNav.remove();}
				next=current;
				var count=current.querySelector('.woocommerce-result-count');if(count)count.textContent='Đã tải '+oldProducts.children.length+' sản phẩm';
			}else{current.replaceWith(next);}
			next.querySelectorAll('form.woocommerce-ordering select.orderby').forEach(function (select) {
				select.addEventListener('change', function () { if(!config.filters)select.form.submit(); });
			});
			document.title = doc.title;
			if (push) history.pushState({ bstShop: true }, '', url);
			if(!append){next.focus({ preventScroll: true });next.scrollIntoView({ block: 'start', behavior: 'auto' });}
			next.removeAttribute('aria-busy');loading=false;setupMore();
			document.dispatchEvent(new CustomEvent('bst:shop-updated', { detail: { url: url } }));
		} catch (error) {
			loading=false;
			if (ticket === active) location.assign(url);
		}
	};
	history.replaceState(Object.assign({}, history.state, { bstShop: true }), '', location.href);
	document.addEventListener('click', function (event) {
		var more=event.target.closest('[data-bst-load-more]');if(more){event.preventDefault();navigate(more.dataset.bstLoadMore,false,true);return;}
		var link = event.target.closest('[data-bst-shop-results] .woocommerce-pagination a');
		if (!link || event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey || link.target) return;
		var url = new URL(link.href, location.href);
		if (url.origin !== location.origin) return;
		event.preventDefault();
		navigate(url.href, true);
	});
	function formUrl(form){var url=new URL(form.action||location.href,location.href);new FormData(form).forEach(function(v,k){url.searchParams.set(k,v);});return url;}
	if(config.filters){
		document.addEventListener('submit',function(e){var form=e.target;if(!form.matches('.woocommerce-ordering, .widget_price_filter form'))return;var url=formUrl(form);if(url.origin!==location.origin)return;e.preventDefault();navigate(url.href,true,false);});
		document.addEventListener('change',function(e){if(!e.target.matches('.woocommerce-ordering select.orderby'))return;e.stopImmediatePropagation();var url=formUrl(e.target.form);if(url.origin===location.origin)navigate(url.href,true,false);},true);
	}
	setupMore();
	window.addEventListener('popstate', function (event) {
		if (event.state && event.state.bstShop) navigate(location.href, false);
		else location.reload();
	});
})();
