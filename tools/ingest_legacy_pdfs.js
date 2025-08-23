const fs = require('fs');
const path = require('path');
const pdf = require('pdf-parse');

const SRC = 'assets/pdfs/legacy';
const MD_DIR = 'assets/md';
const OUT = 'assets/data/datacards.json';

function slugify(s){ return String(s||'').toLowerCase().replace(/[^a-z0-9]+/g,'-').replace(/(^-|-$)/g,''); }
function toNum(x){ return x ? +String(x).replace(/,/g,'') : 0; }
function parseUSDate(d){ const m=/(\d{2})\/(\d{2})\/(\d{4})/.exec(d||''); return m? `${m[3]}-${m[1]}-${m[2]}`:''; }
function firstLine(s){ return (s||'').split(/\n+/).map(x=>x.trim()).filter(Boolean)[0]||''; }

(async ()=>{
  const files = fs.existsSync(SRC) ? fs.readdirSync(SRC).filter(f=>f.toLowerCase().endsWith('.pdf')) : [];
  if(!files.length){ console.error('No PDFs in', SRC); process.exit(0); }

  const existing = fs.existsSync(OUT) ? JSON.parse(fs.readFileSync(OUT,'utf8')) : [];
  const byId = new Map(existing.map(c => [String(c.card_id ?? c.id ?? slugify(c.title||'')), c]));

  for (const f of files){
    const buf = fs.readFileSync(path.join(SRC,f));
    const text = (await pdf(buf)).text.replace(/\r/g,'');
    const x = re => (text.match(re)||[])[1]?.trim() || '';

    // Core fields
    const title = x(/^\s*([A-Z][^\n]+?)\s*\n(?:SEGMENTS|DESCRIPTION|ID NUMBER|UNIVERSE)/m) || firstLine(text) || path.parse(f).name;
    const nextmark = x(/NextMark\s+(\d{3,})/i);
    const universe = x(/([\d,]+)\s+TOTAL UNIVERSE/i) || x(/UNIVERSE\s*\n\s*([\d,]+)/i);
    const base = x(/BASE RATE\s*\$?(\d+(?:\.\d+)?)\s*\/?M/i);
    const lastUpd = x(/Last update\s+(\d{2}\/\d{2}\/\d{4})/i);
    const source = x(/SOURCE\s*\n\s*([^\n]+)/i);
    const geography = x(/GEOGRAPHY\s*\n\s*([^\n]+)/i);

    // Description block
    let desc = '';
    {
      const m = text.match(/DESCRIPTION\s*\n([\s\S]*?)(?:\nID NUMBER|\nSELECTS|\nGEOGRAPHY|\nLIST MAINTENANCE|\nGENDER PROFILE|\nMINIMUM ORDER|\nNET NAME|\nEXCHANGES|\nKEY CODING|\nADDRESSING|$)/i);
      if(m) desc = m[1].replace(/\n{2,}/g,'\n\n').trim();
    }

    // Segments
    const segBlock = (text.match(/SEGMENTS[^\n]*\n([\s\S]*?)(?:\nDESCRIPTION|\nID NUMBER|\nLIST MAINTENANCE|$)/i)||[])[1] || '';
    const segments=[];
    segBlock.split('\n').forEach(line=>{
      const l=line.trim().replace(/\s+/g,' ');
      let m = l.match(/^([\d,]+)\s+(.+?)\s+\$?(\d+(?:\.\d+)?)\s*\/?M/i);
      if(m) { segments.push({ segment:m[2], count:toNum(m[1]), cpm:+m[3] }); return; }
      m = l.match(/^([\d,]+)\s+TOTAL UNIVERSE.*\$?(\d+(?:\.\d+)?)\s*\/?M/i);
      if(m) segments.push({ segment:'TOTAL UNIVERSE', count:toNum(m[1]), cpm:+m[2] });
    });

    // Selects
    const selBlock = (text.match(/SELECTS\s*\n([\s\S]*?)(?:\nGEOGRAPHY|\nUNIT OF SALE|\nGENDER|\nMINIMUM ORDER|\nNET NAME|\nEXCHANGES|\nKEY CODING|\nADDRESSING|$)/i)||[])[1] || '';
    const selects=[];
    selBlock.split('\n').forEach(line=>{
      const l=line.trim().replace(/\s+/g,' ');
      const m=l.match(/^(.+?)\s+\$?(\d+(?:\.\d+)?)\s*\/?M$/i);
      if(m) selects.push({ select:m[1], cpm:+m[2] });
    });

    // Gender
    const male = x(/Male:\s*(\d+)%/i), female = x(/Female:\s*(\d+)%/i);

    // Write Markdown
    const slug = slugify(title);
    const mdPath = `${MD_DIR}/${slug}.md`;
    fs.mkdirSync(MD_DIR, {recursive:true});
    const md = `# ${title}

**Last updated:** ${lastUpd||'—'}  
**Universe:** ${universe||'—'}  
**Base rate:** ${base?`$${(+base).toFixed(2)}/M`:'—'}

${desc||''}

## Pricing
| Segment | Count | Price/M |
|---|---:|---:|
${segments.map(s=>`| ${s.segment} | ${s.count?.toLocaleString?.()||s.count||''} | ${s.cpm?`$${s.cpm.toFixed(2)}`:''} |`).join('\n')}

## Optional Selects
| Select | Price/M |
|---|---:|
${selects.length? selects.map(s=>`| ${s.select} | ${s.cpm?`$${s.cpm.toFixed(2)}`:''} |`).join('\n') : '| — | — |'}

## Audience Profile
${male?`Male: ${male}%  `:''}${female?`Female: ${female}%`:''}

**Source:** ${source||'—'}  
**Geography:** ${geography||'—'}
`;
    fs.writeFileSync(mdPath, md);

    const entry = {
      card_id: nextmark? +nextmark : undefined,
      title,
      universe_size: toNum(universe),
      base_rate_per_thousand: base? +base : 0,
      last_updated: parseUSDate(lastUpd),
      short_description: firstLine(desc).slice(0,240),
      source: source||'',
      geography: geography||'',
      pdf: `assets/pdfs/legacy/${f}`,
      markdown: mdPath
    };
    const key = String(entry.card_id||slug);
    const prev = byId.get(key);
    byId.set(key, {...prev, ...entry});
  }

  const out = [...byId.values()].sort((a,b)=>String(a.title||'').localeCompare(String(b.title||'')));
  fs.mkdirSync(path.dirname(OUT), {recursive:true});
  fs.writeFileSync(OUT, JSON.stringify(out, null, 2));
  console.log('Wrote', OUT, 'cards:', out.length);
})();
