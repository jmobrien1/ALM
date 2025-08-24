
import json
import os
from pathlib import Path
from pdfminer.high_level import extract_text

def slugify(s):
    return "".join(c for c in s if c.isalnum() or c in " -_").replace(" ", "-").lower()

def main():
    root = Path(__file__).parent.parent
    pdf_dir = root / "assets" / "pdfs" / "legacy"
    output_json_path = root / "assets" / "data" / "datacards.json"
    
    all_cards = []

    for filename in os.listdir(pdf_dir):
        if not filename.lower().endswith(".pdf"):
            continue
        
        pdf_path = pdf_dir / filename
        print(f"Processing {pdf_path}...")
        
        try:
            text = extract_text(pdf_path)
            title = text.split("\\n")[2].strip() if len(text.split("\\n")) > 2 else Path(filename).stem
            
            card_data = {
                "id": slugify(title),
                "card_id": "".join(filter(str.isdigit, str(pdf_path))), # Heuristic for a numeric ID
                "title": title,
                "pdf": f"assets/pdfs/legacy/{filename}",
                "contentMarkdown": text
            }
            all_cards.append(card_data)
        except Exception as e:
            print(f"  Error processing {filename}: {e}")

    all_cards.sort(key=lambda x: x["title"])
    
    with open(output_json_path, "w") as f:
        json.dump(all_cards, f, indent=2)
        
    print(f"\\nSuccess! Wrote {len(all_cards)} cards to {output_json_path}")

if __name__ == "__main__":
    main()

