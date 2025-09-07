#!/usr/bin/env python3
import json, os, html, re

ROOT = os.path.dirname(os.path.abspath(__file__))
REPO = os.path.abspath(os.path.join(ROOT, ".."))
DATA_JSON = os.path.join(REPO, "assets", "data", "datacards.json")
MARKETPLACE_HTML = os.path.join(REPO, "marketplace.html")

def slugify(s: str) -> str:
    s = s.strip().lower()
    s = re.sub(r"[^a-z0-9\-]+", "-", s)
    s = re.sub(r"-{2,}", "-", s).strip("-")
    return s or "card"

def read_md(path: str) -> str:
    if not path:
        return ""
    abs_path = os.path.join(REPO, *path.split("/"))
    if os.path.isfile(abs_path):
        with open(abs_path, "r", encoding="utf-8", errors="ignore") as f:
            return f.read()
    return ""

def html_escape(s):
    return html.escape(str(s)) if s is not None else ""

def is_dsg(owner: str) -> bool:
    return (owner or "").strip().lower() == "dominion strategy group"


def card_tile(card) -> str:
    badge = '<span class="badge badge-dsg">A DSG Property</span>' if is_dsg(card.get("owner","")) else ""
    title = html_escape(card.get("title","Untitled"))
    # Normalize 'nan' from sheet
    def norm(v, default):
        t = "" if v is None else str(v).strip()
        return default if not t or t.lower()=="nan" else t
    universe = norm(card.get("universe"), "n/a")
    base = norm(card.get("baseRate"), "call")
    updated = norm(card.get("lastUpdate"), "n/a")
    pdf = norm(card.get("pdf"), "")
    # View -> open PDF in new tab; Download -> force download
    if pdf:
        view_btn = f'<a class="btn" href="{pdf}" target="_blank" rel="noopener">View</a>'
        dl_btn   = f'<a class="btn btn-secondary" href="{pdf}" download>Download PDF</a>'
    else:
        view_btn = '<span class="btn" style="opacity:.5;pointer-events:none;">View</span>'
        dl_btn   = '<span class="btn btn-secondary" style="opacity:.5;pointer-events:none;">Download PDF</span>'
    return f"""
    <article class="card">
      <div class="card-head">
        <h3 class="card-title">{title}</h3>
        {badge}
      </div>
      <dl class="meta">
        <div><dt>Universe</dt><dd>{universe}</dd></div>
        <div><dt>Base</dt><dd>{base}</dd></div>
        <div><dt>Updated</dt><dd>{updated}</dd></div>
      </dl>
      <div class="card-actions">
        {view_btn}
        {dl_btn}
      </div>
    </article>
    """.strip()
def marketplace_templatedef marketplace_template(cards_html: str) -> str:
    return f"""<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Marketplace | Allegiance List Marketing</title>
  <meta name="description" content="Browse direct mail lists from ALM and Dominion Strategy Group. Download the PDF for each data card." />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <style>
    :root {{
      --ink:#0f172a; --muted:#475569; --line:#e2e8f0; --bg:#ffffff; --accent:#111827;
      --badge:#0ea5e9; --badge-ink:#fff;
    }}
    *{{box-sizing:border-box}}
    body{{margin:0;font-family:system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial,sans-serif;color:var(--ink);background:var(--bg)}}
    .wrap{{max-width:1100px;margin:0 auto;padding:40px 20px}}
    h1{{margin:0 0 6px;font-size:2rem}}
    p.lead{{margin:0 0 20px;color:var(--muted)}}
    .grid{{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px}}
    .card{{border:1px solid var(--line);border-radius:14px;padding:18px;background:#fff;display:flex;flex-direction:column;gap:12px}}
    .card-head{{display:flex;justify-content:space-between;align-items:flex-start;gap:8px}}
    .card-title{{margin:0;font-size:1.05rem;line-height:1.3}}
    .badge{{display:inline-block;padding:4px 8px;border-radius:999px;font-size:.75rem}}
    .badge-dsg{{background:var(--badge);color:var(--badge-ink)}}
    dl.meta{{display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px;margin:0}}
    dl.meta dt{{font-size:.75rem;color:var(--muted)}}
    dl.meta dd{{margin:0}}
    .card-actions{{display:flex;gap:8px;margin-top:auto}}
    .btn{{display:inline-block;text-decoration:none;background:var(--accent);color:#fff;padding:8px 12px;border-radius:10px}}
    .btn-secondary{{background:#fff;border:1px solid var(--accent);color:var(--accent)}}
    footer{{margin-top:30px;color:var(--muted);font-size:.9rem}}
  </style>
</head>
<body>
  <main class="wrap">
    <header>
      <h1>Marketplace</h1>
      <p class="lead">Direct mail data cards from Allegiance List Marketing and Dominion Strategy Group. Click a card to view details or download the PDF.</p>
    </header>
    <section class="grid">
      {cards_html}
    </section>
    <footer>Cards labeled <strong>A DSG Property</strong> are owned by Dominion Strategy Group.</footer>
  </main>
</body>
</html>
"""

