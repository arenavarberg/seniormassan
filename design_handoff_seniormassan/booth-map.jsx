/* Seniormässan — Monterkarta (SVG)
   Klickbara montrar grupperade A-N enligt PDF-planen.
   Färger:
     2x2 = gul, 2x3 = blå, 3x3 = grön
     N1–N12 = rosa (endast föreningar, fast pris)
   Pris (exkl. moms):
     2x2 = 3820
     2x3 = 5730
     3x3 = 8845
     N1–N12 = 1888 kr exkl. moms (2360 kr inkl. moms) — endast föreningar
*/

const FORENING_PRICE = 1888; // 2360 inkl. moms

const BOOTH_PRICES = {
  "2x2": 3820,
  "2x3": 5730,
  "3x3": 8845,
};

const BOOTH_COLORS = {
  "2x2": "#F2DC6E",   // gul
  "2x3": "#7DC1D9",   // ljusblå
  "3x3": "#A8D38E",   // limegrön
  "forening": "#E8A6C4", // rosa — endast föreningar (N1–N12)
};

function isForeningBooth(id) {
  return typeof id === "string" && id.startsWith("N");
}

/* Layout: alla montrar med id, storlek (2x2|2x3|3x3), och rektangel-koordinater
   i kart-koord-systemet (1000 × 720 viewBox).
   Skala: ~1m = 22 px för läsbarhet.
*/
const BOOTHS = [
  // === N (entréhallen, vänster) — 2x2 montrar i två kolonner ===
  { id: "N1",  size: "2x2", x: 70,  y: 110, w: 38, h: 38 },
  { id: "N2",  size: "2x2", x: 70,  y: 152, w: 38, h: 38 },
  { id: "N3",  size: "2x2", x: 70,  y: 194, w: 38, h: 38 },
  { id: "N4",  size: "2x2", x: 70,  y: 236, w: 38, h: 38 },
  { id: "N5",  size: "2x2", x: 70,  y: 322, w: 38, h: 38 },
  { id: "N6",  size: "2x2", x: 70,  y: 364, w: 38, h: 38 },
  { id: "N7",  size: "2x2", x: 130, y: 110, w: 38, h: 38 },
  { id: "N8",  size: "2x2", x: 130, y: 152, w: 38, h: 38 },
  { id: "N9",  size: "2x2", x: 130, y: 252, w: 38, h: 38 },
  { id: "N10", size: "2x2", x: 130, y: 294, w: 38, h: 38 },
  { id: "N11", size: "2x2", x: 130, y: 336, w: 38, h: 38 },
  { id: "N12", size: "2x2", x: 130, y: 378, w: 38, h: 38 },

  // === A (vid trappan) ===
  { id: "A1", size: "2x2", x: 320, y: 178, w: 38, h: 38 },
  { id: "A2", size: "2x2", x: 320, y: 136, w: 38, h: 38 },

  // === B (Sparbankshallen norr) — 2x3 (bred 60 hög 38) ===
  { id: "B1", size: "2x3", x: 380, y: 100, w: 56, h: 38 },
  { id: "B2", size: "2x3", x: 438, y: 100, w: 56, h: 38 },
  { id: "B3", size: "2x3", x: 496, y: 100, w: 56, h: 38 },
  { id: "B4", size: "2x3", x: 554, y: 100, w: 56, h: 38 },

  // === C (Sparbankshallen östra) — 3x3 montrar (60x60) ===
  { id: "C1", size: "3x3", x: 640, y: 100, w: 56, h: 38 },
  { id: "C2", size: "3x3", x: 698, y: 100, w: 56, h: 38 },
  { id: "C3", size: "3x3", x: 756, y: 100, w: 56, h: 38 },
  { id: "C4", size: "3x3", x: 814, y: 100, w: 56, h: 38 },
  { id: "C5", size: "3x3", x: 872, y: 100, w: 56, h: 38 },
  { id: "C6", size: "3x3", x: 930, y: 142, w: 38, h: 56 },
  { id: "C7", size: "3x3", x: 930, y: 200, w: 38, h: 56 },
  { id: "C8", size: "3x3", x: 930, y: 258, w: 38, h: 56 },

  // === D (mittenraden höger) — 2x3 ===
  { id: "D1", size: "2x3", x: 696, y: 170, w: 56, h: 38 },
  { id: "D2", size: "2x3", x: 754, y: 170, w: 56, h: 38 },
  { id: "D3", size: "2x3", x: 812, y: 170, w: 56, h: 38 },
  { id: "D4", size: "2x3", x: 870, y: 170, w: 56, h: 38 },
  { id: "D5", size: "2x3", x: 696, y: 210, w: 56, h: 38 },
  { id: "D6", size: "2x3", x: 754, y: 210, w: 56, h: 38 },
  { id: "D7", size: "2x3", x: 812, y: 210, w: 56, h: 38 },
  { id: "D8", size: "2x3", x: 870, y: 210, w: 56, h: 38 },

  // === E (mittenraden vänster) — 2x3 ===
  { id: "E1", size: "2x3", x: 380, y: 170, w: 56, h: 38 },
  { id: "E2", size: "2x3", x: 438, y: 170, w: 56, h: 38 },
  { id: "E3", size: "2x3", x: 496, y: 170, w: 56, h: 38 },
  { id: "E4", size: "2x3", x: 554, y: 170, w: 56, h: 38 },
  { id: "E5", size: "2x3", x: 380, y: 210, w: 56, h: 38 },
  { id: "E6", size: "2x3", x: 438, y: 210, w: 56, h: 38 },
  { id: "E7", size: "2x3", x: 496, y: 210, w: 56, h: 38 },
  { id: "E8", size: "2x3", x: 554, y: 210, w: 56, h: 38 },

  // === I (vänstra raden, blåa rutor mitten) — 2x3 vinklade förenklat raka ===
  { id: "I1", size: "2x3", x: 320, y: 282, w: 38, h: 56 },
  { id: "I2", size: "2x3", x: 320, y: 340, w: 38, h: 56 },
  { id: "I3", size: "2x3", x: 320, y: 398, w: 38, h: 56 },
  { id: "I4", size: "2x3", x: 360, y: 340, w: 38, h: 56 },
  { id: "I5", size: "2x3", x: 360, y: 398, w: 38, h: 56 },
  { id: "I6", size: "2x3", x: 360, y: 282, w: 38, h: 56 },

  // === G (mitten höger) — 3x3 ===
  { id: "G1", size: "3x3", x: 580, y: 282, w: 56, h: 38 },
  { id: "G2", size: "3x3", x: 638, y: 282, w: 56, h: 38 },
  { id: "G3", size: "3x3", x: 696, y: 282, w: 56, h: 38 },
  { id: "G4", size: "3x3", x: 754, y: 282, w: 56, h: 38 },
  { id: "G5", size: "3x3", x: 580, y: 322, w: 56, h: 38 },
  { id: "G6", size: "3x3", x: 638, y: 322, w: 56, h: 38 },
  { id: "G7", size: "3x3", x: 696, y: 322, w: 56, h: 38 },
  { id: "G8", size: "3x3", x: 754, y: 322, w: 56, h: 38 },

  // === H (södra mitten) — 3x3 ===
  { id: "H1", size: "3x3", x: 470, y: 380, w: 56, h: 38 },
  { id: "H2", size: "3x3", x: 528, y: 380, w: 56, h: 38 },
  { id: "H3", size: "3x3", x: 586, y: 380, w: 56, h: 38 },
  { id: "H4", size: "3x3", x: 470, y: 420, w: 56, h: 38 },
  { id: "H5", size: "3x3", x: 528, y: 420, w: 56, h: 38 },
  { id: "H6", size: "3x3", x: 586, y: 420, w: 56, h: 38 },

  // === J (södra raden vänster) — 2x3 ===
  { id: "J1", size: "2x3", x: 410, y: 488, w: 38, h: 56 },
  { id: "J2", size: "2x3", x: 410, y: 546, w: 38, h: 56 },
  { id: "J3", size: "2x3", x: 410, y: 604, w: 38, h: 56 },
  { id: "J4", size: "2x3", x: 450, y: 604, w: 38, h: 56 },
  { id: "J5", size: "2x3", x: 450, y: 546, w: 38, h: 56 },
  { id: "J6", size: "2x3", x: 450, y: 488, w: 38, h: 56 },

  // === K (södra raden höger) — 2x3 ===
  { id: "K1", size: "2x3", x: 540, y: 488, w: 38, h: 56 },
  { id: "K2", size: "2x3", x: 540, y: 546, w: 38, h: 56 },
  { id: "K3", size: "2x3", x: 540, y: 604, w: 38, h: 56 },
  { id: "K4", size: "2x3", x: 580, y: 604, w: 38, h: 56 },
  { id: "K5", size: "2x3", x: 580, y: 546, w: 38, h: 56 },
  { id: "K6", size: "2x3", x: 580, y: 488, w: 38, h: 56 },

  // === L (höger om scen) — 2x2 ===
  { id: "L1", size: "2x2", x: 720, y: 488, w: 38, h: 38 },
  { id: "L2", size: "2x2", x: 720, y: 530, w: 38, h: 38 },
  { id: "L3", size: "2x2", x: 720, y: 572, w: 38, h: 38 },
  { id: "L4", size: "2x2", x: 720, y: 614, w: 38, h: 38 },
  { id: "L5", size: "2x2", x: 720, y: 446, w: 38, h: 38 },
  { id: "L6", size: "2x2", x: 720, y: 404, w: 38, h: 38 },
  { id: "L7", size: "2x2", x: 720, y: 656, w: 38, h: 38 },

  // === M (längst söderut) — 2x2 ===
  { id: "M1",  size: "2x2", x: 280, y: 580, w: 38, h: 38 },
  { id: "M2",  size: "2x2", x: 280, y: 622, w: 38, h: 38 },
  { id: "M3",  size: "2x2", x: 280, y: 664, w: 38, h: 38 },
  { id: "M4",  size: "2x2", x: 280, y: 706, w: 38, h: 38 },
  { id: "M5",  size: "2x2", x: 340, y: 706, w: 38, h: 38 },
  { id: "M6",  size: "2x2", x: 382, y: 706, w: 38, h: 38 },
  { id: "M7",  size: "2x2", x: 424, y: 706, w: 38, h: 38 },
  { id: "M8",  size: "2x2", x: 466, y: 706, w: 38, h: 38 },
  { id: "M9",  size: "2x2", x: 508, y: 706, w: 38, h: 38 },
  { id: "M10", size: "2x2", x: 550, y: 706, w: 38, h: 38 },
  { id: "M11", size: "2x2", x: 592, y: 706, w: 38, h: 38 },
  { id: "M12", size: "2x2", x: 634, y: 706, w: 38, h: 38 },
];

