# VERSION: web-v3-aug24
import json, os, re, sys, html
from pathlib import Path

ALL_CAPS = r"(?:[A-Z][A-Z \-/&]+)"
EXTRACT_BLOCK_RE = lambda title: re.compile(rf"(?:^|\n){title}\s*\n(.*?)(?=\n{ALL_CAPS}\s*\n|\Z)", re.S)

def _clean(s): return (s or "").strip()

def try_extract_pdf_text(path: Path) -> str:
    try:
        from pdfminer.high_level import extract_text
        return (extract_text(str(path)) or "").replace("\r\n","\n").replace("\r","\n")
    except Exception:
        return ""

def read_text_any(p: Path) -> str:
    return try_extract_pdf_text(p) if p.suffix.lower()==".pdf" else p.read_text(encoding="utf-8", errors="ignore")

def slugify(s: str) -> str:
    s = s.lower().strip()
    s = re.sub(r"[^a-z0-9\s-]", "", s)
    s = re.sub(r"[\s-]+", "-", s)
    return s[:75] if s else "untitled"

def norm_lines(text: str): return [ln.rstrip() for ln in text.split("\n")]

def plausible_title(lines, fallback):
    date_re = re.compile(r"^\s*\d{1,2}/\d{1,2}/\d{2,4}(?:,\s*\d{1,2}:\d{2}\s*(?:AM|PM))?\s*$", re.I)
    noise = {"blog","price","segments","description","id number","universe","list type","source"}
    def looks_title(t):
        if len(t) < 6 or len(t) > 100: return False
        if date_re.match(t): return False
        if re.fullmatch(ALL_CAPS, t): return False
        if any(tok in t.lower() for tok in noise): return False
        return len([w for w in re.findall(r"[A-Z][a-z]+", t)]) >= 2
    for ln in lines[:60]:
        t = ln.strip()
        if looks_title(t): return t
    t = re.sub(r"[_\-]+"," ", fallback).strip()
    return t.title() if t else "Untitled"

def block_lines(text, heading):
    m = EXTRACT_BLOCK_RE(heading).search(text)
    if not m: return []
    lines = [ln.strip() for ln in m.group(1).split("\n")]
    return [ln for ln in lines if ln]

def kv_search(text, label, pat=r":\s*(.+)"):
    m = re.search(rf"\b{re.escape(label)}{pat}", text, re.I)
    return _clean(m.group(1) if m else "")

def find_universe(text):
    m = re.search(r"\bUNIVERSE\b[:\s]*([\d,]+)", text, re.I)
    if m: return m.group(1)
    for name, price in parse_segments(text):
        if "TOTAL UNIVERSE" in name.upper():
            n = re.search(r"([\d,]+)", name)
            if n: return n.group(1)
    return ""

def find_base_rate(text):
    m = re.search(r"\bBASE(?:\s+RATE)?\b[:\s]*\$\s*([\d,]+(?:\.\d{2})?)\s*/?M\b", text, re.I)
    if m: return f"${m.group(1)}/M"
    for name, price in parse_segments(text):
        if "TOTAL UNIVERSE" in name.upper() and price: return price
    return ""

def find_last_update(text):
    m = re.search(r"\b(Last update)\b[:\s]*([0-9]{2,4}[-/][0-9]{2}[-/][0-9]{2})", text, re.I)
    return m.group(2) if m else ""

def parse_segments(text):
    segs=[]
    for ln in block_lines(text, "SEGMENTS"):
        ln = re.sub(r"\s{2,}", " ", ln).strip()
        m = re.search(r"(.*?)[\s\-]*\$\s*(\d+(?:\.\d{2})?)/(?:M|F)\b", ln, re.I)
        if m: segs.append((m.group(1).strip(), f"${m.group(2)}/M"))
        else: segs.append((ln, ""))
    return segs

