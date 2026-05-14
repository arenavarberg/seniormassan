/* Seniormässan — webbplats-prototyp (alla sidor i en SPA-vy) */

const NAV = [
  { id: "visitor", label: "För besökare" },
  { id: "program", label: "Program" },
  { id: "info", label: "Hitta hit" },
  { id: "contact", label: "Kontakt" },
  { id: "exhibitor", label: "För utställare" },
];

function SiteHeader({ page, setPage, onRegister }) {
  return (
    <header style={{
      position: "sticky", top: 0, zIndex: 30,
      background: "rgba(250, 247, 242, 0.92)",
      backdropFilter: "blur(10px)",
      borderBottom: "1px solid var(--sm-line-soft)",
    }}>
      <div className="sm-container" style={{
        display: "flex", alignItems: "center", gap: 32,
        padding: "16px 32px",
      }}>
        <button
          onClick={() => setPage("visitor")}
          style={{ background: "none", border: "none", padding: 0, display: "flex", flexDirection: "column", alignItems: "center", gap: 2, cursor: "pointer" }}>
          <img src="assets/senior-logo-2026-transparent.png" alt="Senior" style={{ height: 44, width: "auto", display: "block" }} />
          <div style={{
            display: "flex", alignItems: "center", justifyContent: "center", gap: 10,
            fontFamily: "var(--sm-font-sans)",
            fontSize: 11, fontWeight: 500,
            color: "var(--sm-primary)",
            marginTop: 2,
          }}>
            <span style={{ width: 22, height: 1, background: "currentColor", opacity: 0.55 }} />
            <span style={{ letterSpacing: "0.65em", paddingLeft: "0.65em" }}>MÄSSAN</span>
            <span style={{ width: 22, height: 1, background: "currentColor", opacity: 0.55 }} />
          </div>
        </button>
        <nav style={{ display: "flex", gap: 4, marginLeft: "auto", flexWrap: "wrap" }}>
          {NAV.map((n) => (
            <button key={n.id} onClick={() => setPage(n.id)}
              style={{
                padding: "10px 16px", borderRadius: 8, border: "none",
                background: page === n.id ? "var(--sm-primary-soft)" : "transparent",
                color: page === n.id ? "var(--sm-primary)" : "var(--sm-ink)",
                fontWeight: page === n.id ? 700 : 500,
                fontSize: 16,
              }}>{n.label}</button>
          ))}
        </nav>
        <button className="sm-btn sm-btn--accent sm-btn--small" onClick={onRegister}>
          Boka monter →
        </button>
      </div>
    </header>
  );
}

function Hero({ heroVariant, setPage, onRegister }) {
  /* Full-bleed hero-bild med text-overlay — glada seniorer (placeholder-foto från Unsplash) */
  const heroImage = "https://images.unsplash.com/photo-1556911220-bff31c812dba?w=1920&q=80";

  if (heroVariant === "split") {
    return (
      <section style={{ background: "var(--sm-primary)", color: "#fff" }}>
        <div className="sm-container" style={{ display: "grid", gridTemplateColumns: "1.1fr 1fr", gap: 64, padding: "80px 32px", alignItems: "center" }}>
          <div>
            <div className="sm-eyebrow" style={{ color: "var(--sm-accent)" }}>14–15 oktober 2026 · Arena Varberg</div>
            <h1 style={{ fontSize: "var(--sm-fs-xxl)", color: "#fff" }}>
              Västkustens mötesplats för dig som <span style={{ color: "var(--sm-gold)" }}>lever hela livet</span>.
            </h1>
            <p style={{ fontSize: "var(--sm-fs-lg)", opacity: 0.88, marginTop: 24, fontWeight: 300 }}>
              Två dagar i Arena Varberg — närmare 90 utställare, caféer, bar, lunchservering och ett spännande scenprogram. Sänkt entréavgift 2026.
            </p>
          </div>
          <div style={{
            aspectRatio: "4/5", borderRadius: "var(--sm-radius-lg)", overflow: "hidden",
            backgroundImage: `url(${heroImage})`, backgroundSize: "cover", backgroundPosition: "center",
          }} />
        </div>
      </section>
    );
  }
  return (
    <section style={{ position: "relative", background: "var(--sm-ink)" }}>
      <div style={{
        position: "relative",
        backgroundImage: `linear-gradient(180deg, rgba(15,23,30,0.15) 0%, rgba(15,23,30,0.55) 65%, rgba(15,23,30,0.75) 100%), url(${heroImage})`,
        backgroundSize: "cover",
        backgroundPosition: "center 30%",
        minHeight: 560,
        display: "flex", alignItems: "flex-end",
      }}>
        <div className="sm-container" style={{ padding: "80px 32px 72px", color: "#fff" }}>
          <div className="sm-eyebrow" style={{ color: "var(--sm-gold)", opacity: 1 }}>14–15 oktober 2026 · Arena Varberg</div>
          <h1 style={{
            fontSize: "var(--sm-fs-xxl)", color: "#fff", maxWidth: 900,
            textShadow: "0 2px 20px rgba(0,0,0,0.35)",
          }}>
            För dig som vill <span style={{ color: "var(--sm-gold)" }}>leva hela livet</span> — och njuta av det.
          </h1>
          <p style={{
            fontSize: "var(--sm-fs-lg)", maxWidth: 680, marginTop: 20,
            color: "rgba(255,255,255,0.92)", fontWeight: 400,
            textShadow: "0 1px 10px rgba(0,0,0,0.4)",
          }}>
            Två dagar i Arena Varberg — närmare 90 utställare, scenprogram, caféer, bar och lunch. Sänkt entréavgift 2026.
          </p>
        </div>
      </div>
    </section>
  );
}