def datacard_page(card, md_html: str) -> str:
    title = html_escape(card.get("title","Untitled"))
    universe = html_escape(card.get("universe","n/a"))
    base = html_escape(card.get("baseRate","call"))
    updated = html_escape(card.get("lastUpdate","n/a"))
    owner = html_escape(card.get("owner",""))
    pdf = html_escape(card.get("pdf",""))
    badge = '<span class="badge badge-dsg">A DSG Property</span>' if is_dsg(card.get("owner","")) else ""
    desc_block = md_html.strip() if md_html.strip() else "<p><em>Details coming soon.</em></p>"
    return f"""<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>{title} | Data Card</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <style>
    :root {{ --ink:#0f172a; --muted:#475569; --line:#e2e8f0; --bg:#ffffff; --accent:#111827; --badge:#0ea5e9; --badge-ink:#fff; }}
    body{{margin:0;font-family:system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial,sans-serif;color:var(--ink);background:var(--bg)}}
    .wrap{{max-width:900px;margin:0 auto;padding:40px 20px}}
    h1{{margin:0 0 10px;font-size:1.8rem}}
    .muted{{color:var(--muted)}}
    .panel{{border:1px solid var(--line);border-radius:14px;padding:18px;background:#fff}}
    .row{{display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px}}
    .row div{{border:1px dashed var(--line);border-radius:10px;padding:12px}}
    .badge{{display:inline-block;padding:4px 8px;border-radius:999px;font-size:.75rem}}
    .badge-dsg{{background:var(--badge);color:var(--badge-ink)}}
    .actions{{margin:16px 0;display:flex;gap:10px}}
    .btn{{display:inline-block;text-decoration:none;background:var(--accent);color:#fff;padding:10px 14px;border-radius:10px}}
    .btn-secondary{{background:#fff;border:1px solid var(--accent);color:var(--accent)}}
    .md :is(h2,h3){{margin-top:1.1em}}
    .md p{{line-height:1.6}}
  </style>
</head>
<body>
  <main class="wrap">
    <h1>{title} {badge}</h1>
    <p class="muted">Owner: {owner}</p>

    <section class="panel">
      <div class="row">
        <div><strong>Universe</strong><br>{universe}</div>
        <div><strong>Base Rate</strong><br>{base}</div>
        <div><strong>Updated</strong><br>{updated}</div>
      </div>
      <div class="actions">
        <a class="btn" href="{pdf}" target="_blank" rel="noopener">Download PDF</a>
        <a class="btn btn-secondary" href="marketplace.html">Back to Marketplace</a>
      </div>
    </section>

    <section class="panel md" style="margin-top:16px;">
      {desc_block}
    </section>
  </main>
</body>
</html>
"""

def md_to_html(md_text: str) -> str:
    # very light markdown -> html (bold, headings, line breaks), no external deps
    if not md_text:
        return ""
    t = md_text
    t = re.sub(r"^### (.*)$", r"<h3>\1</h3>", t, flags=re.MULTILINE)
    t = re.sub(r"^## (.*)$", r"<h2>\1</h2>", t, flags=re.MULTILINE)
    t = re.sub(r"\*\*(.*?)\*\*", r"<strong>\1</strong>", t)
    # paragraphs
    parts = [f"<p>{html.escape(p.strip())}</p>" for p in re.split(r"\n\s*\n", t) if p.strip()]
    return "\n".join(parts)

def main():
    if not os.path.isfile(DATA_JSON):
        raise SystemExit(f"Missing {DATA_JSON}.")
    with open(DATA_JSON, "r", encoding="utf-8") as f:
        cards = json.load(f)
    # sanitize, ensure ids
    clean = []
    for c in cards:
        c = {**c}
        if not c.get("id"):
            c["id"] = slugify(c.get("title","card"))
        clean.append(c)

    # marketplace
    tiles = "\n".join(card_tile(c) for c in clean)
    with open(MARKETPLACE_HTML, "w", encoding="utf-8") as f:
        f.write(marketplace_template(tiles))

    # per-card pages
if False:
    for c in clean:
        cid = c["id"]
        md_text = read_md(c.get("md","").strip())
        page = datacard_page(c, md_to_html(md_text))
        out = os.path.join(REPO, f"datacard-{cid}.html")
        with open(out, "w", encoding="utf-8") as f:
            f.write(page)

    # basic report to stdout
    missing_pdfs = [c for c in clean if not os.path.isfile(os.path.join(REPO, *str(c.get("pdf","")).split("/")))]
    if missing_pdfs:
        print("\nWARNING: The following cards reference missing PDF files:")
        for m in missing_pdfs:
            print(" -", m["id"], "=>", m.get("pdf","(none)"))
    print("\nBuild complete:")
    print(" - marketplace.html written")
    print(" -", len(clean), "data card pages written as datacard-<id>.html")

if __name__ == "__main__":
    main()