/* Stora element runt om: café, scen, trappor, toaletter etc. */
const FIXED_AREAS = [
  { kind: "cafe",   x: 70,   y: 60,  w: 100, h: 40, label: "CAFÉ" },
  { kind: "stage",  x: 235,  y: 100, w: 60,  h: 110, label: "UTSTÄLLAR\u00ADSCEN" },
  { kind: "trappa", x: 310,  y: 60,  w: 110, h: 70, label: "TRAPPA IN & UT MÄSSGOLV" },
  { kind: "cafe2",  x: 750,  y: 320, w: 160, h: 100, label: "CAFÉ" },
  { kind: "stage2", x: 800,  y: 440, w: 100, h: 100, label: "SCEN" },
  { kind: "trappa2",x: 175,  y: 425, w: 90,  h: 50,  label: "TRAPPA ENTRÉ & UTGÅNG" },
  { kind: "garderob", x: 270, y: 320, w: 36,  h: 110, label: "GARDEROB", rotate: -90 },
  { kind: "toilet",   x: 230, y: 240, w: 36,  h: 60,  label: "TOALETTER", rotate: -90 },
  { kind: "toilet2",  x: 230, y: 480, w: 36,  h: 60,  label: "TOALETTER", rotate: -90 },
  { kind: "guests",   x: 70,  y: 450, w: 200, h: 30,  label: "← GÄSTSERVICE  ·  RESTAURANG" },
  { kind: "stolar",   x: 750, y: 580, w: 180, h: 110, label: "Publikplatser" },
];