function HomePage({ setPage, onRegister, heroVariant }) {
  return (
    <>
      <Hero heroVariant={heroVariant} setPage={setPage} onRegister={onRegister} />

      {/* Siffror */}
      <section style={{ background: "var(--sm-surface)", borderTop: "1px solid var(--sm-line)", borderBottom: "1px solid var(--sm-line)" }}>
        <div className="sm-container" style={{ display: "grid", gridTemplateColumns: "repeat(4, 1fr)", padding: "48px 32px", gap: 32 }}>
          {[
            ["~90", "utställare"],
            ["~2000", "besökare"],
            ["2", "caféer + bar"],
            ["10+ år", "tradition"],
          ].map(([n, l]) => (
            <div key={l} style={{ textAlign: "center" }}>
              <div style={{ fontFamily: "var(--sm-font-display)", fontSize: 56, fontWeight: 800, color: "var(--sm-primary)", letterSpacing: "-0.03em", lineHeight: 1 }}>{n}</div>
              <div style={{ fontSize: 16, color: "var(--sm-ink-soft)", marginTop: 8, letterSpacing: "0.04em" }}>{l}</div>
            </div>
          ))}
        </div>
      </section>

      {/* Två-spalts entry */}
      <section className="sm-container" style={{ padding: "96px 32px" }}>
        <div style={{ display: "grid", gridTemplateColumns: "1fr 1fr", gap: 32 }}>
          <EntryCard
            tag="FÖR BESÖKARE"
            title="Ett äventyr mitt i Varberg"
            body="Träffa utställare inom resor, hälsa, boende, ekonomi, teknik och kultur. Lyssna på föreläsningar, prova en ny hobby och ta en fika med gamla och nya vänner."
            cta="Se programmet →"
            onClick={() => setPage("visitor")}
            tone="primary"
          />
          <EntryCard
            tag="FÖR UTSTÄLLARE"
            title="Möt en köpstark målgrupp"
            body="Svenska seniorer står för mer än hälften av all konsumtion av fritid, hälsa och resor. På Seniormässan träffar du 6 000 kvalificerade besökare på två dagar."
            cta="Boka monter →"
            onClick={() => setPage("exhibitor")}
            tone="accent"
          />
        </div>
      </section>

      {/* Highlights */}
      <section style={{ background: "var(--sm-surface)", borderTop: "1px solid var(--sm-line)" }}>
        <div className="sm-container" style={{ padding: "96px 32px" }}>
          <div className="sm-eyebrow">Höjdpunkter 2026</div>
          <h2 style={{ fontSize: "var(--sm-fs-xl)", maxWidth: 700 }}>Det du inte vill missa.</h2>
          <div style={{ display: "grid", gridTemplateColumns: "repeat(3, 1fr)", gap: 24, marginTop: 48 }}>
            {[
              { k: "Scenprogram", t: "Författarsamtal & musik", d: "Scenprogram som berör. Från livemusik till föreläsningar om det goda livet efter 70." },
              { k: "Provspring", t: "Testa nya aktiviteter", d: "Prova pickleball, boule, linedance, digitalt målande och mycket mer — helt gratis." },
              { k: "Resecentrum", t: "Drömresor & bussutflykter", d: "Plocka hem vårens bästa reseidéer. Mässpriser hos 20+ researrangörer." },
            ].map((h) => (
              <article key={h.k} className="sm-card" style={{ borderTop: "4px solid var(--sm-accent)" }}>
                <div style={{ fontSize: 13, fontWeight: 700, letterSpacing: "0.16em", textTransform: "uppercase", color: "var(--sm-muted)" }}>{h.k}</div>
                <h3 style={{ fontSize: 26, marginTop: 10, marginBottom: 14 }}>{h.t}</h3>
                <p style={{ color: "var(--sm-ink-soft)", fontSize: 17, margin: 0 }}>{h.d}</p>
              </article>
            ))}
          </div>
        </div>
      </section>

      {/* Partner bar */}
      <section className="sm-container" style={{ padding: "64px 32px" }}>
        <div style={{ fontSize: 14, letterSpacing: "0.2em", textTransform: "uppercase", color: "var(--sm-muted)", textAlign: "center", marginBottom: 24 }}>
          Samarrangeras med
        </div>
        <div style={{ display: "flex", justifyContent: "center", alignItems: "center", gap: 48, flexWrap: "wrap", opacity: 0.7 }}>
          {["Varbergs kommun", "PRO Varberg", "SPF Seniorerna", "Hallands Nyheter", "Region Halland"].map((p) => (
            <div key={p} style={{ fontFamily: "var(--sm-font-display)", fontWeight: 700, fontSize: 20, color: "var(--sm-ink-soft)" }}>{p}</div>
          ))}
        </div>
      </section>
    </>
  );
}

