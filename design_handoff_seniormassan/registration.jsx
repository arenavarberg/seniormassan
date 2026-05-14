/* Seniormässan — anmälningsformulär
   Flerstegs: (1) Företag (2) Monter (karta) (3) Tillägg (4) Granska
   Sparar anmälningar i localStorage + låter admin exportera CSV.
*/

const BOOTH_PRICE = {
  "2x2": 3820,
  "2x3": 5730,
  "3x3": 8845,
};
const FORENING_PRICE = 1888; // 2360 kr inkl. moms — endast för N1–N12
const REGISTRATION_FEE = 800;

/* Tillval i monter — alla priser exkl. moms.
   Vissa har färgval (variants). "outOfStock" hanteras via localStorage av admin. */
const ADDONS = [
  { id: "matta",        n: "Matta",                       p: 110, cat: "golv",   variants: ["Blå", "Röd", "Grön", "Grå", "Svart", "Orange"], hint: "Plus moms. 30 dagar mellan inflyttning och utflyttning. OBS: Cleaning under mässan tillkommer." },
  { id: "stol",         n: "Stol",                        p: 60,  cat: "mobler", hint: "Stoppad stol" },
  { id: "barstol",      n: "Barstol",                     p: 130, cat: "mobler" },
  { id: "bord180",      n: "Bord 180×80",                  p: 130, cat: "mobler" },
  { id: "bord120",      n: "Bord 120×50",                  p: 110, cat: "mobler", hint: "Bordstorlek vit lojek" },
  { id: "rullbox",      n: "Rullbox",                     p: 657, cat: "mobler", hint: "Rullbox med förvaring" },
  { id: "barbord",      n: "Barbord",                     p: 130, cat: "mobler" },
  { id: "strumpa",      n: "Bordsstrumpa till barbord",   p: 100, cat: "mobler", variants: ["Vit", "Svart"], hint: "OBS! Endast som tillval till barbord." },
  { id: "belysning",    n: "Belysning monter",            p: 110, cat: "el" },
  { id: "el16",         n: "Anslutning 16 amp",           p: 463, cat: "el",     hint: "3-fas, 16 A. För tyngre belastning." },
  { id: "grenkontakt",  n: "Grenkontakt",                 p: 110, cat: "el" },
  { id: "fika_fm",      n: "Förmiddagsfika (exkl. fikabiljett)", p: 95,  cat: "mat", hint: "Bullera fra kaffe" },
  { id: "lunch",        n: "Lunch (exkl. fikabiljett)",   p: 180, cat: "mat", hint: "Arenakökets Moniques härliga lunchbuffet med variation." },
  { id: "fika_em",      n: "Eftermiddagsfika (exkl. fikabiljett)", p: 95, cat: "mat", hint: "Kaffe & te · hembakat" },
];

const CAT_LABELS = { mobler: "Möbler", el: "El & belysning", mat: "Mat & fika", golv: "Mattor & golv" };

/* Lager-status (slut/inte slut) sparas under denna nyckel — admin kan toggla. */
const STOCK_KEY = "sm_addon_stock_v1";
function getStockStatus() {
  try {
    const s = JSON.parse(localStorage.getItem(STOCK_KEY) || "{}");
    // default: alla in stock förutom "barstol" (slutar enligt befintlig bild)
    return { barstol: false, ...s };
  } catch { return { barstol: false }; }
}
function setStockStatus(stock) {
  localStorage.setItem(STOCK_KEY, JSON.stringify(stock));
}

function RegistrationWizard({ onClose, onSubmitted }) {
  const [step, setStep] = React.useState(0);
  const [data, setData] = React.useState({
    company: "", orgnr: "", invoiceAddress: "", invoiceEmail: "",
    contactName: "", contactEmail: "", contactPhone: "",
    website: "",
    description: "",
    booths: [],
    addons: {}, foodTickets: 0,
    specialRequests: "",
    stageSlot: null,
    acceptTerms: false, acceptGdpr: false,
  });

  const update = (patch) => setData((d) => ({ ...d, ...patch }));
  const total = computeTotal(data);

  const steps = ["Företag", "Monter", "Tillägg", "Scen", "Granska"];

  const canNext = {
    0: data.company && data.orgnr && data.contactName && data.contactEmail && data.contactPhone && data.website,
    1: data.booths.length > 0,
    2: true,
    3: true,
    4: data.acceptTerms && data.acceptGdpr,
  }[step];

  const submit = () => {
    const record = {
      id: "SM-" + Date.now().toString(36).toUpperCase(),
      submittedAt: new Date().toISOString(),
      ...data,
      total,
    };
    const existing = JSON.parse(localStorage.getItem("sm-registrations") || "[]");
    existing.push(record);
    localStorage.setItem("sm-registrations", JSON.stringify(existing));
    onSubmitted(record);
  };

  return (
    <div style={{
      position: "fixed", inset: 0, zIndex: 100,
      background: "rgba(20, 30, 30, 0.55)",
      backdropFilter: "blur(6px)",
      overflowY: "auto",
      display: "flex", alignItems: "flex-start", justifyContent: "center",
      padding: "40px 20px",
    }} onClick={onClose}>
      <div onClick={(e) => e.stopPropagation()} style={{
        background: "var(--sm-bg)", width: "100%", maxWidth: 980,
        borderRadius: "var(--sm-radius-lg)",
        boxShadow: "0 40px 80px rgba(0,0,0,0.3)",
        overflow: "hidden",
      }}>
        {/* Header */}
        <div style={{ background: "var(--sm-primary)", color: "#fff", padding: "24px 32px", display: "flex", justifyContent: "space-between", alignItems: "center" }}>
          <div>
            <div style={{ fontSize: 12, letterSpacing: "0.2em", textTransform: "uppercase", opacity: 0.85 }}>Utställaranmälan 2027</div>
            <div style={{ fontFamily: "var(--sm-font-display)", fontSize: 26, fontWeight: 800, marginTop: 4 }}>Seniormässan · Arena Varberg</div>
          </div>
          <button onClick={onClose} style={{
            background: "rgba(255,255,255,0.15)", color: "#fff", border: "none",
            width: 40, height: 40, borderRadius: 20, fontSize: 20,
          }}>×</button>
        </div>

        {/* Stepper */}
        <div style={{ display: "flex", background: "#fff", borderBottom: "1px solid var(--sm-line)" }}>
          {steps.map((s, i) => (
            <button key={s}
              onClick={() => i < step && setStep(i)}
              disabled={i > step}
              style={{
                flex: 1, padding: "18px 12px", border: "none",
                background: "transparent",
                borderBottom: i === step ? "3px solid var(--sm-accent)" : "3px solid transparent",
                color: i <= step ? "var(--sm-ink)" : "var(--sm-muted)",
                fontWeight: i === step ? 700 : 500,
                fontSize: 15,
                cursor: i <= step ? "pointer" : "not-allowed",
              }}>
              <span style={{ display: "inline-block", width: 26, height: 26, borderRadius: 13, background: i <= step ? "var(--sm-primary)" : "var(--sm-line)", color: i <= step ? "#fff" : "var(--sm-muted)", marginRight: 10, fontSize: 13, lineHeight: "26px" }}>{i + 1}</span>
              {s}
            </button>
          ))}
        </div>

        {/* Body */}
        <div style={{ padding: "40px 48px", minHeight: 480 }}>
          {step === 0 && <StepCompany data={data} update={update} />}
          {step === 1 && <StepBooth data={data} update={update} />}
          {step === 2 && <StepAddons data={data} update={update} />}
          {step === 3 && <StepStage data={data} update={update} />}
          {step === 4 && <StepReview data={data} total={total} />}
        </div>

        {/* Footer */}
        <div style={{ padding: "20px 48px", background: "#fff", borderTop: "1px solid var(--sm-line)", display: "flex", alignItems: "center", gap: 16 }}>
          <div style={{ fontSize: 14, color: "var(--sm-muted)" }}>
            Total: <strong style={{ color: "var(--sm-primary)", fontSize: 20 }}>{total.toLocaleString("sv-SE")} kr</strong> exkl. moms
          </div>
          <div style={{ flex: 1 }} />
          {step > 0 && (
            <button className="sm-btn sm-btn--ghost sm-btn--small" onClick={() => setStep(step - 1)}>← Tillbaka</button>
          )}
          {step < 4 && (
            <button className="sm-btn sm-btn--small" disabled={!canNext}
              style={{ opacity: canNext ? 1 : 0.4 }}
              onClick={() => setStep(step + 1)}>Nästa →</button>
          )}
          {step === 4 && (
            <button className="sm-btn sm-btn--accent sm-btn--small" disabled={!canNext}
              style={{ opacity: canNext ? 1 : 0.4 }}
              onClick={submit}>Skicka anmälan ✓</button>
          )}
        </div>
      </div>
    </div>
  );
}

