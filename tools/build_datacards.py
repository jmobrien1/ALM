#!/usr/bin/env python3
import json, os, html, re, datetime

REPO = os.path.dirname(os.path.abspath(__file__))
REPO = os.path.abspath(os.path.join(REPO, ".."))
DATA_JSON = os.path.join(REPO, "assets", "data", "datacards.json")
OUT_HTML  = os.path.join(REPO, "marketplace.html")

def norm(v, default):
    t = "" if v is None else str(v).strip()
    return default if not t or t.lower() == "nan" else t

def is_dsg(owner):
    return (owner or "").strip().lower() == "dominion strategy group"

def tile(card):
    title   = html.escape(norm(card.get("title"), "Untitled"))
    universe= html.escape(norm(card.get("universe"), "n/a"))
    base    = html.escape(norm(card.get("baseRate"), "call"))
    updated = html.escape(norm(card.get("lastUpdate"), "n/a"))
    pdf     = html.escape(norm(card.get("pdf"), ""))
    badge   = '<span class="badge badge-dsg">A DSG Property</span>' if is_dsg(card.get("owner")) else ""
    btn     = f'<span class="btn" style="opacity:.5;pointer-events:none;">View</span>'
    if pdf:
        # Single button: open the PDF file
        btn = f'<a class="btn" href="{pdf}" target="_self">View</a>'
    return f"""
    <article class="card">
      <div class="head"><div class="title">{title}</div>{badge}</div>
      <div class="meta">
        <span class="pill">Universe: {universe}</span>
        <span class="pill">Base: {base}</span>
        <span class="pill">Updated: {updated}</span>
      </div>
      <div class="cta">{btn}</div>
    </article>
    """.strip()

def render(cards):
    sig = f"<!-- BUILD SIG: {datetime.datetime.now().isoformat()} -->"
    tiles = "\n".join(tile(c) for c in cards)
    return f"""<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  {sig}
  <title>Marketplace | Allegiance List Marketing</title>
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
    .head{{display:flex;justify-content:space-between;align-items:flex-start;gap:8px}}
    .title{{font-weight:700}}
    .badge{{display:inline-block;padding:4px 8px;border-radius:999px;font-size:.75rem}}
    .badge-dsg{{background:var(--badge);color:var(--badge-ink)}}
    .meta{{display:flex;gap:8px;flex-wrap:wrap;color:var(--muted)}}
    .pill{{border:1px solid var(--line);border-radius:999px;padding:4px 10px;background:#f8fafc}}
    .cta{{margin-top:auto}}
    .btn{{display:inline-block;text-decoration:none;background:var(--accent);color:#fff;padding:8px 12px;border-radius:10px}}
  </style>
</head>
<body>
  <main class="wrap">
    <header>
      <h1>Marketplace</h1>
      <p class="lead">Click a card to view its PDF. DSG-owned lists are labeled below.</p>
    </header>
    <section class="grid">
      {tiles}
    </section>
    <footer style="margin-top:30px;color:var(--muted);font-size:.9rem">
      Cards labeled <strong>A DSG Property</strong> are owned by Dominion Strategy Group.
    </footer>
  </main>
</body>
</html>
"""

def main():
    if not os.path.isfile(DATA_JSON):
        raise SystemExit(f"Missing {DATA_JSON}")
    with open(DATA_JSON, "r", encoding="utf-8") as f:
        cards = json.load(f)
    # Keep only valid records (no placeholders)
    clean=[]
    for c in cards:
        title = norm(c.get("title"), "")
        if not title: 
            continue
        if title.strip().lower() in {"allegiance list marketing","manager"}:
            # remove the two bogus placeholders
            continue
        clean.append(c)
    with open(OUT_HTML, "w", encoding="utf-8") as f:
        f.write(render(clean))
    print(f"Wrote {OUT_HTML} with {len(clean)} cards.")

if __name__ == "__main__":
    main()