/* Beräkna pris för en enskild monter (exkl. moms).
   N-montrar är öronmärkta för föreningar och har fast pris. */
function priceFor(boothId) {
  if (isForeningBooth(boothId)) return FORENING_PRICE;
  const b = BOOTHS.find((x) => x.id === boothId);
  if (!b) return 0;
  return BOOTH_PRICES[b.size];
}

function BoothLegend() {
  const items = [
    { color: BOOTH_COLORS["2x2"], label: "2 × 2 m  ·  3 820 kr" },
    { color: BOOTH_COLORS["2x3"], label: "2 × 3 m  ·  5 730 kr" },
    { color: BOOTH_COLORS["3x3"], label: "3 × 3 m  ·  8 845 kr" },
    { color: BOOTH_COLORS["forening"], label: "N1–N12  ·  endast föreningar  ·  2 360 kr inkl. moms" },
  ];
  return (
    <div style={{ display: "flex", gap: 28, flexWrap: "wrap", marginBottom: 20, fontSize: 14 }}>
      {items.map((i) => (
        <div key={i.label} style={{ display: "flex", alignItems: "center", gap: 10 }}>
          <span style={{ width: 22, height: 22, background: i.color, borderRadius: 3, border: "1px solid rgba(0,0,0,0.15)" }} />
          <span style={{ color: "var(--sm-ink-soft)" }}>{i.label}</span>
        </div>
      ))}
      <div style={{ display: "flex", alignItems: "center", gap: 10 }}>
        <span style={{ width: 22, height: 22, background: "var(--sm-accent)", borderRadius: 3 }} />
        <span style={{ color: "var(--sm-ink-soft)" }}>Vald</span>
      </div>
      <div style={{ display: "flex", alignItems: "center", gap: 10 }}>
        <span style={{ width: 22, height: 22, background: "#d6d2cb", borderRadius: 3 }} />
        <span style={{ color: "var(--sm-ink-soft)" }}>Bokad</span>
      </div>
    </div>
  );
}