function computeTotal(d) {
  let t = 0;
  for (const id of d.booths || []) {
    if (id.startsWith("N")) {
      t += FORENING_PRICE;
      continue;
    }
    const b = (window.BOOTHS || []).find((x) => x.id === id);
    if (!b) continue;
    t += BOOTH_PRICE[b.size];
  }
  if (t > 0) t += REGISTRATION_FEE;
  for (const [id, val] of Object.entries(d.addons || {})) {
    const a = ADDONS.find((x) => x.id === id);
    if (!a) continue;
    const qty = typeof val === "object" ? (val.qty || 0) : (val || 0);
    if (qty) t += a.p * qty;
  }
  return t;
}

function Field({ label, required, children, hint, span }) {
  return (
    <label style={{ display: "block", gridColumn: span ? `span ${span}` : undefined }}>
      <div style={{ fontSize: 14, fontWeight: 700, marginBottom: 8, color: "var(--sm-ink)" }}>
        {label} {required && <span style={{ color: "var(--sm-accent)" }}>*</span>}
      </div>
      {children}
      {hint && <div style={{ fontSize: 13, color: "var(--sm-muted)", marginTop: 6 }}>{hint}</div>}
    </label>
  );
}

const inputStyle = {
  width: "100%", padding: "14px 16px", fontSize: 17,
  fontFamily: "var(--sm-font-body)",
  border: "1.5px solid var(--sm-line)", borderRadius: 8,
  background: "#fff", color: "var(--sm-ink)",
  transition: "border-color 0.15s",
};

function StepCompany({ data, update }) {
  return (
    <>
      <h2 style={{ fontSize: 28, marginBottom: 8 }}>Företagsuppgifter</h2>
      <p style={{ color: "var(--sm-ink-soft)", marginBottom: 32 }}>Vi använder dessa för fakturan och utställarlistan.</p>
      <div style={{ display: "grid", gridTemplateColumns: "1fr 1fr", gap: 24 }}>
        <Field label="Företagsnamn" required span={2}>
          <input style={inputStyle} value={data.company} onChange={(e) => update({ company: e.target.value })} />
        </Field>
        <Field label="Organisationsnummer" required>
          <input style={inputStyle} placeholder="556123-4567" value={data.orgnr} onChange={(e) => update({ orgnr: e.target.value })} />
        </Field>
        <Field label="Faktura-e-post">
          <input style={inputStyle} type="email" value={data.invoiceEmail} onChange={(e) => update({ invoiceEmail: e.target.value })} />
        </Field>
        <Field label="Fakturaadress" span={2}>
          <input style={inputStyle} value={data.invoiceAddress} onChange={(e) => update({ invoiceAddress: e.target.value })} />
        </Field>
        <Field label="Kontaktperson" required>
          <input style={inputStyle} value={data.contactName} onChange={(e) => update({ contactName: e.target.value })} />
        </Field>
        <Field label="Telefon" required>
          <input style={inputStyle} value={data.contactPhone} onChange={(e) => update({ contactPhone: e.target.value })} />
        </Field>
        <Field label="E-post kontaktperson" required span={2}>
          <input style={inputStyle} type="email" value={data.contactEmail} onChange={(e) => update({ contactEmail: e.target.value })} />
        </Field>
        <Field label="Webbplats" required hint="Visas i utställarlistan på webbplatsen. T.ex. https://dittforetag.se" span={2}>
          <input style={inputStyle} type="url" placeholder="https://" value={data.website} onChange={(e) => update({ website: e.target.value })} />
        </Field>
        <Field label="Kort beskrivning av företaget" hint="Visas i utställarlistan på webbplatsen. Max ca 300 tecken." span={2}>
          <textarea style={{ ...inputStyle, minHeight: 100, resize: "vertical" }} maxLength={320}
            value={data.description} onChange={(e) => update({ description: e.target.value })} />
        </Field>
      </div>
    </>
  );
}

function StepBooth({ data, update }) {
  const selected = new Set(data.booths || []);
  const toggle = (id) => {
    const next = new Set(selected);
    if (next.has(id)) next.delete(id);
    else next.add(id);
    update({ booths: [...next] });
  };
  const clear = () => update({ booths: [] });

  const priceForId = (id) => {
    if (id.startsWith("N")) return FORENING_PRICE; // 1888 exkl moms (2360 inkl)
    const b = (window.BOOTHS || []).find((x) => x.id === id);
    if (!b) return 0;
    return BOOTH_PRICE[b.size];
  };

  const total = (data.booths || []).reduce((s, id) => s + priceForId(id), 0);

  return (
    <>
      <h2 style={{ fontSize: 28, marginBottom: 8 }}>Välj monterplats</h2>
      <p style={{ color: "var(--sm-ink-soft)", marginBottom: 8, fontSize: 16 }}>
        Kolla på monterkartan och välj din monter. Du kan välja flera montrar bredvid varandra för att få en större yta.
      </p>
      <p style={{ color: "var(--sm-ink-soft)", marginBottom: 24, fontSize: 16 }}>
        Klicka direkt på montrarna i kartan för att välja eller avmarkera.
        <br/>
        <strong>Rosa montrar (N1–N12)</strong> är endast för ideella föreningar och kostar 2 360 kr inkl. moms.
      </p>

      <BoothMap selected={selected} onToggle={toggle} />

      {/* Live-summering */}
      {selected.size > 0 ? (
        <div style={{
          marginTop: 24,
          background: "var(--sm-primary)",
          color: "#fff",
          borderRadius: 12,
          padding: "24px 28px",
          display: "grid", gridTemplateColumns: "1fr auto", alignItems: "center", gap: 24,
        }}>
          <div>
            <div style={{ fontSize: 12, letterSpacing: "0.1em", textTransform: "uppercase", opacity: 0.8 }}>Dina valda montrar</div>
            <div style={{ marginTop: 10, display: "flex", flexWrap: "wrap", gap: 6 }}>
              {[...selected].sort().map((id) => {
                const b = (window.BOOTHS || []).find((x) => x.id === id);
                const price = priceForId(id);
                return (
                  <span key={id} style={{
                    background: "rgba(255,255,255,0.15)",
                    padding: "6px 12px", borderRadius: 6,
                    fontFamily: "var(--sm-font-display)", fontWeight: 700, fontSize: 14,
                  }}>
                    {id} <span style={{ opacity: 0.7, fontWeight: 400, fontSize: 12 }}>· {b?.size.replace("x", "×")} m · {price.toLocaleString("sv-SE")} kr</span>
                  </span>
                );
              })}
            </div>
            <button
              type="button"
              onClick={clear}
              style={{ marginTop: 12, background: "transparent", border: "1px solid rgba(255,255,255,0.4)", color: "#fff", padding: "6px 12px", borderRadius: 6, cursor: "pointer", fontSize: 13 }}
            >
              Rensa val
            </button>
          </div>
          <div style={{ textAlign: "right" }}>
            <div style={{ fontSize: 12, opacity: 0.8, letterSpacing: "0.1em", textTransform: "uppercase" }}>Montrar</div>
            <div style={{ fontFamily: "var(--sm-font-display)", fontSize: 36, fontWeight: 800, lineHeight: 1, marginTop: 4 }}>
              {total.toLocaleString("sv-SE")} <span style={{ fontSize: 16, fontWeight: 500 }}>kr</span>
            </div>
            <div style={{ fontSize: 12, opacity: 0.75, marginTop: 4 }}>+ 800 kr reg.avg · exkl. moms</div>
          </div>
        </div>
      ) : (
        <div style={{
          marginTop: 24,
          border: "2px dashed var(--sm-line)",
          borderRadius: 12,
          padding: "32px 24px",
          textAlign: "center",
          color: "var(--sm-ink-soft)",
          fontSize: 16,
        }}>
          Inga montrar valda än. Klicka på kartan ovan för att börja.
        </div>
      )}
    </>
  );
}

