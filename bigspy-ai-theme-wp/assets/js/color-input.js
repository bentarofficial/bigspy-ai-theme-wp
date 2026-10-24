/**
 * Type or paste a color code under every color palette and gradient picker in the editor
 * (theme blocks and core blocks alike), then press Apply or Enter.
 *
 * The pickers are WordPress components, so the row finds the picker's own onChange
 * through React and calls it with the typed value.
 */
(function () {
	'use strict';
	var __ = (window.wp && wp.i18n && wp.i18n.__) || function (s) { return s; };
	var SOLID = '.components-color-palette__custom-color-wrapper';
	var GRADIENT = '.components-custom-gradient-picker';

	function fiberOf(el) {
		for (var k in el) {
			if (k.indexOf('__reactFiber$') === 0) {
				return el[k];
			}
		}
		return null;
	}

	/* Nearest component above the element whose props have onChange (and the wanted key). */
	function findOnChange(el, needKey) {
		var f = fiberOf(el);
		for (var i = 0; f && i < 40; i++, f = f.return) {
			var p = f.memoizedProps;
			if (p && typeof p.onChange === 'function' && (!needKey || needKey in p)) {
				return p.onChange;
			}
		}
		return null;
	}

	function validColor(v) {
		return !!v && window.CSS && CSS.supports('color', v);
	}

	function normalizeColor(v) {
		v = v.trim();
		if (/^[0-9a-f]{3}([0-9a-f]{3})?([0-9a-f]{2})?$/i.test(v)) {
			v = '#' + v;
		}
		return validColor(v) ? v : '';
	}

	/* A full CSS gradient, or 2+ colors separated by spaces / commas -> linear gradient. */
	function normalizeGradient(v) {
		v = v.trim().replace(/;$/, '');
		if (/gradient\(/i.test(v)) {
			return window.CSS && CSS.supports('background-image', v) ? v : '';
		}
		var parts = v.split(/[\s,]+/).map(normalizeColor).filter(Boolean);
		return parts.length >= 2 ? 'linear-gradient(135deg, ' + parts.join(', ') + ')' : '';
	}

	function row(target, kind) {
		var wrap = document.createElement('div');
		wrap.className = 'bst-color-input';
		var input = document.createElement('input');
		input.type = 'text';
		input.spellcheck = false;
		input.className = 'bst-color-input__field';
		input.placeholder = kind === 'gradient' ? '#eef6fb #087f8c  hoặc  linear-gradient(...)' : '#eef6fb';
		input.setAttribute('aria-label', kind === 'gradient' ? __('Gradient code', 'bigspy-ai-theme-wp') : __('Color code', 'bigspy-ai-theme-wp'));
		var button = document.createElement('button');
		button.type = 'button';
		button.className = 'components-button is-secondary is-compact bst-color-input__apply';
		button.textContent = __('Apply', 'bigspy-ai-theme-wp');
		var note = document.createElement('div');
		note.className = 'bst-color-input__note';

		function apply() {
			var value = kind === 'gradient' ? normalizeGradient(input.value) : normalizeColor(input.value);
			if (!value) {
				note.textContent = kind === 'gradient' ? __('Enter a CSS gradient or at least two colors.', 'bigspy-ai-theme-wp') : __('Not a valid color code.', 'bigspy-ai-theme-wp');
				wrap.classList.add('is-error');
				return;
			}
			var onChange = kind === 'gradient' ? findOnChange(target, 'value') : (findOnChange(target, 'colors') || findOnChange(target, 'value'));
			if (!onChange) {
				note.textContent = __('This picker cannot be set from here.', 'bigspy-ai-theme-wp');
				wrap.classList.add('is-error');
				return;
			}
			onChange(value);
			wrap.classList.remove('is-error');
			note.textContent = '';
			input.value = '';
		}

		button.addEventListener('click', apply);
		input.addEventListener('keydown', function (e) {
			if (e.key === 'Enter') {
				e.preventDefault();
				apply();
			}
		});
		// Keep the parent popover open while typing.
		input.addEventListener('mousedown', function (e) { e.stopPropagation(); });

		wrap.appendChild(input);
		wrap.appendChild(button);
		wrap.appendChild(note);
		return wrap;
	}

	function enhance(root) {
		(root.querySelectorAll ? root : document).querySelectorAll(SOLID + ',' + GRADIENT).forEach(function (el) {
			if (el.dataset.bstColorInput) {
				return;
			}
			el.dataset.bstColorInput = '1';
			if (el.matches(GRADIENT)) {
				el.parentNode.insertBefore(row(el, 'gradient'), el.nextSibling);
				return;
			}
			// Solid palette: the row goes under the swatches, at the end of the palette box.
			var box = el.parentNode;
			while (box && box !== document.body && !box.querySelector('.components-circular-option-picker')) {
				box = box.parentNode;
			}
			(box && box !== document.body ? box : el.parentNode).appendChild(row(el, 'solid'));
		});
	}

	function start() {
		enhance(document);
		var queued = false;
		new MutationObserver(function () {
			if (queued) {
				return;
			}
			queued = true;
			window.requestAnimationFrame(function () {
				queued = false;
				enhance(document);
			});
		}).observe(document.body, { childList: true, subtree: true });
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', start);
	} else {
		start();
	}
})();