/* Demo: några montrar är "redan bokade" */
const DEMO_BOOKED = new Set(["B2", "B3", "C2", "E1", "E2", "G1", "H5", "K3", "M5"]);

function BoothMap({ selected, onToggle }) {
  return (
    <div style={{
      background: "#fff",
      border: "1px solid var(--sm-line-soft)",
      borderRadius: "var(--sm-radius-lg)",
      padding: 24,
      boxShadow: "var(--sm-shadow-sm)",
    }}>
      <BoothLegend />
      <div style={{ width: "100%", overflow: "auto" }}>
        <svg viewBox="0 0 1000 780" style={{ width: "100%", minWidth: 720, height: "auto", display: "block" }}>
          {/* Hallens omslutande kontur */}
          <rect x="60" y="50" width="920" height="710" fill="#fbfaf7" stroke="var(--sm-ink)" strokeWidth="2" rx="6" />

          {/* Hallrubriker */}
          <text x="220" y="80" textAnchor="middle" fontFamily="var(--sm-font-display)" fontSize="14" fontWeight="700" fill="var(--sm-ink)">ENTRÉHALLEN</text>
          <text x="600" y="80" textAnchor="middle" fontFamily="var(--sm-font-display)" fontSize="14" fontWeight="700" fill="var(--sm-ink)">SPARBANKSHALLEN</text>
          <text x="220" y="754" textAnchor="middle" fontFamily="var(--sm-font-display)" fontSize="13" fontWeight="700" fill="var(--sm-ink-soft)">HUVUDENTRÉ</text>

          {/* Stora områden — café, scen, trappor etc */}
          {FIXED_AREAS.map((a, i) => {
            const fill = a.kind.startsWith("cafe") ? "#1A1A1A"
              : a.kind.startsWith("stage") ? "#E94E8E"
              : a.kind.startsWith("trappa") ? (a.kind === "trappa2" ? "#E5602A" : "#1A1A1A")
              : a.kind.startsWith("garderob") ? "#B5A0D6"
              : a.kind.startsWith("toilet") ? "#E5602A"
              : "#f0ebe2";
            const textColor = (fill === "#1A1A1A" || fill === "#E5602A") ? "#fff" : "var(--sm-ink)";
            return (
              <g key={i}>
                <rect x={a.x} y={a.y} width={a.w} height={a.h} fill={fill} rx="2" />
                <text
                  x={a.x + a.w / 2}
                  y={a.y + a.h / 2 + 4}
                  textAnchor="middle"
                  fontFamily="var(--sm-font-display)"
                  fontSize={a.kind === "guests" ? "10" : "11"}
                  fontWeight="700"
                  fill={textColor}
                  transform={a.rotate ? `rotate(${a.rotate} ${a.x + a.w / 2} ${a.y + a.h / 2})` : undefined}
                >
                  {a.label}
                </text>
              </g>
            );
          })}

          {/* Publikstolar — punkter i grid */}
          <g opacity="0.6">
            {Array.from({ length: 11 }).flatMap((_, row) =>
              Array.from({ length: 18 }).map((_, col) => (
                <circle key={`${row}-${col}`} cx={760 + col * 9} cy={590 + row * 9} r="1.5" fill="var(--sm-ink-soft)" />
              ))
            )}
          </g>

          {/* Montrar */}
          {BOOTHS.map((b) => {
            const isSelected = selected.has(b.id);
            const isBooked = DEMO_BOOKED.has(b.id);
            const baseColor = isForeningBooth(b.id) ? BOOTH_COLORS["forening"] : BOOTH_COLORS[b.size];
            const fill = isBooked ? "#d6d2cb"
              : isSelected ? "var(--sm-accent)"
              : baseColor;
            const textFill = isSelected ? "#fff"
              : isBooked ? "var(--sm-muted)"
              : "var(--sm-ink)";
            return (
              <g
                key={b.id}
                style={{ cursor: isBooked ? "not-allowed" : "pointer" }}
                onClick={() => !isBooked && onToggle(b.id)}
              >
                <rect
                  x={b.x} y={b.y} width={b.w} height={b.h}
                  fill={fill}
                  stroke={isSelected ? "var(--sm-primary)" : "rgba(0,0,0,0.18)"}
                  strokeWidth={isSelected ? 2 : 0.8}
                  rx="2"
                />
                <text
                  x={b.x + b.w / 2}
                  y={b.y + b.h / 2 + 3}
                  textAnchor="middle"
                  fontFamily="var(--sm-font-body)"
                  fontSize="11"
                  fontWeight="700"
                  fill={textFill}
                  pointerEvents="none"
                >
                  {b.id}
                </text>
              </g>
            );
          })}
        </svg>
      </div>
    </div>
  );
}