def first_paragraph(text):
    desc = block_lines(text, "DESCRIPTION")
    if desc:
        # scrub placeholders like "DESCRIPTION _X"
        desc = [re.sub(r"\bDESCRIPTION\s*_?X\b","", ln, flags=re.I).strip() for ln in desc]
        body = " ".join([ln for ln in desc if ln])
        body = re.sub(r"\s{2,}", " ", body)
        body = body.replace(" * ", " • ")
        return body.strip()
    for ln in norm_lines(text):
        t = ln.strip()
        if not re.fullmatch(ALL_CAPS, t) and len(t) > 80:
            return t
    return ""

def parse_selects(text):
    out=[]
    for ln in block_lines(text, "SELECTS"):
        m = re.search(r"(.+?)\s+\$?(\d+(?:\.\d{2})?)/(?:M|F)\b", ln)
        out.append({"name": m.group(1).strip(), "price": f"${m.group(2)}"} if m else {"name": ln.strip(), "price": ""})
    return out

def parse_contacts(text):
    blk = block_lines(text, "CONTACTS")
    items=[]; email_re = re.compile(r"([A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.[A-Z]{2,})", re.I); phone_re = re.compile(r"\+?\d[\d \-\(\)]{7,}\d")
    for ln in blk:
        email = (email_re.search(ln) or [None,None])[1]
        phone_m = phone_re.search(ln)
        phone = phone_m.group(0) if phone_m else ""
        name = re.sub(r"^(?:NAME|CONTACT)\s*[:=\-]\s*","", ln, flags=re.I).strip()
        name = re.sub(email_re, "", name).strip()
        name = re.sub(phone_re, "", name).strip(" -,:")
        if not any([email, phone, name]): continue
        items.append({"name": name, "email": email or "", "phone": phone})
    return items

def parse_id_block(text):
    blk = block_lines(text, "ID NUMBER")
    id_system=id_number=manager=""
    for ln in blk:
        m = re.search(r"([A-Za-z][A-Za-z ]+)\s+([0-9]{3,})", ln)
        if m: id_system, id_number = m.group(1).strip(), m.group(2).strip()
        if "manager" in ln.lower(): manager = "Manager"
    return id_system, id_number, manager

CSS = """
:root { --brand:#0A2342; --accent:#C22B2B; --ink:#111827; }
*{box-sizing:border-box}
body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,Helvetica,Arial,sans-serif;margin:0;background:#f8fafc;color:var(--ink)}
header{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:16px 20px;background:#fff;border-bottom:1px solid #e5e7eb;position:sticky;top:0}
.back{color:#0969da;text-decoration:none}
.actions{display:flex;gap:8px}
.btn{border:1px solid #d0d7de;padding:.5rem .9rem;border-radius:.5rem;background:#fff;cursor:pointer}
.btn.primary{background:var(--accent);color:#fff;border-color:var(--accent)}
.page{max-width:1100px;margin:24px auto;padding:0 16px}
h1{margin:.2rem 0 8px;font:800 30px/1.2 system-ui}
.kv, .tbl {width:100%;border-collapse:collapse}
.kv th{width:230px;text-align:left;color:#374151;background:#f3f4f6;border-top:1px solid #e5e7eb;padding:10px}
.kv td{border-top:1px solid #e5e7eb;padding:10px}
.tbl th,.tbl td{border-top:1px solid #e5e7eb;padding:10px;text-align:left}
.tbl th.num,.tbl td.num{text-align:right;white-space:nowrap}
.block{background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:16px;margin:14px 0}
.headergrid{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.hsection h3{margin:0 0 8px;font:700 12px/1 system-ui;letter-spacing:.08em;color:#111;text-transform:uppercase}
.hline{height:2px;background:#111;margin:6px 0 12px}
.section-title{font:800 11px/1 system-ui;color:#111;text-transform:uppercase;letter-spacing:.08em;border-top:2px solid #111;padding-top:8px;margin:18px 0 10px}
p{margin:0}
@media print{header{display:none}.page{margin:0;max-width:none}.block{page-break-inside:avoid}}
"""
def esc(s): return html.escape(s or "")