function LegendDot({ color, label }) {
  return (
    <div style={{ display: "flex", alignItems: "center", gap: 6 }}>
      <div style={{ width: 16, height: 16, borderRadius: 3, background: color, border: "1px solid rgba(0,0,0,0.08)" }} />
      {label}
    </div>
  );
}

function StepAddons({ data, update }) {
  const stock = getStockStatus();
  const getAddon = (id) => {
    const v = data.addons[id];
    if (!v) return { qty: 0, variant: null };
    if (typeof v === "object") return { qty: v.qty || 0, variant: v.variant || null };
    return { qty: v || 0, variant: null };
  };
  const setAddon = (id, patch) => {
    const cur = getAddon(id);
    const next = { ...cur, ...patch };
    next.qty = Math.max(0, next.qty);
    const newAddons = { ...data.addons };
    if (next.qty === 0) delete newAddons[id];
    else newAddons[id] = next;
    // Cascade: bordsstrumpa kräver barbord — ta bort om barbord försvinner
    if (id === "barbord" && next.qty === 0) delete newAddons["strumpa"];
    update({ addons: newAddons });
  };

  const grouped = {};
  for (const a of ADDONS) {
    if (!grouped[a.cat]) grouped[a.cat] = [];
    grouped[a.cat].push(a);
  }
  const catOrder = ["mobler", "golv", "el", "mat"];

  return (
    <>
      <h2 style={{ fontSize: 28, marginBottom: 8 }}>Tilläggsprodukter</h2>
      <p style={{ color: "var(--sm-ink-soft)", marginBottom: 32 }}>Lägg till det ni behöver i montern. Alla priser exkl. moms.</p>

      {catOrder.filter((c) => grouped[c]).map((cat) => (
        <div key={cat} style={{ marginBottom: 36 }}>
          <div style={{ fontSize: 13, fontWeight: 700, letterSpacing: "0.16em", textTransform: "uppercase", color: "var(--sm-muted)", marginBottom: 14 }}>
            {CAT_LABELS[cat]}
          </div>
          <div style={{ display: "grid", gridTemplateColumns: "repeat(2, 1fr)", gap: 16 }}>
            {grouped[cat].map((a) => {
              const { qty, variant } = getAddon(a.id);
              const isOut = stock[a.id] === false;
              // Lås bordsstrumpa: kräver minst 1 barbord
              const barbordQty = getAddon("barbord").qty;
              const lockedReason = (a.id === "strumpa" && barbordQty === 0)
                ? "Kräver att du först lägger till ett barbord"
                : null;
              return (
                <AddonCard key={a.id} addon={a} qty={qty} variant={variant}
                  isOut={isOut}
                  lockedReason={lockedReason}
                  onQty={(q) => setAddon(a.id, { qty: q })}
                  onVariant={(v) => setAddon(a.id, { variant: v, qty: qty || 1 })}
                />
              );
            })}
          </div>
        </div>
      ))}

      <Field label="Särskilda önskemål" hint="T.ex. placering, grannar, behov av lift, inflyttning." >
        <textarea style={{ ...inputStyle, minHeight: 80, marginTop: 16 }}
          value={data.specialRequests} onChange={(e) => update({ specialRequests: e.target.value })} />
      </Field>
    </>
  );
}