function BoothSelector({ selected, onToggle, onClear, member, setMember }) {
  const grouped = React.useMemo(() => {
    const map = {};
    BOOTHS.forEach((b) => {
      const sect = b.id.match(/^[A-Z]+/)[0];
      (map[sect] ||= []).push(b);
    });
    Object.values(map).forEach((arr) => arr.sort((a, b) => parseInt(a.id.replace(/\D/g, ""), 10) - parseInt(b.id.replace(/\D/g, ""), 10)));
    return map;
  }, []);

  const total = [...selected].reduce((s, id) => s + priceFor(id, member), 0);

  const sectionOrder = ["A", "B", "C", "D", "E", "G", "H", "I", "J", "K", "L", "M", "N"];

  return (
    <div style={{ marginTop: 32 }}>
      {/* Förening + filter */}
      <div style={{ display: "flex", justifyContent: "space-between", alignItems: "center", marginBottom: 24, flexWrap: "wrap", gap: 16 }}>
        <label style={{ display: "flex", alignItems: "center", gap: 12, cursor: "pointer", fontSize: 16 }}>
          <input
            type="checkbox"
            checked={member}
            onChange={(e) => setMember(e.target.checked)}
            style={{ width: 22, height: 22, accentColor: "var(--sm-primary)" }}
          />
          <span><strong>Vi är en ideell förening</strong> — föreningspris (1 890 kr) gäller på 2×2-montrar</span>
        </label>
        {selected.size > 0 && (
          <button
            type="button"
            onClick={onClear}
            style={{ background: "transparent", border: "1px solid var(--sm-line)", padding: "8px 16px", borderRadius: 6, cursor: "pointer", fontSize: 14 }}
          >
            Rensa val ({selected.size})
          </button>
        )}
      </div>

      {/* Listor per sektion */}
      <h3 style={{ fontSize: 22, marginBottom: 8 }}>Numret på din monter — välj i listan</h3>
      <p style={{ fontSize: 15, color: "var(--sm-ink-soft)", marginBottom: 24 }}>
        Klicka direkt på kartan ovan eller bocka för i listan. Bokade montrar är gråa.
      </p>

      <div style={{
        display: "grid",
        gridTemplateColumns: "repeat(auto-fill, minmax(180px, 1fr))",
        gap: 24,
        marginBottom: 32,
      }}>
        {sectionOrder.filter((s) => grouped[s]).map((sect) => (
          <div key={sect}>
            <div style={{
              fontFamily: "var(--sm-font-display)", fontWeight: 700, fontSize: 14,
              letterSpacing: "0.1em", textTransform: "uppercase",
              color: "var(--sm-ink-soft)", marginBottom: 10,
            }}>Sektion {sect}</div>
            <div style={{ display: "flex", flexWrap: "wrap", gap: 6 }}>
              {grouped[sect].map((b) => {
                const isSelected = selected.has(b.id);
                const isBooked = DEMO_BOOKED.has(b.id);
                return (
                  <button
                    key={b.id}
                    type="button"
                    disabled={isBooked}
                    onClick={() => onToggle(b.id)}
                    title={`${b.id} · ${b.size.replace("x", "×")} m`}
                    style={{
                      padding: "6px 10px",
                      minWidth: 44,
                      borderRadius: 4,
                      border: isSelected ? "2px solid var(--sm-primary)" : "1px solid rgba(0,0,0,0.15)",
                      background: isBooked ? "#e8e6e1"
                        : isSelected ? "var(--sm-accent)"
                        : BOOTH_COLORS[b.size],
                      color: isBooked ? "var(--sm-muted)"
                        : isSelected ? "#fff"
                        : "var(--sm-ink)",
                      fontWeight: 700, fontSize: 13,
                      fontFamily: "var(--sm-font-body)",
                      cursor: isBooked ? "not-allowed" : "pointer",
                      textDecoration: isBooked ? "line-through" : "none",
                    }}
                  >
                    {b.id}
                  </button>
                );
              })}
            </div>
          </div>
        ))}
      </div>

      {/* Sammanfattning */}
      {selected.size > 0 ? (
        <div style={{
          background: "var(--sm-primary)",
          color: "#fff",
          borderRadius: "var(--sm-radius-lg)",
          padding: "32px 36px",
          display: "grid", gridTemplateColumns: "1fr auto", alignItems: "center", gap: 32,
        }}>
          <div>
            <div style={{ fontSize: 13, letterSpacing: "0.1em", textTransform: "uppercase", opacity: 0.8 }}>Dina valda montrar</div>
            <div style={{ marginTop: 12, display: "flex", flexWrap: "wrap", gap: 8 }}>
              {[...selected].sort().map((id) => {
                const b = BOOTHS.find((x) => x.id === id);
                return (
                  <span key={id} style={{
                    background: "rgba(255,255,255,0.15)",
                    padding: "8px 14px", borderRadius: 6,
                    fontFamily: "var(--sm-font-display)", fontWeight: 700, fontSize: 16,
                  }}>
                    {id} <span style={{ opacity: 0.7, fontWeight: 400, fontSize: 13 }}>· {b.size.replace("x", "×")} m · {priceFor(id, member).toLocaleString("sv-SE")} kr</span>
                  </span>
                );
              })}
            </div>
            <div style={{ marginTop: 14, fontSize: 14, opacity: 0.85 }}>
              {selected.size} {selected.size === 1 ? "monter" : "montrar"} · totalt {[...selected].reduce((s, id) => {
                const b = BOOTHS.find((x) => x.id === id);
                const [w, h] = b.size.split("x").map(Number);
                return s + w * h;
              }, 0)} m² · El, väggar, omnämning i program {member ? "" : "och 5 entrébiljetter "}ingår
            </div>
          </div>
          <div style={{ textAlign: "right" }}>
            <div style={{ fontSize: 13, opacity: 0.8, letterSpacing: "0.1em", textTransform: "uppercase" }}>Totalt</div>
            <div style={{ fontFamily: "var(--sm-font-display)", fontSize: 48, fontWeight: 800, lineHeight: 1, marginTop: 4 }}>
              {total.toLocaleString("sv-SE")} <span style={{ fontSize: 18, fontWeight: 500 }}>kr</span>
            </div>
            <div style={{ fontSize: 12, opacity: 0.75, marginTop: 4 }}>exkl. moms</div>
          </div>
        </div>
      ) : (
        <div style={{
          border: "2px dashed var(--sm-line)",
          borderRadius: "var(--sm-radius-lg)",
          padding: "40px 32px",
          textAlign: "center",
          color: "var(--sm-ink-soft)",
        }}>
          Inga montrar valda än. Klicka på kartan eller listan ovan för att börja.
        </div>
      )}
    </div>
  );
}