def render_segments_table(segments):
    if not segments: return ""
    rows = "".join(f"<tr><td>{esc(n)}</td><td class='num'>{esc(p)}</td></tr>" for n,p in segments)
    return f"<div class='hsection'><h3>Segments</h3><div class='hline'></div><table class='tbl'><tbody>{rows}</tbody></table></div>"

def render_price_id(base_rate, id_system, id_number, manager):
    cells=[]
    if base_rate: cells.append(f"<tr><th>Base Rate</th><td class='num'>{esc(base_rate)}</td></tr>")
    if id_system or id_number: cells.append(f"<tr><th>ID Number</th><td>{esc(id_system)} {esc(id_number)}</td></tr>")
    if manager: cells.append(f"<tr><th>Manager</th><td>{esc(manager)}</td></tr>")
    if not cells: return ""
    return f"<div class='hsection'><h3>Price / ID</h3><div class='hline'></div><table class='kv'><tbody>{''.join(cells)}</tbody></table></div>"

def render_headergrid(segments, base_rate, id_system, id_number, manager):
    left = render_segments_table(segments)
    right = render_price_id(base_rate, id_system, id_number, manager)
    if not left and not right: return ""
    if left and not right: right = "<div></div>"
    if right and not left: left = "<div></div>"
    return f"<div class='block headergrid'>{left}{right}</div>"

def render_selects(selects):
    if not selects: return ""
    rows = "".join(f"<tr><td>{esc(s['name'])}</td><td class='num'>{esc(s['price'])}</td></tr>" for s in selects)
    return f"<div class='block'><div class='section-title'>Selects</div><table class='tbl'><tbody>{rows}</tbody></table></div>"

def render_contacts(contacts):
    if not contacts: return ""
    head = "<tr><th>Name</th><th>Email</th><th>Phone</th></tr>"
    rows=[]
    for c in contacts:
        email = c.get("email") or ""
        email_html = f"<a href='mailto:{esc(email)}'>{esc(email)}</a>" if email else ""
        rows.append(f"<tr><td>{esc(c.get('name',''))}</td><td>{email_html}</td><td>{esc(c.get('phone',''))}</td></tr>")
    return f"<div class='block'><div class='section-title'>Contacts</div><table class='tbl'><thead>{head}</thead><tbody>{''.join(rows)}</tbody></table></div>"

def render_detail_page(meta):
    header_html = render_headergrid(meta['segments'], meta['baseRate'], meta['idSystem'], meta['idNumber'], meta['manager'])
    summary_html = f"<div class='block'><div class='section-title'>Description</div><p>{esc(meta['description'])}</p></div>" if meta['description'] else ""
    kv_rows=[]
    for label,key in [("Universe","universe"),("List Type","listType"),("Source","source"),("Geography","geography"),
        ("Counts Through","countsThrough"),("Last Update","lastUpdate"),("Next Update","nextUpdate"),
        ("Minimum Order","minimumOrder"),("Net Name","netName"),("Exchanges","exchanges"),
        ("Key Coding","keyCoding"),("Addressing","addressing")]:
        val = meta.get(key,"")
        if val: kv_rows.append(f"<tr><th>{esc(label)}</th><td>{esc(val)}</td></tr>")
    details_html = f"<div class='block'><div class='section-title'>Details</div><table class='kv'><tbody>{''.join(kv_rows)}</tbody></table></div>" if kv_rows else ""
    selects_html = render_selects(meta['selects'])
    contacts_html = render_contacts(meta['contacts'])
    return f"""<!doctype html>
<html lang='en'><head><meta charset='utf-8'/><title>{esc(meta['title'])} | Allegiance List Marketing</title>
<meta name='viewport' content='width=device-width, initial-scale=1'/><style>{CSS}</style></head>
<body>
<header><a class='back' href='marketplace.html'>← Back to Marketplace</a>
<div class='actions'><button class='btn' onclick='location.reload()'>Refresh</button>
<button class='btn primary' onclick='window.print()'>Download PDF</button></div></header>
<article class='page'>
  <h1>{esc(meta['title'])}</h1>
  {header_html}
  {summary_html}
  {details_html}
  {selects_html}
  {contacts_html}
</article>
</body></html>"""