/* SVG-ikoner för de olika tillvalen — enkla låg-detalj illustrationer */
function AddonIcon({ id, variant }) {
  const matColor = {
    "Blå": "#7DC1D9", "Röd": "#D96B6B", "Grön": "#A8D38E",
    "Grå": "#9aa3a6", "Svart": "#2c2c2c", "Orange": "#E89B5C",
  }[variant] || "#7DC1D9";
  const stCol = variant === "Svart" ? "#2c2c2c" : "#fff";
  const stStroke = variant === "Svart" ? "#2c2c2c" : "#bdb6a9";

  const common = { width: "100%", height: 110, viewBox: "0 0 120 80" };
  switch (id) {
    case "matta":
      return <svg {...common}><rect x="15" y="15" width="90" height="50" fill={matColor} rx="2"/><rect x="15" y="15" width="90" height="50" fill="none" stroke="rgba(0,0,0,0.18)" strokeWidth="1.5" rx="2"/><line x1="30" y1="15" x2="30" y2="65" stroke="rgba(255,255,255,0.25)" strokeWidth="1"/><line x1="60" y1="15" x2="60" y2="65" stroke="rgba(255,255,255,0.25)" strokeWidth="1"/><line x1="90" y1="15" x2="90" y2="65" stroke="rgba(255,255,255,0.25)" strokeWidth="1"/></svg>;
    case "stol":
      return <svg {...common}><rect x="45" y="20" width="30" height="35" fill="#7DC1D9" rx="3"/><rect x="45" y="45" width="30" height="6" fill="#5A9AB0"/><line x1="48" y1="55" x2="48" y2="68" stroke="#3a3a3a" strokeWidth="2"/><line x1="72" y1="55" x2="72" y2="68" stroke="#3a3a3a" strokeWidth="2"/></svg>;
    case "barstol":
      return <svg {...common}><rect x="50" y="22" width="20" height="8" fill="#aaa" rx="1"/><line x1="60" y1="30" x2="60" y2="68" stroke="#3a3a3a" strokeWidth="2"/><line x1="50" y1="68" x2="70" y2="68" stroke="#3a3a3a" strokeWidth="2"/></svg>;
    case "bord180":
      return <svg {...common}><rect x="15" y="30" width="90" height="8" fill="#d4a878"/><line x1="22" y1="38" x2="22" y2="66" stroke="#3a3a3a" strokeWidth="2"/><line x1="98" y1="38" x2="98" y2="66" stroke="#3a3a3a" strokeWidth="2"/></svg>;
    case "bord120":
      return <svg {...common}><rect x="30" y="32" width="60" height="7" fill="#d4a878"/><line x1="35" y1="39" x2="35" y2="64" stroke="#3a3a3a" strokeWidth="1.5"/><line x1="85" y1="39" x2="85" y2="64" stroke="#3a3a3a" strokeWidth="1.5"/></svg>;
    case "rullbox":
      return <svg {...common}><rect x="32" y="22" width="56" height="38" fill="#3a3a3a" rx="2"/><circle cx="40" cy="66" r="4" fill="#666"/><circle cx="80" cy="66" r="4" fill="#666"/><line x1="40" y1="30" x2="80" y2="30" stroke="#888" strokeWidth="1"/></svg>;
    case "barbord":
      return (
        <svg {...common}>
          {/* bordsskiva (rund, sett snett ovanifrån — ellips) */}
          <ellipse cx="60" cy="24" rx="22" ry="6" fill="#ffffff" stroke="#bdb6a9" strokeWidth="1.2" />
          <ellipse cx="60" cy="22" rx="22" ry="5" fill="#f4f1ea" stroke="none" />
          {/* X-ben — fyra stråk som korsas i mitten */}
          <line x1="45" y1="30" x2="75" y2="68" stroke="#9aa3a6" strokeWidth="2.2" strokeLinecap="round" />
          <line x1="75" y1="30" x2="45" y2="68" stroke="#9aa3a6" strokeWidth="2.2" strokeLinecap="round" />
          <line x1="50" y1="30" x2="70" y2="68" stroke="#bdc3c5" strokeWidth="2.2" strokeLinecap="round" />
          <line x1="70" y1="30" x2="50" y2="68" stroke="#bdc3c5" strokeWidth="2.2" strokeLinecap="round" />
          {/* tvärslå */}
          <line x1="50" y1="58" x2="70" y2="58" stroke="#9aa3a6" strokeWidth="1.6" strokeLinecap="round" />
          {/* skugga */}
          <ellipse cx="60" cy="70" rx="18" ry="2" fill="rgba(0,0,0,0.08)" />
        </svg>
      );
    case "strumpa":
      return (
        <svg {...common}>
          {/* bordsskiva (rund, sett snett ovanifrån) */}
          <ellipse cx="60" cy="20" rx="22" ry="5" fill={stCol} stroke={stStroke} strokeWidth="1" />
          {/* timglasformad strumpa — bred topp, smal midja vid 42, breddar igen och slutar med tre synliga "ben-flikar" */}
          <path
            d="M 38 20
               C 36 30, 44 38, 50 42
               C 50 50, 47 58, 43 65
               L 49 65
               C 51 60, 54 56, 58 56
               L 60 67
               L 62 56
               C 66 56, 69 60, 71 65
               L 77 65
               C 73 58, 70 50, 70 42
               C 76 38, 84 30, 82 20
               Z"
            fill={stCol}
            stroke={stStroke}
            strokeWidth="1"
            strokeLinejoin="round"
          />
          {/* lätt skugga i sidorna för 3D */}
          <path d="M 39 22 C 38 32, 44 38, 50 42" fill="none" stroke={variant === "Svart" ? "#000" : "#d8d2c2"} strokeWidth="0.8" opacity="0.5" />
          <path d="M 81 22 C 82 32, 76 38, 70 42" fill="none" stroke={variant === "Svart" ? "#000" : "#d8d2c2"} strokeWidth="0.8" opacity="0.5" />
          {/* mjuk skugga under */}
          <ellipse cx="60" cy="69" rx="17" ry="1.5" fill="rgba(0,0,0,0.12)" />
        </svg>
      );
    case "belysning":
      // Spotlampa på lång stav: silvrig huvud (rund), lång stav uppt på sidan, kabel
      return (
        <svg {...common}>
          {/* stav (lutad från nedre vänster mot övre höger) */}
          <line x1="24" y1="66" x2="86" y2="22" stroke="#c0c4c8" strokeWidth="3" strokeLinecap="round" />
          <line x1="24" y1="66" x2="86" y2="22" stroke="#9aa0a6" strokeWidth="1" strokeLinecap="round" />
          {/* spotlampans hus i nedre vänster */}
          <ellipse cx="22" cy="68" rx="7" ry="5" fill="#bdc3c7" stroke="#7f8c8d" strokeWidth="0.8" />
          <circle cx="22" cy="68" r="4" fill="#f7e89c" stroke="#d8c870" strokeWidth="0.8" />
          <circle cx="21" cy="67" r="1.5" fill="#fff8d6" />
          {/* stickpropp i övre högra delen */}
          <rect x="86" y="18" width="10" height="6" fill="#222" rx="1" />
          <line x1="96" y1="21" x2="100" y2="21" stroke="#222" strokeWidth="1.2" />
          {/* slingrande kabel */}
          <path d="M 88 24 C 78 30, 70 22, 66 30 C 62 38, 76 36, 70 44" fill="none" stroke="#222" strokeWidth="1.2" strokeLinecap="round" />
        </svg>
      );
    case "el16":
      // 3-fas kabel: svart upprullad slinga + röd kontakt höger med 5 små metall-pinnar
      return (
        <svg {...common}>
          {/* upprullad kabel — ovala slingor som överlappar */}
          <ellipse cx="50" cy="40" rx="22" ry="14" fill="none" stroke="#1a1a1a" strokeWidth="3" />
          <ellipse cx="50" cy="40" rx="18" ry="11" fill="none" stroke="#222" strokeWidth="2.5" />
          <ellipse cx="50" cy="40" rx="14" ry="8" fill="none" stroke="#1a1a1a" strokeWidth="2" />
          {/* röd hon-kontakt vänster (vinklad) */}
          <rect x="14" y="28" width="14" height="10" fill="#cc4022" stroke="#7a2412" strokeWidth="0.8" rx="1" />
          <rect x="12" y="30" width="3" height="6" fill="#1a1a1a" rx="0.5" />
          {/* sladd från hon-kontakt till slinga */}
          <path d="M 28 33 Q 32 35 35 38" fill="none" stroke="#1a1a1a" strokeWidth="2.5" strokeLinecap="round" />
          {/* röd han-kontakt höger */}
          <rect x="82" y="42" width="18" height="12" fill="#cc4022" stroke="#7a2412" strokeWidth="0.8" rx="1.5" />
          {/* metall-tipp på han-kontakten */}
          <rect x="100" y="44" width="5" height="8" fill="#dde0e3" stroke="#9aa0a6" strokeWidth="0.8" rx="0.6" />
          {/* 5 små pinnar på han-kontaktens framsida */}
          <circle cx="86" cy="45" r="1" fill="#1a1a1a" />
          <circle cx="90" cy="45" r="1" fill="#1a1a1a" />
          <circle cx="94" cy="45" r="1" fill="#1a1a1a" />
          <circle cx="88" cy="50" r="1" fill="#1a1a1a" />
          <circle cx="92" cy="50" r="1" fill="#1a1a1a" />
          {/* sladd från slinga till han-kontakt */}
          <path d="M 70 45 Q 76 47 82 48" fill="none" stroke="#1a1a1a" strokeWidth="2.5" strokeLinecap="round" />
        </svg>
      );
    case "grenkontakt":
      // Vit lång grenkontakt (sett snett) med 3 jordade uttag
      return (
        <svg {...common}>
          {/* Hus */}
          <rect x="18" y="30" width="84" height="22" rx="4" fill="#fafafa" stroke="#aaa" strokeWidth="1" />
          <rect x="18" y="30" width="84" height="4" fill="#e8e8e8" rx="4" />
          {/* 3 uttag (cirkuljära med två platta hål + jord-kontakt) */}
          {[35, 60, 85].map((x, i) => (
            <g key={i}>
              <circle cx={x} cy="42" r="7" fill="#ffffff" stroke="#bbb" strokeWidth="0.8" />
              <rect x={x - 4} y="40" width="3" height="1.5" fill="#222" />
              <rect x={x + 1} y="40" width="3" height="1.5" fill="#222" />
              <rect x={x - 1} y="45" width="2" height="1.5" fill="#222" />
            </g>
          ))}
          {/* sladd ut höger */}
          <path d="M 102 41 Q 110 41 113 47" fill="none" stroke="#1a1a1a" strokeWidth="2" strokeLinecap="round" />
          {/* skugga */}
          <ellipse cx="60" cy="56" rx="36" ry="2" fill="rgba(0,0,0,0.08)" />
        </svg>
      );
    case "fika_fm":
    case "fika_em":
      return <svg {...common}><ellipse cx="60" cy="55" rx="22" ry="5" fill="#bdb6a9"/><path d="M40 35 L 80 35 L 75 55 L 45 55 Z" fill="#fff" stroke="#bdb6a9" strokeWidth="1.5"/><path d="M80 38 Q 90 38 90 45 Q 90 52 80 52" fill="none" stroke="#bdb6a9" strokeWidth="1.5"/><path d="M48 30 Q 50 25 52 30" fill="none" stroke="#9a9a9a" strokeWidth="1"/><path d="M58 28 Q 60 23 62 28" fill="none" stroke="#9a9a9a" strokeWidth="1"/><path d="M68 30 Q 70 25 72 30" fill="none" stroke="#9a9a9a" strokeWidth="1"/></svg>;
    case "lunch":
      return <svg {...common}><circle cx="60" cy="45" r="22" fill="#fff" stroke="#bdb6a9" strokeWidth="1.5"/><circle cx="60" cy="45" r="15" fill="#A8D38E" opacity="0.5"/><circle cx="55" cy="42" r="4" fill="#D96B6B"/><circle cx="63" cy="48" r="3" fill="#E89B5C"/></svg>;
    default:
      return <svg {...common}><rect x="30" y="25" width="60" height="35" fill="#e8e3d8" rx="3"/></svg>;
  }
}