/* Toppnivå-komponenten — hanterar state och returnerar ihop kartan + selektorn */
function BoothMapSection({ onRegister }) {
  const [selected, setSelected] = React.useState(new Set());
  const [member, setMember] = React.useState(false);

  const toggle = (id) => {
    setSelected((prev) => {
      const next = new Set(prev);
      if (next.has(id)) next.delete(id);
      else next.add(id);
      return next;
    });
  };
  const clear = () => setSelected(new Set());

  return (
    <>
      <div className="sm-eyebrow">Monter</div>
      <h2 style={{ fontSize: "var(--sm-fs-xl)" }}>Välj monterplats</h2>
      <p style={{ fontSize: "var(--sm-fs-lg)", color: "var(--sm-ink-soft)", marginTop: 16, marginBottom: 8, maxWidth: 760 }}>
        Kolla på monterkartan och välj din monter. Du kan välja flera montrar bredvid varandra för att få en större yta.
      </p>
      <p style={{ fontSize: 16, color: "var(--sm-ink-soft)", marginBottom: 32, maxWidth: 760 }}>
        Numret på din monter väljer du sedan i listan under kartan.
      </p>

      <BoothMap selected={selected} onToggle={toggle} member={member} />
      <BoothSelector
        selected={selected}
        onToggle={toggle}
        onClear={clear}
        member={member}
        setMember={setMember}
      />

      <div style={{ textAlign: "center", marginTop: 40 }}>
        <button
          className="sm-btn sm-btn--accent"
          disabled={selected.size === 0}
          onClick={() => onRegister && onRegister([...selected], member)}
          style={selected.size === 0 ? { opacity: 0.5, cursor: "not-allowed" } : {}}
        >
          Gå vidare till anmälan {selected.size > 0 && `→`}
        </button>
        <div style={{ marginTop: 14, fontSize: 15, color: "var(--sm-muted)" }}>Sista anmälningsdag: 15 augusti 2027</div>
      </div>
    </>
  );
}

Object.assign(window, { BoothMap, BoothMapSection, BOOTHS, BOOTH_PRICES, BOOTH_COLORS });