function EntryCard({ tag, title, body, cta, onClick, tone }) {
  const isAccent = tone === "accent";
  return (
    <button onClick={onClick} style={{
      textAlign: "left", border: "none", padding: 0, cursor: "pointer",
      background: isAccent ? "var(--sm-accent)" : "var(--sm-primary)",
      color: "#fff", borderRadius: "var(--sm-radius-lg)",
      padding: 48, display: "flex", flexDirection: "column",
      minHeight: 380, transition: "transform 0.2s ease, box-shadow 0.2s ease",
    }}
      onMouseEnter={(e) => { e.currentTarget.style.transform = "translateY(-4px)"; e.currentTarget.style.boxShadow = "var(--sm-shadow-md)"; }}
      onMouseLeave={(e) => { e.currentTarget.style.transform = ""; e.currentTarget.style.boxShadow = ""; }}
    >
      <div style={{ fontSize: 13, fontWeight: 700, letterSpacing: "0.18em", opacity: 0.85 }}>{tag}</div>
      <h3 style={{ fontSize: 40, marginTop: 16, color: "#fff" }}>{title}</h3>
      <p style={{ fontSize: 19, opacity: 0.92, marginTop: 16 }}>{body}</p>
      <div style={{ marginTop: "auto", paddingTop: 24, fontSize: 19, fontWeight: 700 }}>{cta}</div>
    </button>
  );
}

function SiteFooter() {
  return (
    <footer style={{ background: "var(--sm-ink)", color: "rgba(255,255,255,0.82)", marginTop: 0 }}>
      <div className="sm-container" style={{ padding: "64px 32px 32px", display: "grid", gridTemplateColumns: "1.5fr 1fr 1fr 1fr", gap: 48 }}>
        <div>
          <div style={{ display: "inline-flex", flexDirection: "column", alignItems: "center", gap: 4 }}>
            <img src="assets/senior-logo-2026-transparent.png" alt="Senior" style={{ height: 56, width: "auto", filter: "brightness(0) invert(1)", opacity: 0.95 }} />
            <div style={{
              display: "flex", alignItems: "center", justifyContent: "center", gap: 10,
              fontFamily: "var(--sm-font-sans)",
              fontSize: 12, fontWeight: 500,
              color: "#fff",
              opacity: 0.9,
            }}>
              <span style={{ width: 28, height: 1, background: "currentColor", opacity: 0.55 }} />
              <span style={{ letterSpacing: "0.65em", paddingLeft: "0.65em" }}>MÄSSAN</span>
              <span style={{ width: 28, height: 1, background: "currentColor", opacity: 0.55 }} />
            </div>
          </div>
          <div style={{ fontSize: 13, letterSpacing: "0.2em", textTransform: "uppercase", marginTop: 16, opacity: 0.6 }}>Sedan 2013</div>
          <p style={{ marginTop: 24, opacity: 0.75, maxWidth: 360 }}>
            En mötesplats för seniorer i Halland — arrangerad av Arena Varberg.
          </p>
          <div style={{ marginTop: 28, display: "flex", alignItems: "center", gap: 14 }}>
            <div style={{ fontSize: 11, letterSpacing: "0.22em", textTransform: "uppercase", opacity: 0.55 }}>Arrangör</div>
            <img src="assets/arena-varberg-logo-white.png" alt="Arena Varberg" style={{ height: 56, width: "auto" }} />
          </div>
        </div>
        <div>
          <div style={{ fontWeight: 700, color: "#fff", marginBottom: 14 }}>Mässan</div>
          {["Program", "Hitta hit", "Parkering", "Tillgänglighet"].map((l) => (
            <div key={l} style={{ marginBottom: 8, opacity: 0.8, fontSize: 16 }}>{l}</div>
          ))}
        </div>
        <div>
          <div style={{ fontWeight: 700, color: "#fff", marginBottom: 14 }}>Utställare</div>
          {["Bli utställare", "Priser & paket", "Ladda ner säljblad", "Villkor"].map((l) => (
            <div key={l} style={{ marginBottom: 8, opacity: 0.8, fontSize: 16 }}>{l}</div>
          ))}
        </div>
        <div>
          <div style={{ fontWeight: 700, color: "#fff", marginBottom: 14 }}>Kontakt</div>
          <div style={{ opacity: 0.8, fontSize: 16, lineHeight: 1.8 }}>
            Arena Varberg<br/>
            0340-69 02 00<br/>
            info@arenavarberg.se
          </div>
        </div>
      </div>
      <div style={{ borderTop: "1px solid rgba(255,255,255,0.12)", padding: "20px 32px", fontSize: 14, opacity: 0.55, textAlign: "center" }}>
        © 2026 Arena Varberg AB
      </div>
    </footer>
  );
}

Object.assign(window, { SiteHeader, Hero, HomePage, EntryCard, SiteFooter, NAV });
