require(process.argv[2]); const C=globalThis.bstFormCalc;
const msg={incomplete:'Choose more details to see the price',confirm:'Price to be confirmed'};
const out=[];
for(const c of JSON.parse(require('fs').readFileSync(process.argv[3]))){
 // build client config like bst_calc_client_config (formula '' when server finds an error is emulated by run-time checks)
 const fields={},calcs=[];const usable=['number','select','radio','checkbox','acceptance','calc'];const ids=Object.fromEntries(c.fields.map(f=>[f.id,f]));
 for(const f of c.fields){ if(f.type==='calc'){let ok=true;try{const t=C.parse(f.formula);ok=C.refs(t).every(r=>ids[r]&&usable.includes(ids[r].type));}catch(e){ok=false;} calcs.push({id:f.id,name:f.name,formula:ok?f.formula:'',currency:(f.currency||'VND').toUpperCase()});}
  else if(usable.includes(f.type)){const pr={};if(f.options)for(const l of f.options.split('\n')){const[a,b]=l.split('|').map(s=>s.trim());pr[a]=b===undefined?null:C.num(b);} fields[f.id]={name:f.name,type:f.type,required:!!f.required,prices:pr};}}
 for(const r of c.runs){ const res=C.run({fields,calcs,msg},n=>{const v=r[n]||''; return fields[Object.keys(fields).find(k=>fields[k].name===n)]?.type==='checkbox'?(v?v.split(', '):[]):v;}); const o={}; for(const k of Object.keys(res).sort()) o[k]=res[k].status+':'+res[k].text; out.push(c.label+' '+JSON.stringify(r)+' => '+JSON.stringify(o)); }
}
console.log(out.join('\n'));
