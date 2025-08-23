#!/usr/bin/env node
const fs = require('fs');
const path = require('path');
const { pathToFileURL } = require('url');

const cwd = process.cwd();
const targetRel = 'tools/ingest_legacy_pdfs.js';
const target = path.resolve(cwd, targetRel);

function pkgType() {
  try {
    const p = path.join(cwd, 'package.json');
    if (fs.existsSync(p)) {
      const j = JSON.parse(fs.readFileSync(p, 'utf8'));
      return j.type || 'commonjs';
    }
  } catch {}
  return 'commonjs';
}

(async () => {
  try {
    if (!fs.existsSync(target)) {
      console.log('ingest: skip (tools/ingest_legacy_pdfs.js not found)');
      return;
    }
    const type = pkgType();
    let mod;
    if (target.endsWith('.mjs') || type === 'module') {
      mod = await import(pathToFileURL(target).href);
    } else {
      mod = require(target);
    }
    const fn = (mod && (mod.default || mod.main)) || null;
    if (typeof fn === 'function') {
      await fn(process.argv.slice(2));
    }
    // If the script does its work at top-level, just importing/requiring it is enough.
  } catch (e) {
    console.error('INGEST ERROR:', e && e.stack ? e.stack : e);
    process.exit(1);
  }
})();
