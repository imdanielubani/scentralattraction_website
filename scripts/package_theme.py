"""Package the theme into an installable WordPress ZIP.

Usage:  python scripts/package_theme.py

Creates releases/scentral-attraction-theme.zip with a single top-level folder
`scentral-attraction/` (the layout WordPress expects for Appearance -> Themes ->
Upload Theme). Development-only files (authoring sources, placeholders, OS files)
are excluded. Paths always use forward slashes so the archive installs on Linux hosts.
"""

from __future__ import annotations

import sys
import zipfile
from pathlib import Path

ROOT = Path(__file__).resolve().parent.parent
THEME = ROOT / "wordpress-theme" / "scentral-attraction"
OUT = ROOT / "releases" / "scentral-attraction-theme.zip"
SLUG = "scentral-attraction"

EXCLUDE_DIRS = {"src", "node_modules", ".git", "__pycache__"}
EXCLUDE_FILES = {".gitkeep", ".DS_Store", "Thumbs.db", "desktop.ini"}
EXCLUDE_SUFFIXES = {".map", ".log"}
REQUIRED = ["style.css", "index.php", "functions.php", "screenshot.png", "assets/dist/main.css", "assets/dist/main.js"]


def should_include(path: Path) -> bool:
    rel_parts = path.relative_to(THEME).parts
    if any(part in EXCLUDE_DIRS for part in rel_parts[:-1]):
        return False
    if path.name in EXCLUDE_FILES or path.suffix in EXCLUDE_SUFFIXES:
        return False
    return path.is_file()


def main() -> int:
    missing = [rel for rel in REQUIRED if not (THEME / rel).is_file()]
    if missing:
        print("Cannot package, missing: " + ", ".join(missing), file=sys.stderr)
        print("Run `npm run build` and `npm run images` first.", file=sys.stderr)
        return 1

    OUT.parent.mkdir(parents=True, exist_ok=True)
    files = sorted(p for p in THEME.rglob("*") if should_include(p))
    with zipfile.ZipFile(OUT, "w", compression=zipfile.ZIP_DEFLATED, compresslevel=9) as archive:
        for file in files:
            arcname = f"{SLUG}/" + file.relative_to(THEME).as_posix()
            archive.write(file, arcname)

    size_kb = OUT.stat().st_size / 1024
    print(f"Packaged {len(files)} files -> {OUT.relative_to(ROOT).as_posix()} ({size_kb:,.0f} KB)")
    return 0


if __name__ == "__main__":
    sys.exit(main())
