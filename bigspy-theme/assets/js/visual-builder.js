/* BIGSPY AI standalone visual workspace. Native blocks remain editable in WordPress. */
(function (wp, cfg) {
 'use strict';
 var el = wp.element.createElement, useState = wp.element.useState, useRef = wp.element.useRef, useEffect = wp.element.useEffect;
 var be = wp.blockEditor, c = wp.components;
 if (!wp.blocks.getBlockType('core/paragraph')) { wp.blockLibrary.registerCoreBlocks(); }
 function Workspace() {
  var initial = useRef(wp.blocks.parse(cfg.content));
  var pair = useState(initial.current), blocks = pair[0], setBlocks = pair[1];
  var tp = useState(cfg.title), title = tp[0], setTitle = tp[1];
  var vp = useState('desktop'), device = vp[0], setDevice = vp[1];
  var mp = useState('design'), mode = mp[0], setMode = mp[1];
  var np = useState(''), notice = np[0], setNotice = np[1];
  var sp = useState(false), saving = sp[0], setSaving = sp[1];
  var lp = useState(false), libraryOpen = lp[0], setLibraryOpen = lp[1];
  var pp = useState('structure'), panel = pp[0], setPanel = pp[1];
  var ip = useState(false), inspectorOpen = ip[0], setInspectorOpen = ip[1];
  var qp = useState(''), query = qp[0], setQuery = qp[1];
  var ep = useState({}), expanded = ep[0], setExpanded = ep[1];
  var lpTab = useState('elements'), libraryTab = lpTab[0], setLibraryTab = lpTab[1];
  var cp = useState('Tất cả'), category = cp[0], setCategory = cp[1];
  var history = useRef([]), future = useRef([]), current = useRef(blocks), baseline = useRef(wp.blocks.serialize(initial.current)), modified = useRef(cfg.modified);
  current.current = blocks;
  var serialized = wp.blocks.serialize(blocks), dirty = serialized !== baseline.current || title !== cfg.title;
  function change(next) { if (wp.blocks.serialize(next) !== wp.blocks.serialize(current.current)) { history.current.push(current.current); if (history.current.length > 60) history.current.shift(); future.current = []; } current.current = next; setBlocks(next); }
  useEffect(function () { function guard(e) { if (dirty) { e.preventDefault(); e.returnValue = ''; } } window.addEventListener('beforeunload', guard); return function () { window.removeEventListener('beforeunload', guard); }; }, [dirty]);
  function undo() { if (history.current.length) { future.current.push(current.current); var next = history.current.pop(); current.current = next; setBlocks(next); } }
  function redo() { if (future.current.length) { history.current.push(current.current); var next = future.current.pop(); current.current = next; setBlocks(next); } }
  function save() {
   if (saving) return; setSaving(true); setNotice('Đang lưu…');
   wp.apiFetch({path:'/bigspy/v1/builder/' + cfg.id, method:'POST', data:{content:serialized,title:title,modified:modified.current}}).then(function (result) {
    modified.current = result.modified; baseline.current = result.content; cfg.title = result.title;
    // Preserve client IDs and selection when server did not sanitize content.
    if (result.content !== serialized) { var parsed = wp.blocks.parse(result.content); current.current = parsed; setBlocks(parsed); }
    setTitle(result.title); setNotice('Đã lưu. ' + (cfg.status === 'publish' ? 'Trang công khai đã được cập nhật.' : 'Trang vẫn giữ trạng thái hiện tại, chưa tự xuất bản.'));
   }).catch(function (err) { setNotice(err.message || 'Không lưu được. Nội dung vẫn còn trong trình thiết kế.'); }).finally(function () { setSaving(false); });
  }
  var settings = Object.assign({}, cfg.settings, {mediaUpload: function (args) { wp.mediaUtils.mediaUpload(args); }});
  function dropElement(e) { var name=e.dataTransfer.getData('text/x-bigspy-element'); if(!name) return; e.preventDefault(); e.stopPropagation(); var store=wp.data.select('core/block-editor'), target=e.target.closest('[data-block]'), id=target && target.getAttribute('data-block'), root=id ? store.getBlockRootClientId(id) : undefined, index=id ? store.getBlockIndex(id,root) : store.getBlockOrder().length; if(store.canInsertBlockType(name,root)) { wp.data.dispatch('core/block-editor').insertBlocks(wp.blocks.createBlock(name),index,root); setPanel('structure'); setLibraryOpen(false); setInspectorOpen(true); } }
  function add(name) {
   var type=wp.blocks.getBlockType(name), store=wp.data.select('core/block-editor'), dispatch=wp.data.dispatch('core/block-editor');
   if (!type) return;
   var selected=store.getSelectedBlockClientId(), root, index;
   if (selected && store.canInsertBlockType(name,selected)) { root=selected; index=store.getBlockOrder(selected).length; }
   else if (selected) { root=store.getBlockRootClientId(selected); index=store.getBlockIndex(selected,root)+1; }
   else { root=undefined; index=store.getBlockOrder().length; }
   if (!store.canInsertBlockType(name,root)) { setNotice('Phần tử này chưa thể đặt tại vị trí đang chọn. Hãy chọn một Section hoặc vị trí khác.'); return; }
   dispatch.insertBlocks(wp.blocks.createBlock(name),index,root);
   setPanel('structure'); setLibraryOpen(false); setInspectorOpen(true);
  }
  var names = ['bst/section','bst/row','bst/banner','bst/slider','bst/title','bst/text','bst/image','bst/buttons','bst/icon-box','bst/tabs','bst/accordion','bst/posts','bst/products','bst/product-categories','bst/countdown','bst/product-part','core/paragraph','core/heading','core/image','core/gallery','core/video','core/cover','core/social-links','core/site-logo','core/navigation','core/search','core/query','core/buttons','core/group','core/columns','core/shortcode'];
  function blockLabel(b,type) { var a=b.attributes||{}, value=a.text||a.content||a.title||a.heading||a.label; if (Array.isArray(value)) value=''; if (typeof value==='string') value=value.replace(/<[^>]*>/g,' ').trim(); return (type?type.title:b.name)+(value?' · '+value.slice(0,38):''); }
  function tree(list) { return el('ol',{className:'bst-builder-tree'},list.map(function (b, index) {
   var type=wp.blocks.getBlockType(b.name), hasChildren=b.innerBlocks.length>0, isOpen=!!expanded[b.clientId]; return el('li',{key:b.clientId,draggable:!(b.attributes.lock && b.attributes.lock.move),className:(wp.data.select('core/block-editor').isBlockSelected(b.clientId)?'is-selected':'')+(hasChildren?' has-children':'') ,onDragStart:function(e){e.stopPropagation();e.dataTransfer.setData('text/x-bigspy-block',b.clientId);},onDragOver:function(e){e.preventDefault();e.stopPropagation();},onDrop:function(e){e.preventDefault();e.stopPropagation();var id=e.dataTransfer.getData('text/x-bigspy-block'),store=wp.data.select('core/block-editor'),root=store.getBlockRootClientId(b.clientId);if(id && id!==b.clientId && store.getBlockRootClientId(id)===root && store.canMoveBlocks([id],root)){wp.data.dispatch('core/block-editor').moveBlocksToPosition([id],root,root,index);}}},
    el('div',{className:'bst-builder-tree-row'},hasChildren?el('button',{type:'button',className:'bst-tree-toggle','aria-label':isOpen?'Thu gọn':'Mở rộng','aria-expanded':isOpen,onClick:function(){var next=Object.assign({},expanded);next[b.clientId]=!isOpen;setExpanded(next);}},isOpen?'▾':'▸'):el('span',{className:'bst-tree-spacer'}),el('button',{type:'button',className:'bst-tree-select',onClick:function () {wp.data.dispatch('core/block-editor').selectBlock(b.clientId);setInspectorOpen(true);var node=document.querySelector('[data-block="'+b.clientId+'"]');if(node)node.scrollIntoView({block:'center',behavior:'smooth'});}},blockLabel(b,type)),
    el('span',{className:'bst-tree-order'},el('button',{type:'button',disabled:index===0 || !!(b.attributes.lock && b.attributes.lock.move),title:'Di chuyển lên',onClick:function () {wp.data.dispatch('core/block-editor').moveBlocksUp([b.clientId]);}},'↑'),el('button',{type:'button',disabled:index===list.length-1 || !!(b.attributes.lock && b.attributes.lock.move),title:'Di chuyển xuống',onClick:function () {wp.data.dispatch('core/block-editor').moveBlocksDown([b.clientId]);}},'↓'))),
    hasChildren&&isOpen?tree(b.innerBlocks):null);
  })); }
  function libraryPanel() {
   if (panel==='structure') return el('div',{className:'bst-builder-structure'},el('h2',null,'Cấu trúc trang'),el('p',{className:'bst-builder-panel-hint'},'Chọn khối để sửa · Dùng mũi tên để đổi thứ tự · Bấm ▸ để mở nhóm'),tree(blocks));
   var categories=['Tất cả','Bố cục','Bán hàng','Nội dung','WordPress'];
   function inCategory(n) { if(category==='Tất cả')return true;if(category==='Bố cục')return /section|row|banner|slider|group|columns/.test(n);if(category==='Bán hàng')return /product|countdown/.test(n);if(category==='WordPress')return n.indexOf('core/')===0&&!/paragraph|heading|image|gallery|video|cover|buttons/.test(n);return /title|text|image|button|icon-box|tabs|accordion|posts|paragraph|heading|gallery|video|cover/.test(n); }
   var catalog=names.filter(function (n) {var t=wp.blocks.getBlockType(n);return t&&String(t.title).toLowerCase().indexOf(query.toLowerCase())!==-1&&inCategory(n);});
   var patterns=(cfg.patterns||[]).filter(function (p) {return p.title.toLowerCase().indexOf(query.toLowerCase())!==-1;});
   var items=libraryTab==='elements'?el('div',{className:'bst-builder-elements'},catalog.map(function (n) {var type=wp.blocks.getBlockType(n);return el('div',{key:n,draggable:true,onDragStart:function(e){e.dataTransfer.setData('text/x-bigspy-element',n);e.dataTransfer.effectAllowed='copy';}},el(c.Button,{variant:'secondary',onClick:function(){add(n);}},type.title));})):el('div',{className:'bst-builder-patterns'},patterns.map(function (p) {return el(c.Button,{key:p.name,variant:'secondary',onClick:function(){var insert=wp.blocks.parse(p.content),store=wp.data.select('core/block-editor'),selected=store.getSelectedBlockClientId(),root=selected?store.getBlockRootClientId(selected):undefined,index=selected?store.getBlockIndex(selected,root)+1:store.getBlockOrder().length;wp.data.dispatch('core/block-editor').insertBlocks(insert,index,root);setPanel('structure');}},p.title);}));
   return el('div',{className:'bst-builder-library-panel'},el('h2',null,'Chọn hoặc kéo vào trang'),el('div',{className:'bst-builder-library-tabs'},el('button',{type:'button',className:libraryTab==='elements'?'is-active':'',onClick:function(){setLibraryTab('elements');}},'Phần tử'),el('button',{type:'button',className:libraryTab==='patterns'?'is-active':'',onClick:function(){setLibraryTab('patterns');}},'Mẫu bố cục')),libraryTab==='elements'?el(c.TextControl,{label:'Tìm phần tử',value:query,onChange:setQuery}):null,libraryTab==='elements'?el('div',{className:'bst-builder-category-filters'},categories.map(function(x){return el('button',{key:x,type:'button',className:category===x?'is-active':'',onClick:function(){setCategory(x);}},x);})):null,items);
  }
  return el(c.SlotFillProvider,null,el(be.BlockEditorProvider,{value:blocks,onInput:change,onChange:change,settings:settings},
   el('div',{className:'bst-builder-shell'+(libraryOpen?' is-library-open':'')+(inspectorOpen?' is-inspector-open':'')},
    el('header',{className:'bst-builder-top'},el('a',{href:cfg.editUrl,className:'bst-builder-exit','aria-label':'Đóng builder'},'×'),el('strong',null,'BIGSPY AI Builder'),el('input',{'aria-label':'Tên trang',value:title,onChange:function (e) {setTitle(e.target.value);}}),
     el(c.Button,{variant:'secondary',onClick:undo,disabled:!history.current.length},'Hoàn tác'),el(c.Button,{variant:'secondary',onClick:redo,disabled:!future.current.length},'Làm lại'),
     el(c.Button,{variant:'secondary',onClick:function () {setMode(mode==='design'?'code':'design');}},mode==='design'?'Xem mã':'Thiết kế'),
     el('a',{href:cfg.editUrl},'Sửa trang WordPress'),el(c.Button,{variant:'primary',onClick:save,disabled:saving || !dirty},saving?'Đang lưu…':cfg.status==='publish'?'Lưu lên website':'Lưu bản hiện tại')),
    el('div',{className:'bst-builder-status',role:'status'},notice || (dirty?'Có thay đổi chưa lưu':'Nội dung được lưu bằng khối WordPress. Bấm vào phần tử để sửa.')),
    el('div',{className:'bst-builder-body'},el('aside',{className:'bst-builder-library'},el('div',{className:'bst-builder-panel-tabs'},el(c.Button,{variant:panel==='elements'?'primary':'secondary',onClick:function(){setPanel('elements');setLibraryOpen(true);}},'+ Thêm phần tử'),el(c.Button,{variant:panel==='structure'?'primary':'secondary',onClick:function(){setPanel('structure');setLibraryOpen(false);}},'Cấu trúc')),libraryPanel()),
     el('main',{className:'bst-builder-stage'},el('nav',{'aria-label':'Kích thước xem trước'},el(c.Button,{variant:inspectorOpen?'primary':'secondary',onClick:function(){setInspectorOpen(!inspectorOpen);}},inspectorOpen?'Ẩn thiết lập':'Thiết lập phần tử'),['desktop','tablet','mobile'].map(function (d) {return el(c.Button,{key:d,variant:device===d?'primary':'secondary',onClick:function () {setDevice(d);}}, {desktop:'Máy tính',tablet:'Tablet',mobile:'Điện thoại'}[d]);})),
      mode==='code'?el('textarea',{className:'bst-builder-code',defaultValue:serialized,'aria-label':'Mã khối WordPress',onBlur:function (e) {change(wp.blocks.parse(e.target.value));}}):el('div',{className:'bst-builder-canvas bst-builder-canvas--'+device,onDragOver:function(e){if(Array.from(e.dataTransfer.types).indexOf('text/x-bigspy-element')!==-1){e.preventDefault();e.dataTransfer.dropEffect='copy';}},onDrop:dropElement},el(be.BlockTools,null,el(be.WritingFlow,null,el(be.ObserveTyping,null,el(be.BlockList))))),
      el('p',{className:'bst-builder-help'},'Khung thiết kế mô phỏng độ rộng thiết bị. Xem website thực tế sau khi lưu để kiểm tra header, footer và các plugin.')),
     el('aside',{className:'bst-builder-inspector'},el('h2',null,'Thiết lập phần tử'),el(be.BlockInspector))),el(c.Popover.Slot))));
 }
 try { wp.element.createRoot(document.getElementById('bst-visual-builder')).render(el(Workspace)); } catch (err) { document.getElementById('bst-visual-builder').textContent='Không mở được builder. Hãy dùng Sửa trang WordPress. '+err.message; }
})(window.wp, window.bstBuilderData);
