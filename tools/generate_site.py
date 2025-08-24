
import json
import os
from pathlib import Path
from pdfminer.high_level import extract_text
import re

def slugify(s):
    s = s.lower().strip()
    s = re.sub(r"[^a-z0-9\\s-]", "", s)
    s = re.sub(r"[\\s-]+", "-", s)
    return s

def main():
    root = Path(__file__).resolve().parent.parent
    # Use a single, consistent folder for all PDFs
    pdf_dir = root / "assets" / "pdfs" / "legacy"
    template_path = root / "datacard-template.html"
    output_json_path = root / "assets" / "data" / "datacards.json"
    
    if not template_path.exists():
        print(f"ERROR: Template file not found at {template_path}")
        return

    template_content = template_path.read_text()
    all_cards_metadata = []

    print("--- Starting Static Page Generation ---")
    for filename in sorted(os.listdir(pdf_dir)):
        if not filename.lower().endswith(".pdf"):
            continue
        
        pdf_path = pdf_dir / filename
        print(f"Processing: {filename}")
        
        try:
            text = extract_text(str(pdf_path))
            lines = [line.strip() for line in text.split("\\n") if line.strip()]
            title = next((line for line in lines if len(line) > 5 and "Blog" not in line and "Page" not in line), Path(filename).stem)
            
            # Generate static HTML page for this card
            page_content = template_content.replace("__TITLE__", title).replace("__CONTENT__", text)
            page_slug = slugify(title)
            page_filename = f"datacard-{page_slug}.html"
            (root / page_filename).write_text(page_content, encoding="utf-8")

            # Create metadata for the marketplace page
            card_metadata = {
                "id": page_slug,
                "title": title,
                "url": page_filename
            }
            all_cards_metadata.append(card_metadata)

        except Exception as e:
            print(f"  ERROR processing {filename}: {e}")

    # Write the master JSON file for the marketplace
    all_cards_metadata.sort(key=lambda x: x["title"])
    with open(output_json_path, "w", encoding="utf-8") as f:
        json.dump(all_cards_metadata, f, indent=2)
        
    print(f"\\nSuccess! Generated {len(all_cards_metadata)} HTML pages and updated datacards.json.")

if __name__ == "__main__":
    main()

