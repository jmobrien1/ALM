
import json
import os
from pathlib import Path
from pdfminer.high_level import extract_text

def slugify(s):
    s = s.lower().strip()
    s = "".join(c for c in s if c.isalnum() or c == " ")
    return "-".join(s.split())

def main():
    root = Path(__file__).resolve().parent.parent
    pdf_dir = root / "assets" / "pdfs" / "legacy"
    output_json_path = root / "assets" / "data" / "datacards.json"

    all_cards = []

    print("--- Starting PDF Processing ---")
    for filename in sorted(os.listdir(pdf_dir)):
        if not filename.lower().endswith(".pdf"):
            continue

        pdf_path = pdf_dir / filename
        print(f"Processing: {filename}")

        try:
            text = extract_text(str(pdf_path))
            lines = [line.strip() for line in text.split("\\n") if line.strip()]

            title = next((line for line in lines if len(line) > 5 and "Blog" not in line and "Retum to search" not in line), Path(filename).stem)

            card_data = {
                "id": slugify(title),
                "card_id": "".join(filter(str.isdigit, title + str(pdf_path)))[:6],
                "title": title,
                "pdf": f"assets/pdfs/legacy/{filename}",
                "contentMarkdown": text
            }
            all_cards.append(card_data)
        except Exception as e:
            print(f"  ERROR processing {filename}: {e}")

    all_cards.sort(key=lambda x: x["title"])

    with open(output_json_path, "w") as f:
        json.dump(all_cards, f, indent=2)

    print(f"\\nSuccess! Wrote {len(all_cards)} cards to {output_json_path}")

if __name__ == "__main__":
    main()

