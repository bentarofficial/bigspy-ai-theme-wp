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
  var ip = useState(false), inspectorOpen = ip[0], setInspectorOpen = ip[1];
  var qp = useState(''), query = qp[0], setQuery = qp[1];
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
  function dropElement(e) { var name=e.dataTransfer.getData('text/x-bigspy-element'); if(!name) return; e.preventDefault(); e.stopPropagation(); var store=wp.data.select('core/block-editor'), target=e.target.closest('[data-block]'), id=target && target.getAttribute('data-block'), root=id ? store.getBlockRootClientId(id) : undefined, index=id ? store.getBlockIndex(id,root) : store.getBlockOrder().length; if(store.canInsertBlockType(name,root)) { wp.data.dispatch('core/block-editor').insertBlocks(wp.blocks.createBlock(name),index,root); setLibraryOpen(false); } }
  function add(name) { var type = wp.blocks.getBlockType(name); if (!type || type.parent) return; change(blocks.concat([wp.blocks.createBlock(name)])); }
  var names = ['bst/section','bst/row','bst/banner','bst/slider','bst/title','bst/text','bst/image','bst/buttons','bst/icon-box','bst/tabs','bst/accordion','bst/posts','bst/products','bst/product-categories','bst/countdown','bst/product-part','core/paragraph','core/heading','core/image','core/gallery','core/video','core/cover','core/social-links','core/site-logo','core/navigation','core/search','core/query','core/buttons','core/group','core/columns','core/shortcode'];
  function tree(list) { return el('ol',{className:'bst-builder-tree'},list.map(function (b, index) {
   var type = wp.blocks.getBlockType(b.name); return el('li',{key:b.clientId,draggable:!(b.attributes.lock && b.attributes.lock.move),onDragStart:function(e){e.stopPropagation();e.dataTransfer.setData('text/x-bigspy-block',b.clientId);},onDragOver:function(e){e.preventDefault();e.stopPropagation();},onDrop:function(e){e.preventDefault();e.stopPropagation();var id=e.dataTransfer.getData('text/x-bigspy-block'),store=wp.data.select('core/block-editor'),root=store.getBlockRootClientId(b.clientId);if(id && id!==b.clientId && store.getBlockRootClientId(id)===root && store.canMoveBlocks([id],root)){wp.data.dispatch('core/block-editor').moveBlocksToPosition([id],root,root,index);}}},el('button',{type:'button',onClick:function () {wp.data.dispatch('core/block-editor').selectBlock(b.clientId);setInspectorOpen(true);}},(type ? type.title : b.name)),
    el('span',null,el('button',{type:'button',disabled:index===0 || !!(b.attributes.lock && b.attributes.lock.move),title:'Di chuyển lên',onClick:function () {wp.data.dispatch('core/block-editor').moveBlocksUp([b.clientId]);}},'↑'),el('button',{type:'button',disabled:index===list.length-1 || !!(b.attributes.lock && b.attributes.lock.move),title:'Di chuyển xuống',onClick:function () {wp.data.dispatch('core/block-editor').moveBlocksDown([b.clientId]);}},'↓')),
    b.innerBlocks.length ? tree(b.innerBlocks) : null);
  })); }
  return el(c.SlotFillProvider,null,el(be.BlockEditorProvider,{value:blocks,onInput:change,onChange:change,settings:settings},
   el('div',{className:'bst-builder-shell'+(libraryOpen?' is-library-open':'')+(inspectorOpen?' is-inspector-open':'')},
    el('header',{className:'bst-builder-top'},el('a',{href:cfg.editUrl,className:'bst-builder-exit','aria-label':'Đóng builder'},'×'),el('strong',null,'BIGSPY AI Builder'),el('input',{'aria-label':'Tên trang',value:title,onChange:function (e) {setTitle(e.target.value);}}),
     el(c.Button,{variant:'secondary',onClick:undo,disabled:!history.current.length},'Hoàn tác'),el(c.Button,{variant:'secondary',onClick:redo,disabled:!future.current.length},'Làm lại'),
     el(c.Button,{variant:'secondary',onClick:function () {setMode(mode==='design'?'code':'design');}},mode==='design'?'Xem mã':'Thiết kế'),
     el('a',{href:cfg.editUrl},'Sửa trang WordPress'),el(c.Button,{variant:'primary',onClick:save,disabled:saving || !dirty},saving?'Đang lưu…':cfg.status==='publish'?'Lưu lên website':'Lưu bản hiện tại')),
    el('div',{className:'bst-builder-status',role:'status'},notice || (dirty?'Có thay đổi chưa lưu':'Nội dung được lưu bằng khối WordPress. Bấm vào phần tử để sửa.')),
    el('div',{className:'bst-builder-body'},el('aside',{className:'bst-builder-library'},el(c.Button,{variant:libraryOpen?'primary':'secondary',onClick:function(){setLibraryOpen(!libraryOpen);}},libraryOpen?'Đóng thư viện':'+ Thêm phần tử'),libraryOpen ? el('div',{className:'bst-builder-library-panel'},el('h2',null,'Chọn hoặc kéo vào trang'),el(c.TextControl,{label:'Tìm phần tử / mẫu',value:query,onChange:setQuery}),el('div',{className:'bst-builder-elements'},names.filter(function (n) {var t=wp.blocks.getBlockType(n); return t && String(t.title).toLowerCase().indexOf(query.toLowerCase())!==-1;}).map(function (n) {return el('div',{key:n,draggable:true,onDragStart:function(e){e.dataTransfer.setData('text/x-bigspy-element',n);e.dataTransfer.effectAllowed='copy';}},el(c.Button,{variant:'secondary',onClick:function () {add(n);setLibraryOpen(false);}},wp.blocks.getBlockType(n).title));})),
     el('h2',null,'Mẫu thiết kế'),(cfg.patterns||[]).filter(function (p) {return p.title.toLowerCase().indexOf(query.toLowerCase())!==-1;}).map(function (p) {return el(c.Button,{key:p.name,variant:'tertiary',onClick:function () {change(blocks.concat(wp.blocks.parse(p.content)));}},p.title);})) : null,el('h2',null,'Cấu trúc trang'),tree(blocks)),
     el('main',{className:'bst-builder-stage'},el('nav',{'aria-label':'Kích thước xem trước'},el(c.Button,{variant:inspectorOpen?'primary':'secondary',onClick:function(){setInspectorOpen(!inspectorOpen);}},inspectorOpen?'Ẩn thiết lập':'Thiết lập phần tử'),['desktop','tablet','mobile'].map(function (d) {return el(c.Button,{key:d,variant:device===d?'primary':'secondary',onClick:function () {setDevice(d);}}, {desktop:'Máy tính',tablet:'Tablet',mobile:'Điện thoại'}[d]);})),
      mode==='code'?el('textarea',{className:'bst-builder-code',defaultValue:serialized,'aria-label':'Mã khối WordPress',onBlur:function (e) {change(wp.blocks.parse(e.target.value));}}):el('div',{className:'bst-builder-canvas bst-builder-canvas--'+device,onDragOver:function(e){if(Array.from(e.dataTransfer.types).indexOf('text/x-bigspy-element')!==-1){e.preventDefault();e.dataTransfer.dropEffect='copy';}},onDrop:dropElement},el(be.BlockTools,null,el(be.WritingFlow,null,el(be.ObserveTyping,null,el(be.BlockList))))),
      el('p',{className:'bst-builder-help'},'Khung thiết kế mô phỏng độ rộng thiết bị. Xem website thực tế sau khi lưu để kiểm tra header, footer và các plugin.')),
     el('aside',{className:'bst-builder-inspector'},el('h2',null,'Thiết lập phần tử'),el(be.BlockInspector))),el(c.Popover.Slot))));
 }
 try { wp.element.createRoot(document.getElementById('bst-visual-builder')).render(el(Workspace)); } catch (err) { document.getElementById('bst-visual-builder').textContent='Không mở được builder. Hãy dùng Sửa trang WordPress. '+err.message; }
})(window.wp, window.bstBuilderData);