function AddonCard({ addon, qty, variant, isOut, lockedReason, onQty, onVariant }) {
  const a = addon;
  const selected = qty > 0;
  const blocked = isOut || !!lockedReason;
  return (
    <div style={{
      background: selected ? "var(--sm-primary-soft)" : "#fff",
      border: `1.5px solid ${selected ? "var(--sm-primary)" : "var(--sm-line)"}`,
      borderRadius: 12,
      padding: 0,
      overflow: "hidden",
      display: "flex", flexDirection: "column",
      opacity: blocked ? 0.6 : 1,
      position: "relative",
    }}>
      {isOut && (
        <div style={{
          position: "absolute", top: 12, left: 12, zIndex: 2,
          background: "var(--sm-ink)", color: "#fff",
          padding: "4px 10px", borderRadius: 4, fontSize: 11, fontWeight: 700, letterSpacing: "0.08em",
        }}>SLUTSÅLD</div>
      )}
      {!isOut && lockedReason && (
        <div style={{
          position: "absolute", top: 12, left: 12, zIndex: 2,
          background: "#fff", color: "var(--sm-ink)",
          border: "1px solid var(--sm-line)",
          padding: "4px 10px", borderRadius: 4, fontSize: 11, fontWeight: 700, letterSpacing: "0.04em",
          display: "flex", alignItems: "center", gap: 4,
        }}>🔒 LÅST</div>
      )}
      <div style={{ background: "#f4f1ea", padding: "16px 12px", display: "flex", alignItems: "center", justifyContent: "center", height: 120 }}>
        <AddonIcon id={a.id} variant={variant} />
      </div>
      <div style={{ padding: "14px 16px 16px", flex: 1, display: "flex", flexDirection: "column", gap: 8 }}>
        <div>
          <div style={{ fontSize: 16, fontWeight: 700, color: "var(--sm-ink)" }}>{a.n}</div>
          {(lockedReason || a.hint) && (
            <div style={{ fontSize: 12, color: lockedReason ? "#a04545" : "var(--sm-muted)", marginTop: 2, lineHeight: 1.4, fontWeight: lockedReason ? 600 : 400 }}>
              {lockedReason || a.hint}
            </div>
          )}
          <div style={{ fontSize: 14, fontWeight: 700, color: "var(--sm-primary)", marginTop: 6 }}>
            {a.p.toLocaleString("sv-SE")} kr / st
          </div>
        </div>

        {a.variants && (
          <div style={{ display: "flex", gap: 6, alignItems: "center", flexWrap: "wrap" }}>
            <span style={{ fontSize: 12, color: "var(--sm-muted)", marginRight: 4 }}>Färg:</span>
            {a.variants.map((v) => {
              const isSel = variant === v;
              const swatch = {
                "Blå": "#7DC1D9", "Röd": "#D96B6B", "Grön": "#A8D38E",
                "Grå": "#9aa3a6", "Svart": "#2c2c2c", "Orange": "#E89B5C",
                "Vit": "#fff",
              }[v] || "#ccc";
              return (
                <button key={v}
                  type="button"
                  onClick={() => !blocked && onVariant(v)}
                  disabled={blocked}
                  title={v}
                  style={{
                    width: 22, height: 22, borderRadius: "50%",
                    background: swatch,
                    border: isSel ? "2px solid var(--sm-primary)" : "1px solid rgba(0,0,0,0.2)",
                    cursor: blocked ? "not-allowed" : "pointer",
                    boxShadow: isSel ? "0 0 0 2px #fff inset" : "none",
                    padding: 0,
                  }}
                />
              );
            })}
          </div>
        )}

        <div style={{ display: "flex", alignItems: "center", justifyContent: "space-between", marginTop: "auto", paddingTop: 8 }}>
          <div style={{ display: "flex", alignItems: "center", gap: 6 }}>
            <button onClick={() => onQty(qty - 1)} disabled={qty === 0 || blocked} style={qtyBtn}>−</button>
            <div style={{ width: 32, textAlign: "center", fontSize: 17, fontWeight: 700 }}>{qty}</div>
            <button onClick={() => onQty(qty + 1)} disabled={blocked} style={qtyBtn}>+</button>
          </div>
          <div style={{ fontSize: 14, fontWeight: 700, color: selected ? "var(--sm-primary)" : "var(--sm-muted)" }}>
            {qty > 0 ? (qty * a.p).toLocaleString("sv-SE") + " kr" : "—"}
          </div>
        </div>
      </div>
    </div>
  );
}

