import { createBuilder3D } from './builder-3d.js';
const $=id=>document.getElementById(id);
const products=JSON.parse($('builder-products-json')?.textContent||'[]');
const category=$('builder-category'),product=$('builder-product'),preview=$('builder-preview'),installButton=$('builder-install'),status=$('builder-status'),summary=$('builder-selected'),total=$('builder-total'),partTitle=$('builder-part-title');
const chosen=new Map();let viewer=null,busy=false;
const compatStatus=$('builder-compat-status'),compatChecks=$('builder-compat-checks');
let checkVersion=0;
async function updateCompatibility(){
 const version=++checkVersion;
 compatChecks.replaceChildren();
 if(!chosen.size){compatStatus.textContent='Choose components to check compatibility.';compatStatus.dataset.state='unknown';return;}
 compatStatus.textContent='Checking compatibility…';compatStatus.dataset.state='unknown';
 try{
  const selected=Object.fromEntries([...chosen].map(([slot,p])=>[slot,p.id]));
  const response=await fetch('../../Controllers/compatibility.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({products:selected})});
  const payload=await response.json();
  if(version!==checkVersion)return;
  if(!response.ok||!payload.success)throw new Error(payload.message||'Check failed');
  const result=payload.data;
  compatStatus.dataset.state=result.status;
  compatStatus.textContent=result.status==='incompatible'?'Incompatible components detected':result.checks.length?'Compatibility not fully verified':'Select two or more related components to run checks';
  for(const check of result.checks){
   const item=document.createElement('div');item.className='builder-compat-item';item.dataset.state=check.status;
   const heading=document.createElement('strong');heading.textContent=(check.status==='compatible'?'✓ ':check.status==='incompatible'?'✕ ':'? ')+check.title;
   const detail=document.createElement('small');detail.textContent=check.message;
   item.append(heading,detail);compatChecks.append(item);
  }
 }catch(error){if(version!==checkVersion)return;compatStatus.textContent='Compatibility check unavailable: '+error.message;compatStatus.dataset.state='unknown';}
}

const parts={case:'case',motherboard:'motherboard',gpu:'gpu',ram:'ram',cpu:'cpu',storage:'storage',psu:'psu',cooler:'cooler'};
function slotFor(s){s=s.toLowerCase();if(s.includes('motherboard'))return 'motherboard';if(s.includes('cooler'))return 'cooler';if(s.includes('processor')||s.includes('cpu'))return 'cpu';if(s.includes('graphic')||s.includes('gpu'))return 'gpu';if(s.includes('memory')||s.includes('ram'))return 'ram';if(s.includes('storage')||s.includes('ssd')||s.includes('hdd'))return 'storage';if(s.includes('power')||s.includes('psu'))return 'psu';if(s.includes('case'))return 'case';return null;}
function option(v,t){const e=document.createElement('option');e.value=v;e.textContent=t;return e;}
function label(t,c){const e=document.createElement(t);e.textContent=c;return e;}
const getProduct=()=>products.find(p=>p.id===Number(product.value));
[...new Set(products.map(p=>p.category).filter(x=>slotFor(x)))].forEach(x=>category.append(option(x,x)));
category.addEventListener('change',()=>{product.replaceChildren(option('','Choose a product'));products.filter(p=>p.category===category.value).forEach(p=>product.append(option(p.id,`${p.name} — ${Number(p.price).toFixed(2)} JD`)));product.disabled=product.options.length===1;preview.textContent='Select a product to preview it.';installButton.disabled=true;});
product.addEventListener('change',()=>{const p=getProduct();preview.replaceChildren();if(p){const type=slotFor(p.category);preview.append(label('div',type?.toUpperCase()||''),label('strong',p.name),label('span',`${Number(p.price).toFixed(2)} JD · ${p.stock} in stock`));}else preview.textContent='Select a product to preview it.';installButton.disabled=!p||p.stock<1||!viewer||busy;});
function render(){summary.replaceChildren();if(!chosen.size)summary.append(label('p','No components selected yet.'));for(const [key,p] of chosen){const row=document.createElement('div');row.className='builder-item';const detail=document.createElement('div');detail.append(label('strong',p.name),label('small',`${key.toUpperCase()} · ${Number(p.price).toFixed(2)} JD`));const remove=label('button','×');remove.type='button';remove.setAttribute('aria-label','Remove '+p.name);remove.addEventListener('click',()=>{if(busy)return;chosen.delete(key);if(key!=='case')viewer?.remove(key);render();});row.append(detail,remove);summary.append(row);}total.textContent=[...chosen.values()].reduce((a,p)=>a+Number(p.price),0).toFixed(2)+' JD';updateCompatibility();}
installButton.addEventListener('click',async()=>{if(busy||!viewer)return;const p=getProduct();if(!p||p.stock<1)return;const key=slotFor(p.category);if(!key)return;busy=true;installButton.disabled=true;partTitle.textContent=p.name;status.textContent='Installing '+p.name+'…';try{await viewer.install(key);chosen.set(key,p);render();status.textContent=p.name+' installed visually. Compatibility checks updated.';}catch(e){status.textContent='Unable to load this 3D part: '+e.message;}finally{busy=false;installButton.disabled=!getProduct()||getProduct().stock<1;}});
$('builder-reset').addEventListener('click',()=>{if(busy)return;chosen.clear();viewer?.reset();partTitle.textContent='Choose a component';status.textContent='Build reset.';render();});
render();
createBuilder3D($('builder-viewport'),msg=>{status.textContent=msg;}).then(v=>{viewer=v;status.textContent='3D case loaded. Rotate with your mouse and select a product.';product.dispatchEvent(new Event('change'));}).catch(e=>{status.textContent='3D viewer could not start: '+e.message;});
