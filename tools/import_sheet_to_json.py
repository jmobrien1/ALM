#!/usr/bin/env python3
import os, json, re, sys
from pathlib import Path

try:
    import pandas as pd
except ImportError:
    print("Missing pandas/openpyxl. Install with:\n  python3 -m pip install pandas openpyxl")
    sys.exit(1)

REPO = Path(__file__).resolve().parents[1]
SHEET = REPO / "assets/data/source.xlsx"
PDF_DIR = REPO / "assets/pdfs"
OUT_JSON = REPO / "assets/data/datacards.json"

def slugify(s: str) -> str:
    s = (s or "").strip().lower()
    s = re.sub(r"[^a-z0-9\-]+", "-", s)
    s = re.sub(r"-{2,}", "-", s).strip("-")
    return s or "card"

def normalize_header(h: str) -> str:
    h = (h or "").strip().lower()
    # map header variants to canonical
    mapping = {
        "id":"id", "slug":"id",
        "title":"title", "name":"title", "list":"title",
        "owner":"owner", "provider":"owner", "manager":"owner",
        "universe":"universe", "count":"universe", "total universe":"universe",
        "base":"baseRate", "base rate":"baseRate", "baserate":"baseRate",
        "price":"baseRate", "rate":"baseRate",
        "updated":"lastUpdate", "last update":"lastUpdate", "lastupdated":"lastUpdate", "last_update":"lastUpdate",
        "pdf":"pdf", "pdf path":"pdf", "datasheet":"pdf"
    }
    return mapping.get(h, h)

def clean(v, default="n/a"):
    try:
        import pandas as pd
        import numpy as np
        if v is None: return default
        if isinstance(v, float) and math.isnan(v): return default
        if 'pd.' in globals() and pd.isna(v): return default
    except Exception:
        pass
    t = str(v).strip()
    if not t or t.lower()=="nan": return default
    return t

def try_find_pdf_by_title(title: str):
    if not title:
        return None
    base = re.sub(r"[\s]+", " ", title).strip()
    candidates = []
    for p in PDF_DIR.glob("*.pdf"):
        name = p.name
        # naive contains/normalized
        nm = re.sub(r"[\s]+", " ", name.replace(".pdf","")).strip().lower()
        if slugify(base) in slugify(nm) or slugify(nm) in slugify(base):
            candidates.append(p)
    # prefer exact-ish title match; else first candidate
    return str(candidates[0].relative_to(REPO)).replace("\\","/") if candidates else None

def main():
    if not SHEET.exists():
        print(f"ERROR: Sheet not found: {SHEET}")
        sys.exit(2)
    df = pd.read_excel(SHEET)
    # normalize headers
    df.columns = [normalize_header(c) for c in df.columns]
    rows = df.to_dict(orient="records")

    out = []
    for r in rows:
        title = r.get("title") or ""
        owner = r.get("owner") or ""
        _id = r.get("id") or slugify(title)
        universe = clean(r.get("universe"), "n/a")
        base = clean(r.get("baseRate", r.get("base")), "call")
        updated = clean(r.get("lastUpdate", r.get("updated")), "n/a")
        pdf = clean(r.get("pdf"), "")

        # Try to auto-match PDF if empty or missing
        if not pdf or not (REPO / str(pdf)).exists():
            guess = try_find_pdf_by_title(title)
            if guess:
                pdf = guess

        # Force owner values to the two canonical options, if possible
        owner_norm = owner.strip()
        if owner_norm.lower().startswith("dominion strategy group") or owner_norm.upper().startswith("DSG"):
            owner_norm = "Dominion Strategy Group"
        elif owner_norm.lower().startswith("allegiance") or owner_norm.lower().startswith("alm"):
            owner_norm = "Allegiance List Marketing"
        owner = owner_norm or "Allegiance List Marketing"

        out.append({
            "id": str(_id),
            "title": str(title).strip(),
            "owner": owner,
            "universe": universe if str(universe).strip() else "n/a",
            "baseRate": base if str(base).strip() else "call",
            "lastUpdate": updated if str(updated).strip() else "n/a",
            "pdf": str(pdf).strip() if pdf else ""
            # optional "md" can be added later if you want custom copy per card
        })

    OUT_JSON.parent.mkdir(parents=True, exist_ok=True)
    with open(OUT_JSON, "w", encoding="utf-8") as f:
        json.dump(out, f, indent=2, ensure_ascii=False)

    print(f"Wrote {OUT_JSON} with {len(out)} cards.")
    missing_pdfs = [c for c in out if not c.get("pdf") or not (REPO / c["pdf"]).exists()]
    if missing_pdfs:
        print("\nWARNING: Missing or not-found PDFs for these cards:")
        for m in missing_pdfs:
            print(" -", m["id"], "→", m.get("pdf","(none)"))

if __name__ == "__main__":
    main()
