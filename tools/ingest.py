#!/usr/bin/env python3
import json, re, hashlib, shutil, sys, os
from datetime import date
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
INCOMING = ROOT / "incoming"
PROCESSED = ROOT / "processed"
MD_DIR = ROOT / "assets" / "md"
DATA_JSON = ROOT / "assets" / "data" / "datacards.json"
TEMP = ROOT / "tmp_ingest"
TEMP.mkdir(exist_ok=True, parents=True)
MD_DIR.mkdir(exist_ok=True, parents=True)
PROCESSED.mkdir(exist_ok=True, parents=True)

# --- Utilities --------------------------------------------------------------

def slugify(s: str) -> str:
    s = s.lower()
    s = re.sub(r"[^a-z0-9]+", "-", s)
    s = re.sub(r"-+", "-", s).strip("-")
    return s[:80] if s else "card"

def stable_id(seed: str) -> int:
    # 6-digit stable id (avoid collisions with your existing ids)
    h = int(hashlib.md5(seed.encode()).hexdigest()[:8], 16)
    return 400000 + (h % 300000)  # 400000..699999

def read_json(path: Path):
    if path.exists():
        try: return json.loads(path.read_text(encoding="utf-8"))
        except Exception: return []
    return []

def write_json(path: Path, obj):
    path.parent.mkdir(parents=True, exist_ok=True)
    path.write_text(json.dumps(obj, indent=2), encoding="utf-8")

def extract_text_pdfminer(pdf_path: Path) -> str:
    # Use pdfminer.six which ships in the Action container
    from pdfminer.high_level import extract_text
    try:
        return extract_text(str(pdf_path)) or ""
    except Exception:
        return ""

def ocr_if_needed(in_pdf: Path) -> Path:
    # If pdfminer returns too little, run OCRmyPDF (scans → text layer)
    txt = extract_text_pdfminer(in_pdf)
    if len(txt.strip()) > 200:  # has text
        return in_pdf
    out_pdf = TEMP / (in_pdf.stem + ".ocr.pdf")
    os.system(f"ocrmypdf --skip-text --force-ocr --quiet '{in_pdf}' '{out_pdf}' || true")
    return out_pdf if out_pdf.exists() else in_pdf

# --- Parse heuristics -------------------------------------------------------

def sniff_title(txt: str, fallback: str) -> str:
    for ln in txt.splitlines():
        ln = ln.strip()
        if len(ln) > 6 and not re.search(r"^(page\s*\d+|copyright|\d{1,2}/\d{1,2}/\d{2,4})$", ln, re.I):
            return ln[:140]
    return fallback

def sniff_universe(txt: str) -> int:
    m = re.search(r"\b([\d,]{3,})\s+(donors?|records?|subscribers?|activists?|members?)\b", txt, re.I)
    return int(m.group(1).replace(",","")) if m else 0

def sniff_rate(txt: str) -> float:
    m = re.search(r"\$\s*([\d,]+(?:\.\d+)?)\s*/?\s*M\b", txt, re.I)
    return float(m.group(1).replace(",","")) if m else 0.0

def sniff_provider(txt: str) -> str:
    m = re.search(r"\b(DSG|Direct\s*Strategy\s*Group|Allegiance|Merkle|Epsilon)\b", txt, re.I)
    return m.group(1).upper() if m else "ALM"

# --- Main -------------------------------------------------------------------

def make_md(title: str, text: str, meta: dict) -> str:
    lines = [f"# {title}", ""]
    lines += ["**Provider:** " + meta.get("provider","ALM"),
              f"**Universe:** {meta.get('universe',0):,}" if meta.get('universe',0) else "**Universe:** n/a",
              f"**Base Rate:** ${meta.get('rate',0):.2f}/M" if meta.get('rate',0) else "**Base Rate:** call",
              f"**Last Updated:** {meta.get('updated','') or date.today().isoformat()}",
              ""]
    lines += ["---", "", "## Overview", "", text.strip()[:4000], ""]
    lines += ["## Selects", "", "- Recency", "- Amount", "- Geography", ""]
    return "\n".join(lines)

def main():
    data = read_json(DATA_JSON)
    by_slug = { (c.get("slug") or slugify(c.get("title",""))): c for c in data }

    pdfs = sorted(INCOMING.glob("*.pdf"))
    if not pdfs:
        print("No PDFs in incoming/. Nothing to do.")
        return

    for pdf in pdfs:
        work = ocr_if_needed(pdf)
        raw = extract_text_pdfminer(work)
        fallback_title = pdf.stem.replace("_"," ").replace("-"," ").strip().title()
        title = sniff_title(raw, fallback_title)
        provider = sniff_provider(raw)
        uni = sniff_universe(raw)
        rate = sniff_rate(raw)
        updated = date.today().isoformat()

        slug = slugify(title or pdf.stem)
        card_id = by_slug.get(slug, {}).get("card_id") or stable_id(slug)

        md_path = MD_DIR / f"{slug}.md"
        md_text = make_md(title, raw, {"provider":provider, "universe":uni, "rate":rate, "updated":updated})
        md_path.write_text(md_text, encoding="utf-8")

        # upsert
        entry = by_slug.get(slug) or {}
        entry.update({
            "card_id": card_id,
            "slug": slug,
            "title": title,
            "universe_size": uni,
            "base_rate_per_thousand": rate,
            "last_updated": updated,
            "short_description": f"{provider} donor file managed by Allegiance List Marketing.",
            "provider": provider,
            "source": entry.get("source","Direct mail"),
            "geography": entry.get("geography","National"),
            "markdown": f"assets/md/{slug}.md",
            "pdf": f"assets/pdfs/{pdf.name}"  # you can upload originals to assets/pdfs/ if desired
        })
        by_slug[slug] = entry

        # move processed
        dest = PROCESSED / pdf.name
        shutil.move(str(pdf), str(dest))
        print(f"[OK] {pdf.name} -> {slug} (id {card_id})")

    # write JSON
    out = list(by_slug.values())
    out.sort(key=lambda c: (c.get("title") or "").lower())
    write_json(DATA_JSON, out)
    print(f"[OK] Wrote {len(out)} cards -> {DATA_JSON}")

if __name__ == "__main__":
    main()
