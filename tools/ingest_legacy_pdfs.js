
const fs = require("fs");
const path = require("path");
const pdf = require("pdf-parse");

const SRC = "assets/pdfs/legacy";
const MD_DIR = "assets/md";
const OUT = "assets/data/datacards.json";

function slugify(s) { return String(s || "").toLowerCase().replace(/[^a-z0-9]+/g, "-").replace(/(^-|-$)/g, ""); }

async function main() {
  const files = fs.readdirSync(SRC).filter(f => f.toLowerCase().endsWith(".pdf"));
  const allCards = [];

  for (const f of files) {
    const pdfPath = path.join(SRC, f);
    const buf = fs.readFileSync(pdfPath);
    const data = await pdf(buf);
    const text = data.text;

    // Extract title from the first few lines, falling back to filename
    const title = (text.split("\\n")[2] || path.parse(f).name).trim();
    const slug = slugify(title);

    const card = {
      id: slug,
      card_id: String(Date.now()).slice(-6) + Math.floor(Math.random() * 100), // Create a unique ID
      title: title,
      universe_size: 0, // Default values
      base_rate_per_thousand: 0,
      last_updated: "",
      short_description: (text.split("DESCRIPTION")[1] || "").split("\\n")[1]?.trim() || "Legacy data card.",
      pdf: `assets/pdfs/legacy/${f}`,
      markdown: `assets/md/${slug}.md`,
      contentMarkdown: text // Embed the full, raw text content
    };
    allCards.push(card);
  }

  allCards.sort((a, b) => a.title.localeCompare(b.title));
  fs.writeFileSync(OUT, JSON.stringify(allCards, null, 2));
  console.log(`Wrote ${allCards.length} cards to ${OUT}`);
}

main();