const qtyBtn = {
  width: 36, height: 36, borderRadius: 18,
  border: "1.5px solid var(--sm-line)",
  background: "#fff", fontSize: 18, fontWeight: 700,
  color: "var(--sm-primary)",
};

function StepStage({ data, update }) {
  const slots = ["11:00", "11:30", "12:00", "12:30", "13:00", "13:30", "14:00", "14:30", "15:00", "15:30", "16:00"];
  // simulate occupied slots from other registrations (read from localStorage)
  const taken = React.useMemo(() => {
    const recs = JSON.parse(localStorage.getItem("sm-registrations") || "[]");
    return new Set(recs.map((r) => r.stageSlot).filter(Boolean));
  }, []);
  const selected = data.stageSlot;
  const set = (slot) => update({ stageSlot: selected === slot ? null : slot });

  return (
    <>
      <h2 style={{ fontSize: 28, marginBottom: 8 }}>Utställarscen — boka tid (frivilligt)</h2>
      <p style={{ color: "var(--sm-ink-soft)", marginBottom: 24, maxWidth: 720, lineHeight: 1.55 }}>
        Ta chansen och nå ut med ditt budskap i 15 min på utställarscenen. Skärm för presentation och ljud finns på plats. Utställarscenens program syns i vår marknadsföring.
        <br /><br />
        <strong>Kostnadsfri bindande anmälan</strong> — vid avbokning kan kostnader tillkomma.
      </p>

      <div style={{ background: "#fff", border: "1px solid var(--sm-line)", borderRadius: 10, padding: 24 }}>
        <div style={{ fontSize: 13, fontWeight: 700, letterSpacing: "0.16em", textTransform: "uppercase", color: "var(--sm-muted)", marginBottom: 16 }}>
          Lediga tider · Onsdag 24 februari 2027
        </div>
        <div style={{ display: "grid", gridTemplateColumns: "repeat(4, 1fr)", gap: 12 }}>
          {slots.map((slot) => {
            const isTaken = taken.has(slot);
            const isSelected = selected === slot;
            return (
              <button
                key={slot}
                disabled={isTaken}
                onClick={() => !isTaken && set(slot)}
                style={{
                  padding: "16px 8px",
                  borderRadius: 8,
                  fontSize: 18,
                  fontWeight: 700,
                  cursor: isTaken ? "not-allowed" : "pointer",
                  border: isSelected ? "2px solid var(--sm-accent)" : "1px solid var(--sm-line)",
                  background: isTaken ? "#f3f3f3" : isSelected ? "var(--sm-accent)" : "#fff",
                  color: isTaken ? "var(--sm-muted)" : isSelected ? "#fff" : "var(--sm-ink)",
                  textDecoration: isTaken ? "line-through" : "none",
                  opacity: isTaken ? 0.6 : 1,
                  transition: "all 0.15s",
                }}
              >
                {slot}
                {isTaken && (
                  <div style={{ fontSize: 11, fontWeight: 500, marginTop: 4, letterSpacing: "0.04em" }}>UPPTAGEN</div>
                )}
              </button>
            );
          })}
        </div>
        {selected && (
          <div style={{ marginTop: 20, padding: 14, background: "var(--sm-bg)", borderRadius: 8, fontSize: 15, display: "flex", justifyContent: "space-between", alignItems: "center" }}>
            <span>Vald tid: <strong>{selected}</strong> · 15 min</span>
            <button onClick={() => set(selected)} style={{ background: "transparent", border: "none", color: "var(--sm-ink-soft)", cursor: "pointer", fontSize: 14, textDecoration: "underline" }}>
              Ta bort
            </button>
          </div>
        )}
        {!selected && (
          <div style={{ marginTop: 20, fontSize: 14, color: "var(--sm-muted)" }}>
            Ingen tid vald — du kan hoppa över detta steg om du inte vill boka scen.
          </div>
        )}
      </div>
    </>
  );
}

function StepReview({ data, total }) {
  const activeAddons = Object.entries(data.addons || {})
    .map(([id, v]) => {
      const qty = typeof v === "object" ? (v.qty || 0) : (v || 0);
      const variant = typeof v === "object" ? v.variant : null;
      return [id, qty, variant];
    })
    .filter(([_, q]) => q > 0);
  const boothRows = (data.booths || []).map((id) => {
    const b = (window.BOOTHS || []).find((x) => x.id === id);
    const price = id.startsWith("N") ? FORENING_PRICE : (b ? BOOTH_PRICE[b.size] : 0);
    const label = id.startsWith("N")
      ? `${id} (förenings-monter)`
      : `${id} (${b?.size.replace("x", "×")} m)`;
    return [label, price.toLocaleString("sv-SE") + " kr"];
  });
  return (
    <>
      <h2 style={{ fontSize: 28, marginBottom: 8 }}>Granska och skicka</h2>
      <p style={{ color: "var(--sm-ink-soft)", marginBottom: 24 }}>Du får en bekräftelse på e-post direkt.</p>

      <div style={{ display: "grid", gridTemplateColumns: "1fr 1fr", gap: 24 }}>
        <ReviewBlock title="Företag" rows={[
          ["Namn", data.company],
          ["Org.nr", data.orgnr],
          ["Kontakt", data.contactName],
          ["E-post", data.contactEmail],
          ["Telefon", data.contactPhone],
          ["Webbplats", data.website],
        ]} />
        <ReviewBlock title={`Montrar (${(data.booths || []).length})`} rows={[
          ...boothRows,
          ["Registreringsavgift", REGISTRATION_FEE.toLocaleString("sv-SE") + " kr"],
        ]} />
      </div>

      {activeAddons.length > 0 && (
        <div style={{ marginTop: 24, padding: 20, background: "#fff", border: "1px solid var(--sm-line)", borderRadius: 10 }}>
          <div style={{ fontSize: 13, fontWeight: 700, letterSpacing: "0.16em", textTransform: "uppercase", color: "var(--sm-muted)", marginBottom: 12 }}>Tillägg</div>
          {activeAddons.map(([id, q, variant]) => {
            const a = ADDONS.find((x) => x.id === id);
            return (
              <div key={id} style={{ display: "flex", justifyContent: "space-between", fontSize: 17, padding: "6px 0" }}>
                <span>{a.n}{variant ? ` (${variant})` : ""} × {q}</span>
                <span style={{ fontWeight: 700 }}>{(a.p * q).toLocaleString("sv-SE")} kr</span>
              </div>
            );
          })}
        </div>
      )}

      {data.stageSlot && (
        <div style={{ marginTop: 24, padding: 20, background: "#fff", border: "1px solid var(--sm-line)", borderRadius: 10 }}>
          <div style={{ fontSize: 13, fontWeight: 700, letterSpacing: "0.16em", textTransform: "uppercase", color: "var(--sm-muted)", marginBottom: 8 }}>Utställarscen</div>
          <div style={{ fontSize: 17 }}>
            <strong>{data.stageSlot}</strong> — 15 min · Onsdag 24 februari 2027
          </div>
          <div style={{ fontSize: 13, color: "var(--sm-ink-soft)", marginTop: 4 }}>Kostnadsfritt · bindande anmälan</div>
        </div>
      )}

      <div style={{ marginTop: 24, padding: "20px 24px", background: "var(--sm-primary)", color: "#fff", borderRadius: 10, display: "flex", justifyContent: "space-between", alignItems: "center" }}>
        <span style={{ fontSize: 18 }}>Totalsumma exkl. moms</span>
        <span style={{ fontFamily: "var(--sm-font-display)", fontSize: 32, fontWeight: 800 }}>{total.toLocaleString("sv-SE")} kr</span>
      </div>

      <div style={{ marginTop: 28, display: "flex", flexDirection: "column", gap: 12 }}>
        <Checkbox checked={data.acceptTerms} onChange={(v) => window.dispatchEvent(new CustomEvent("__smterms", { detail: v }))}
          label={<>Jag godkänner <a href="#" style={{ color: "var(--sm-primary)" }}>utställarvillkoren</a> och faktureringsvillkoren (30 dagar netto).</>}
          _onChange={(v) => (data.acceptTerms = v)} />
        <RawCheckbox label="Jag godkänner utställarvillkoren och faktureringsvillkoren (30 dagar netto)." data={data} field="acceptTerms" />
        <RawCheckbox label="Jag samtycker till att uppgifterna hanteras enligt GDPR och visas i utställarlistan på webben." data={data} field="acceptGdpr" />
      </div>
    </>
  );
}