def is_placeholder(text, title):
    t = (title or "").strip().lower()
    if t.startswith("100% direct mail generated"): return True
    if re.search(r"DESCRIPTION\s*_?X", text, re.I): return True
    return False


def main():
    root = Path(__file__).resolve().parent.parent
    out_json = root / "assets/data/datacards.json"
    out_json.parent.mkdir(parents=True, exist_ok=True)
    src_dirs = [root/"assets/pdfs", root/"assets/pdfs/legacy", root/"assets/md", root/"assets/txt"]
    exts = {".pdf"}

    for f in root.glob("datacard-*.html"):
        try: f.unlink()
        except: pass

    items=[]; any_src=False
    for d in src_dirs:
        if not d.exists(): continue
        for name in sorted(os.listdir(d)):
            p = d/name
            if p.suffix.lower() not in exts: continue
            any_src=True
            raw = read_text_any(p)
            lines = norm_lines(raw)
            title = plausible_title(lines, p.stem)
            # fallback if the title is junk
            if title.strip().lower() in {"100% direct mail generated", "blog"}:
                title = re.sub(r"[_-]+"," ", p.stem).strip().title()
            if is_placeholder(raw, title):
                print(f"SKIP placeholder: {p.name}");
                continue
            meta = {
                "title": title,
                "universe": find_universe(raw),
                "baseRate": find_base_rate(raw),
                "lastUpdate": find_last_update(raw),
                "description": first_paragraph(raw),
                "selects": parse_selects(raw),
                "segments": parse_segments(raw),
                "contacts": parse_contacts(raw),
                "listType": kv_search(raw,"LIST TYPE"),
                "source": kv_search(raw,"SOURCE"),
                "geography": "USA" if re.search(r"\bGEOGRAPHY\b.*?\bUSA\b", raw, re.S|re.I) else kv_search(raw,"GEOGRAPHY"),
                "countsThrough": kv_search(raw,"Counts through"),
                "nextUpdate": kv_search(raw,"Next update"),
                "minimumOrder": " ".join(block_lines(raw,"MINIMUM ORDER")),
                "netName": " ".join(block_lines(raw,"NET NAME ARRANGEMENTS")),
                "exchanges": " ".join(block_lines(raw,"EXCHANGES")),
                "keyCoding": " ".join(block_lines(raw,"KEY CODING")) or kv_search(raw,"Key Coding"),
                "addressing": " ".join(block_lines(raw,"ADDRESSING")),
            }
            idSystem, idNumber, manager = parse_id_block(raw)
            meta.update({"idSystem":idSystem,"idNumber":idNumber,"manager":manager})

            # render page
            slug = slugify(title)
            (root/f"datacard-{slug}.html").write_text(render_detail_page(meta), encoding="utf-8")

            items.append({
                "id": slug, "title": title, "url": f"datacard-{slug}.html",
                "universe": meta["universe"], "baseRate": meta["baseRate"],
                "lastUpdate": meta["lastUpdate"], "description": (meta["description"] or "")[:350]
            })

    if not any_src: print("WARNING: no sources found in assets/pdfs(+legacy)/md/txt")
    items.sort(key=lambda x: x["title"].lower())
    out_json.write_text(json.dumps(items, indent=2), encoding="utf-8")
    print(f"Success: {len(items)} cards -> {out_json}\nGenerator VERSION web-v3-aug24")

if __name__ == "__main__":
    main()
