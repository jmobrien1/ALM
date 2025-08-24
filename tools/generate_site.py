
import json
import os
from pathlib import Path
from pdfminer.high_level import extract_text
import re

def slugify(s):
    s = s.lower().strip()
    s = re.sub(r"[^a-z0-9\\s-]", "", s)
    s = re.sub(r"[\\s-]+", "-", s)
    return s[:75]

def main():
    root = Path(__file__).resolve().parent.parent
    pdf_dir = root / "assets" / "pdfs" / "legacy"
    template_path = root / "datacard-template.html"
    output_json_path = root / "assets" / "data" / "datacards.json"
    
    for old_file in root.glob("datacard-*.html"):
        old_file.unlink()

    if not template_path.exists():
        print(f"ERROR: Template file not found at {template_path}")
        return

    template_content = template_path.read_text(encoding="utf-8")
    all_cards_metadata = []

    print("--- Starting Static Page Generation ---")
    pdf_files = sorted([f for f in os.listdir(pdf_dir) if f.lower().endswith(".pdf")])
    if not pdf_files:
        print(f"No PDF files found in {pdf_dir}. Please add your data card PDFs there.")
        return

    for filename in pdf_files:
        pdf_path = pdf_dir / filename
        print(f"Processing: {filename}")
        
        try:
            text = extract_text(str(pdf_path))
            lines = [line.strip() for line in text.split("\\n") if line.strip()]
            title = next((line for line in lines if len(line) > 5 and "Blog" not in line and len(line) < 100), Path(filename).stem)
            
            page_content = template_content.replace("__TITLE__", title).replace("__CONTENT__", text)
            page_slug = slugify(title)
            page_filename = f"datacard-{page_slug}.html"
            (root / page_filename).write_text(page_content, encoding="utf-8")

            card_metadata = { "id": page_slug, "title": title, "url": page_filename }
            all_cards_metadata.append(card_metadata)

        except Exception as e:
            print(f"  ERROR processing {filename}: {e}")

    all_cards_metadata.sort(key=lambda x: x["title"])
    with open(output_json_path, "w", encoding="utf-8") as f:
        json.dump(all_cards_metadata, f, indent=2)
        
    print(f"\\nSuccess! Generated {len(all_cards_metadata)} HTML pages and updated datacards.json.")

if __name__ == "__main__":
    main()