function Checkbox() { return null; /* ersatt av RawCheckbox */ }

function RawCheckbox({ label, data, field }) {
  const [, force] = React.useReducer((x) => x + 1, 0);
  return (
    <label style={{ display: "flex", gap: 12, cursor: "pointer", alignItems: "flex-start", padding: "8px 0" }}>
      <input type="checkbox" checked={!!data[field]} onChange={(e) => { data[field] = e.target.checked; force(); }} style={{ width: 22, height: 22, marginTop: 2, accentColor: "var(--sm-primary)" }} />
      <span style={{ fontSize: 16 }}>{label}</span>
    </label>
  );
}

function ReviewBlock({ title, rows }) {
  return (
    <div style={{ background: "#fff", border: "1px solid var(--sm-line)", borderRadius: 10, padding: 20 }}>
      <div style={{ fontSize: 13, fontWeight: 700, letterSpacing: "0.16em", textTransform: "uppercase", color: "var(--sm-muted)", marginBottom: 12 }}>{title}</div>
      {rows.map(([k, v]) => (
        <div key={k} style={{ display: "flex", justifyContent: "space-between", padding: "6px 0", fontSize: 16, borderBottom: "1px solid var(--sm-line)" }}>
          <span style={{ color: "var(--sm-ink-soft)" }}>{k}</span>
          <span style={{ fontWeight: 600 }}>{v || "—"}</span>
        </div>
      ))}
    </div>
  );
}

function ConfirmationModal({ record, onClose, onAdmin }) {
  return (
    <div style={{
      position: "fixed", inset: 0, zIndex: 101,
      background: "rgba(20, 30, 30, 0.55)",
      backdropFilter: "blur(6px)",
      display: "flex", alignItems: "center", justifyContent: "center", padding: 20,
    }} onClick={onClose}>
      <div onClick={(e) => e.stopPropagation()} style={{
        background: "var(--sm-bg)", padding: 48, borderRadius: "var(--sm-radius-lg)",
        maxWidth: 520, textAlign: "center",
      }}>
        <div style={{ width: 72, height: 72, borderRadius: 36, background: "var(--sm-success)", color: "#fff", margin: "0 auto 24px", display: "flex", alignItems: "center", justifyContent: "center", fontSize: 36, fontWeight: 700 }}>✓</div>
        <h2 style={{ fontSize: 32 }}>Tack för din anmälan!</h2>
        <p style={{ fontSize: 18, color: "var(--sm-ink-soft)", marginTop: 12 }}>
          Vi har registrerat <strong>{record.company}</strong> i monter <strong>{record.booth}</strong>.
          En bekräftelse går nu till <strong>{record.contactEmail}</strong>.
        </p>
        <p style={{ fontSize: 14, color: "var(--sm-muted)", marginTop: 16 }}>Referens: {record.id}</p>
        <div style={{ display: "flex", gap: 12, justifyContent: "center", marginTop: 32 }}>
          <button className="sm-btn sm-btn--small" onClick={onClose}>Stäng</button>
          <button className="sm-btn sm-btn--ghost sm-btn--small" onClick={onAdmin}>Admin-vy (demo) →</button>
        </div>
      </div>
    </div>
  );
}

