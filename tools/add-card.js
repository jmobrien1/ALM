
const fs = require("fs");
const path = require("path");
const readline = require("readline");

const DATACARDS_PATH = path.join(__dirname, "../assets/data/datacards.json");

const rl = readline.createInterface({
  input: process.stdin,
  output: process.stdout,
});

const question = (query) => new Promise((resolve) => rl.question(query, resolve));

async function main() {
  console.log("--- Add a New Data Card ---");
  console.log("Please provide the following details. Press Enter to skip a field.");

  const title = await question("Title: ");
  if (!title) {
    console.error("Error: Title is required.");
    rl.close();
    return;
  }

  const universe_size = parseInt(await question("Universe Size (e.g., 50000): ") || "0", 10);
  const base_rate_per_thousand = parseFloat(await question("Base Rate $/M (e.g., 125.00): ") || "0");
  const short_description = await question("Short Description: ");
  const last_updated = new Date().toISOString().split("T")[0]; // YYYY-MM-DD

  const newCard = {
    id: String(Date.now()), // Simple unique ID
    card_id: String(Date.now()).slice(-6), // Simple unique legacy ID
    title,
    universe_size,
    base_rate_per_thousand,
    last_updated,
    short_description,
    pdf: "", // You can add these manually later if needed
    markdown: `assets/md/${title.toLowerCase().replace(/[^a-z0-9]+/g, "-")}.md`,
  };

  console.log("\n--- New Card Data ---");
  console.log(JSON.stringify(newCard, null, 2));
  
  const confirm = await question("\nIs this correct? (y/n): ");

  if (confirm.toLowerCase() !== "y") {
    console.log("Aborted. No changes were made.");
    rl.close();
    return;
  }

  // Read existing data, add new card, and write back to file
  const datacards = JSON.parse(fs.readFileSync(DATACARDS_PATH, "utf-8"));
  datacards.push(newCard);
  datacards.sort((a, b) => a.title.localeCompare(b.title)); // Keep it sorted

  fs.writeFileSync(DATACARDS_PATH, JSON.stringify(datacards, null, 2));

  console.log("\nSuccess! A new data card was added to assets/data/datacards.json");
  console.log("You can now commit and push the changes.");

  rl.close();
}

main();