function AdminPanel({ onClose }) {
  const [records, setRecords] = React.useState(() =>
    JSON.parse(localStorage.getItem("sm-registrations") || "[]")
  );
  const [tab, setTab] = React.useState("anm");
  const [stock, setStockLocal] = React.useState(() => getStockStatus());
  const toggleStock = (id) => {
    const cur = stock[id] !== false;
    const next = { ...stock, [id]: !cur };
    setStockLocal(next);
    setStockStatus(next);
  };

  const exportCsv = () => {
    const headers = ["Ref", "Tidpunkt", "Företag", "Org.nr", "Kontakt", "E-post", "Telefon", "Paket", "Monter", "Totalsumma"];
    const rows = records.map((r) => [
      r.id, r.submittedAt, r.company, r.orgnr, r.contactName, r.contactEmail, r.contactPhone,
      (r.booths || []).join(" + "), r.total,
    ]);
    const csv = [headers, ...rows].map((r) => r.map((c) => `"${String(c || "").replace(/"/g, '""')}"`).join(",")).join("\n");
    const blob = new Blob(["\uFEFF" + csv], { type: "text/csv;charset=utf-8" });
    const url = URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.href = url; a.download = `seniormassan-anmalningar-${new Date().toISOString().slice(0, 10)}.csv`;
    a.click();
    URL.revokeObjectURL(url);
  };

  const printList = () => window.print();

  const clear = () => {
    if (!confirm("Rensa alla anmälningar? (demo)")) return;
    localStorage.removeItem("sm-registrations");
    setRecords([]);
  };

  return (
    <div style={{
      position: "fixed", inset: 0, zIndex: 102,
      background: "var(--sm-bg)",
      overflowY: "auto",
    }}>
      <div style={{ background: "var(--sm-ink)", color: "#fff", padding: "20px 32px", display: "flex", justifyContent: "space-between", alignItems: "center" }}>
        <div>
          <div style={{ fontSize: 12, letterSpacing: "0.2em", textTransform: "uppercase", opacity: 0.7 }}>Admin — demo</div>
          <div style={{ fontFamily: "var(--sm-font-display)", fontSize: 22, fontWeight: 800 }}>Seniormässan 2027</div>
        </div>
        <div style={{ display: "flex", gap: 12 }}>
          {tab === "anm" && <>
            <button className="sm-btn sm-btn--small" onClick={exportCsv} style={{ background: "var(--sm-accent)" }}>↓ Exportera CSV</button>
            <button className="sm-btn sm-btn--small" onClick={printList} style={{ background: "#fff", color: "var(--sm-ink)" }}>🖨 Skriv ut lista</button>
            <button className="sm-btn sm-btn--small" onClick={clear} style={{ background: "transparent", border: "1.5px solid rgba(255,255,255,0.3)" }}>Rensa</button>
          </>}
          <button className="sm-btn sm-btn--small" onClick={onClose} style={{ background: "rgba(255,255,255,0.15)" }}>Stäng</button>
        </div>
      </div>

      <div style={{ background: "#fff", borderBottom: "1px solid var(--sm-line)", padding: "0 32px", display: "flex", gap: 4 }}>
        {[
          { id: "anm", label: "Anmälningar" },
          { id: "lager", label: "Tillval / lager" },
        ].map((t) => (
          <button key={t.id} onClick={() => setTab(t.id)}
            style={{
              padding: "16px 20px",
              background: "transparent",
              border: "none",
              borderBottom: tab === t.id ? "3px solid var(--sm-primary)" : "3px solid transparent",
              color: tab === t.id ? "var(--sm-primary)" : "var(--sm-ink-soft)",
              fontSize: 15, fontWeight: 700,
              cursor: "pointer",
            }}>{t.label}</button>
        ))}
      </div>

      {tab === "anm" && (
      <div style={{ padding: "32px" }}>
        <div style={{ display: "grid", gridTemplateColumns: "repeat(4, 1fr)", gap: 16, marginBottom: 32 }}>
          <Stat n={records.length} l="Anmälningar" />
          <Stat n={records.reduce((s, r) => s + (r.total || 0), 0).toLocaleString("sv-SE")} l="Summa kr (exkl. moms)" />
          <Stat n={records.reduce((s, r) => s + ((r.booths || []).length), 0)} l="Bokade montrar" />
          <Stat n={(window.BOOTHS || []).length - records.reduce((s, r) => s + ((r.booths || []).length), 0)} l="Kvarvarande lediga" />
        </div>

        <div style={{ background: "#fff", border: "1px solid var(--sm-line)", borderRadius: "var(--sm-radius-lg)", overflow: "hidden" }}>
          <table style={{ width: "100%", borderCollapse: "collapse", fontSize: 15 }}>
            <thead>
              <tr style={{ background: "var(--sm-primary-soft)" }}>
                {["Ref", "Företag", "Kontakt", "Monter", "Paket", "Summa", "Status"].map((h) => (
                  <th key={h} style={{ textAlign: "left", padding: "14px 16px", fontSize: 13, letterSpacing: "0.08em", textTransform: "uppercase", color: "var(--sm-primary)" }}>{h}</th>
                ))}
              </tr>
            </thead>
            <tbody>
              {records.length === 0 && (
                <tr><td colSpan={7} style={{ padding: 48, textAlign: "center", color: "var(--sm-muted)" }}>
                  Inga anmälningar ännu. Gör en anmälan via formuläret för att testa.
                </td></tr>
              )}
              {records.map((r) => (
                <tr key={r.id} style={{ borderTop: "1px solid var(--sm-line)" }}>
                  <td style={{ padding: "14px 16px", fontFamily: "ui-monospace, monospace", fontSize: 13 }}>{r.id}</td>
                  <td style={{ padding: "14px 16px", fontWeight: 700 }}>{r.company}</td>
                  <td style={{ padding: "14px 16px" }}>{r.contactName}<br/><span style={{ color: "var(--sm-muted)", fontSize: 13 }}>{r.contactEmail}</span></td>
                  <td style={{ padding: "14px 16px", fontWeight: 700, color: "var(--sm-primary)" }}>{(r.booths || []).join(", ") || "—"}</td>
                  <td style={{ padding: "14px 16px" }}>{(r.booths || []).length} st</td>
                  <td style={{ padding: "14px 16px", fontWeight: 700 }}>{r.total?.toLocaleString("sv-SE")} kr</td>
                  <td style={{ padding: "14px 16px" }}>
                    <span style={{ background: "var(--sm-primary-soft)", color: "var(--sm-primary)", padding: "3px 10px", borderRadius: 999, fontSize: 13, fontWeight: 700 }}>Ny</span>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>
      )}

      {tab === "lager" && (
        <div style={{ padding: "32px" }}>
          <div style={{ marginBottom: 24 }}>
            <h2 style={{ fontFamily: "var(--sm-font-display)", fontSize: 28, fontWeight: 800, color: "var(--sm-ink)", margin: 0 }}>Tillval / lager</h2>
            <p style={{ color: "var(--sm-ink-soft)", marginTop: 4, fontSize: 15 }}>Stäng av/på tillval. Slutsålda visas som "Slutsåld" och kan inte väljas av utställare.</p>
          </div>
          <div style={{ background: "#fff", border: "1px solid var(--sm-line)", borderRadius: "var(--sm-radius-lg)", overflow: "hidden" }}>
            <table style={{ width: "100%", borderCollapse: "collapse", fontSize: 15 }}>
              <thead>
                <tr style={{ background: "var(--sm-primary-soft)" }}>
                  {["Produkt", "Kategori", "Pris", "Färgval", "Status", ""].map((h) => (
                    <th key={h} style={{ textAlign: "left", padding: "14px 16px", fontSize: 13, letterSpacing: "0.08em", textTransform: "uppercase", color: "var(--sm-primary)" }}>{h}</th>
                  ))}
                </tr>
              </thead>
              <tbody>
                {ADDONS.map((a) => {
                  const inStock = stock[a.id] !== false;
                  return (
                    <tr key={a.id} style={{ borderTop: "1px solid var(--sm-line)" }}>
                      <td style={{ padding: "14px 16px", fontWeight: 700 }}>{a.n}</td>
                      <td style={{ padding: "14px 16px", color: "var(--sm-ink-soft)" }}>{CAT_LABELS[a.cat]}</td>
                      <td style={{ padding: "14px 16px", fontWeight: 700 }}>{a.p.toLocaleString("sv-SE")} kr</td>
                      <td style={{ padding: "14px 16px", fontSize: 13, color: "var(--sm-ink-soft)" }}>{a.variants ? a.variants.join(", ") : "—"}</td>
                      <td style={{ padding: "14px 16px" }}>
                        {inStock ? (
                          <span style={{ background: "#e3f3df", color: "#3c6e36", padding: "3px 10px", borderRadius: 999, fontSize: 13, fontWeight: 700 }}>I lager</span>
                        ) : (
                          <span style={{ background: "#f5d5d5", color: "#a04545", padding: "3px 10px", borderRadius: 999, fontSize: 13, fontWeight: 700 }}>Slutsåld</span>
                        )}
                      </td>
                      <td style={{ padding: "14px 16px", textAlign: "right" }}>
                        <button onClick={() => toggleStock(a.id)} style={{
                          background: inStock ? "transparent" : "var(--sm-primary)",
                          color: inStock ? "var(--sm-primary)" : "#fff",
                          border: "1.5px solid var(--sm-primary)",
                          padding: "6px 14px", borderRadius: 6, fontSize: 13, fontWeight: 700, cursor: "pointer",
                        }}>
                          {inStock ? "Markera slutsåld" : "Markera i lager"}
                        </button>
                      </td>
                    </tr>
                  );
                })}
              </tbody>
            </table>
          </div>
        </div>
      )}
    </div>
  );
}

function Stat({ n, l }) {
  return (
    <div style={{ background: "#fff", border: "1px solid var(--sm-line)", borderRadius: 10, padding: 20 }}>
      <div style={{ fontFamily: "var(--sm-font-display)", fontSize: 36, fontWeight: 800, color: "var(--sm-primary)", letterSpacing: "-0.02em", lineHeight: 1 }}>{n}</div>
      <div style={{ fontSize: 13, color: "var(--sm-ink-soft)", marginTop: 6, letterSpacing: "0.04em", textTransform: "uppercase" }}>{l}</div>
    </div>
  );
}

Object.assign(window, { RegistrationWizard, ConfirmationModal, AdminPanel });
