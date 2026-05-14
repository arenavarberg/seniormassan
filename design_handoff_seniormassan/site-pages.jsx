/* Seniormässan — undersidor: Besökare, Utställare, Program, Info, Kontakt */

/* Mjuk vågdelare mellan sektioner. from = färgen OVANFÖR, to = färgen NEDAN.
   flip=true vänder vågen så den "kupar" åt andra hållet. */
function WaveDivider({ from = "var(--sm-bg)", to = "var(--sm-surface)", flip = false, height = 70 }) {
  const path = flip ?
  "M0,60 C240,0 480,100 720,60 C960,20 1200,80 1440,40 L1440,121 L0,121 Z" :
  "M0,40 C240,90 480,10 720,60 C960,100 1200,30 1440,70 L1440,121 L0,121 Z";
  return (
    <div style={{ background: from, lineHeight: 0, marginBottom: -1, position: "relative", zIndex: 1 }}>
      <svg viewBox="0 0 1440 120" preserveAspectRatio="none"
      style={{ display: "block", width: "100%", height, marginBottom: -1 }}>
        <path d={path} fill={to} />
      </svg>
    </div>);

}

/* Vimeo-embed i ramverk — bevarar 16:9 + rundade hörn */
function VimeoEmbed({ id = "1178774214", title = "Seniormässan 2025" }) {
  return (
    <div style={{
      position: "relative", paddingTop: "56.25%",
      borderRadius: "var(--sm-radius-lg)", overflow: "hidden",
      boxShadow: "var(--sm-shadow-md)", background: "#000"
    }}>
      <iframe
        src={`https://player.vimeo.com/video/${id}?title=0&byline=0&portrait=0`}
        title={title}
        style={{ position: "absolute", inset: 0, width: "100%", height: "100%", border: 0 }}
        allow="autoplay; fullscreen; picture-in-picture"
        allowFullScreen />
      
    </div>);

}

function VisitorPage({ onRegister, heroVariant, setPage, isHome }) {
  const heroImages = [
  "assets/hero-5.jpg",
  "assets/hero-1.jpg",
  "assets/hero-7.jpg",
  "assets/hero-2.jpg",
  "assets/hero-6.jpg",
  "assets/hero-3.jpg",
  "assets/hero-4.jpg"];

  const [heroIdx, setHeroIdx] = React.useState(0);
  React.useEffect(() => {
    const id = setInterval(() => setHeroIdx((i) => (i + 1) % heroImages.length), 5500);
    return () => clearInterval(id);
  }, []);
  const galleryImages = [
  "assets/hero-1.jpg",
  "assets/hero-2.jpg",
  "assets/hero-3.jpg"];

  return (
    <>
      {/* Full-bleed hero med bildspel */}
      <section style={{ position: "relative", background: "var(--sm-ink)", overflow: "hidden" }}>
        {heroImages.map((src, i) =>
        <div key={src} aria-hidden={i !== heroIdx} style={{
          position: "absolute", inset: 0,
          backgroundImage: `url(${src})`,
          backgroundSize: "cover",
          backgroundPosition: "center 35%",
          opacity: i === heroIdx ? 1 : 0,
          transition: "opacity 1.2s ease-in-out"
        }} />
        )}
        <div style={{
          position: "relative",
          background: "linear-gradient(180deg, rgba(15,23,30,0.15) 0%, rgba(15,23,30,0.55) 60%, rgba(15,23,30,0.82) 100%)",
          minHeight: 620,
          display: "flex", alignItems: "flex-end"
        }}>
          <div className="sm-container" style={{ padding: "80px 32px 88px", color: "#fff", position: "relative", zIndex: 2 }}>
            <h1 style={{ fontSize: "var(--sm-fs-xxl)", color: "#fff", maxWidth: 920, textShadow: "0 2px 20px rgba(0,0,0,0.45)" }}>
              Seniormässan <span style={{ color: "var(--sm-gold)" }}>24 feb 2027</span>
            </h1>
            <p style={{ fontSize: "var(--sm-fs-lg)", maxWidth: 720, marginTop: 20, color: "rgba(255,255,255,0.95)", textShadow: "0 1px 10px rgba(0,0,0,0.5)" }}>
              En dag fylld av möten, upplevelser och inspiration — närmare 90 utställare, scenprogram, caféer och restaurang.
            </p>
          </div>
        </div>
        {/* Mjuk våg längst ner i hero */}
        <svg viewBox="0 0 1440 120" preserveAspectRatio="none" aria-hidden="true"
        style={{ display: "block", position: "absolute", left: 0, right: 0, bottom: -1, width: "100%", height: 90, zIndex: 2, pointerEvents: "none" }}>
          <path d="M0,60 C240,10 480,110 720,70 C960,30 1200,90 1440,50 L1440,120 L0,120 Z" fill="var(--sm-bg)" />
        </svg>
      </section>

      <WaveDivider from="transparent" to="var(--sm-bg)" />
      {/* Siffror — i egen våg */}
      <section style={{ background: "var(--sm-bg)" }}>
        <div className="sm-container" style={{ display: "grid", gridTemplateColumns: "repeat(4, 1fr)", padding: "0 32px 64px", gap: 32 }}>
          {[["~90", "utställare"], ["~2000", "besökare"], ["2", "caféer + bar"], ["10+ år", "tradition"]].map(([n, l]) =>
          <div key={l} style={{ textAlign: "center" }}>
              <div style={{ fontFamily: "var(--sm-font-display)", fontSize: 56, fontWeight: 800, color: "var(--sm-primary)", letterSpacing: "-0.03em", lineHeight: 1 }}>{n}</div>
              <div style={{ fontSize: 16, color: "var(--sm-ink-soft)", marginTop: 8, letterSpacing: "0.04em" }}>{l}</div>
            </div>
          )}
        </div>
      </section>
      <WaveDivider from="var(--sm-bg)" to="var(--sm-surface)" flip />
      {/* Video — förra årets mässa */}
      <section style={{ background: "var(--sm-surface)" }}>
        <div className="sm-container" style={{ padding: "72px 32px" }}>
          <div style={{ textAlign: "center", marginBottom: 32 }}>
            <div className="sm-eyebrow">Så var det 2025</div>
            <h2 style={{ fontSize: "var(--sm-fs-xl)", maxWidth: 720, margin: "0 auto" }}>En liten smakbit av förra årets mässa.</h2>
          </div>
          <div style={{ maxWidth: 960, margin: "0 auto" }}>
            <VimeoEmbed id="1178774214" title="Seniormässan 2025" />
          </div>
        </div>
      </section>
      <WaveDivider from="var(--sm-surface)" to="var(--sm-bg)" flip height={60} />
      {/* Tider & praktisk info — med bild bredvid */}
      <section style={{ background: "var(--sm-bg)" }}><div className="sm-container" style={{ padding: "80px 32px" }}>
        <div style={{ display: "grid", gridTemplateColumns: "1fr 1fr", gap: 56, alignItems: "center" }}>
          <div>
            <div className="sm-eyebrow">Praktiskt</div>
            <h2 style={{ fontSize: "var(--sm-fs-xl)" }}>Hela dagen i ett svep.</h2>
            <p style={{ fontSize: "var(--sm-fs-lg)", color: "var(--sm-ink-soft)", marginTop: 16 }}>
              En dag som rymmer allt. Kom när dörrarna öppnar — eller bara en stund när det passar.
            </p>
            <div style={{ marginTop: 32 }}>
              <InfoBlock title="När?" items={[
                ["Onsdag 24 februari 2027", "10.00 – 17.00"],
                ["", "Dörrarna öppnar 09.30"]]
                } />
              <InfoBlock title="Hur?" items={[
                ["Entré", "100 kr i förköp · 120 kr vid dörren (swish / kort)"],
                ["Fri parkering", "över 200 platser på området"],
                ["Buss", "Linje 1, 2 och 4 till Arena Varberg"]]
                } />
            </div>
          </div>
          <div style={{
              aspectRatio: "4/5", borderRadius: "var(--sm-radius-lg)", overflow: "hidden",
              backgroundImage: `url(${galleryImages[0]})`, backgroundSize: "cover", backgroundPosition: "center"
            }} />
        </div>
      </div></section>
      <WaveDivider from="var(--sm-bg)" to="var(--sm-surface)" />
      {/* Områden — med bild */}
      <section style={{ background: "var(--sm-surface)" }}>
        <div className="sm-container" style={{ padding: "96px 32px" }}>
          <div style={{ display: "grid", gridTemplateColumns: "1fr 1.3fr", gap: 56, alignItems: "center", marginBottom: 56 }}>
            <div style={{
              aspectRatio: "4/3", borderRadius: "var(--sm-radius-lg)", overflow: "hidden",
              backgroundImage: `url(${galleryImages[1]})`, backgroundSize: "cover", backgroundPosition: "center"
            }} />
            <div>
              <div className="sm-eyebrow">Områden</div>
              <h2 style={{ fontSize: "var(--sm-fs-xl)" }}>Sex världar att upptäcka.</h2>
              <p style={{ fontSize: "var(--sm-fs-lg)", color: "var(--sm-ink-soft)", marginTop: 16 }}>
                Utställare inom det som berör vardagen — från drömresor till digital hjälp i lugn takt.
              </p>
            </div>
          </div>
          <div style={{ display: "grid", gridTemplateColumns: "repeat(3, 1fr)", gap: 24 }}>
            {[
            { n: "01", t: "Resor & Upplevelser", d: "Från bussresor i Europa till kryssningar och kulturresor." },
            { n: "02", t: "Hälsa & Välmående", d: "Hörsel, syn, tandvård, fysioterapi och mental hälsa." },
            { n: "03", t: "Boende", d: "Seniorboenden, hemtjänst, tillgänglighetsanpassning, flyttfirmor." },
            { n: "04", t: "Ekonomi & Juridik", d: "Pension, arv och testamente, bankfrågor och försäkring." },
            { n: "05", t: "Teknik", d: "Smartphones, larm, hörselslingor — personlig hjälp i lugn takt." },
            { n: "06", t: "Kultur & Fritid", d: "Studieförbund, körer, bokklubbar, golf, boule, dans." }].
            map((z) =>
            <div key={z.n} style={{ padding: "28px 24px", background: "var(--sm-bg)", borderRadius: "var(--sm-radius-lg)" }}>
                <div style={{ display: "flex", justifyContent: "space-between", alignItems: "baseline" }}>
                  <div style={{ fontFamily: "var(--sm-font-display)", fontSize: 22, fontWeight: 800 }}>{z.t}</div>
                  <div style={{ fontFamily: "var(--sm-font-display)", fontSize: 18, color: "var(--sm-accent)", fontWeight: 700 }}>{z.n}</div>
                </div>
                <p style={{ color: "var(--sm-ink-soft)", marginTop: 10, fontSize: 17, marginBottom: 0 }}>{z.d}</p>
              </div>
            )}
          </div>
        </div>
      </section>

      <WaveDivider from="var(--sm-surface)" to="var(--sm-primary)" flip />
      <CTABand title="Ta med en vän — det blir roligare så." body="Dela gärna inbjudan. Köp biljett i förköp för 100 kr, eller 120 kr vid dörren." />

      <WaveDivider from="var(--sm-primary)" to="var(--sm-bg)" flip />
      {/* Höjdpunkter */}
      <section style={{ background: "var(--sm-bg)" }}><div className="sm-container" style={{ padding: "96px 32px" }}>
        <div className="sm-eyebrow">Höjdpunkter 2027</div>
        <h2 style={{ fontSize: "var(--sm-fs-xl)", maxWidth: 700 }}>Det du inte vill missa.</h2>
        <div style={{ display: "grid", gridTemplateColumns: "repeat(3, 1fr)", gap: 24, marginTop: 48 }}>
          {[
            { img: "images/scenprogram.jpg", k: "Scenprogram", t: "Författarsamtal & musik", d: "Scenprogram som berör. Från livemusik till föreläsningar om det goda livet efter 70." },
            { img: "images/boule.jpg", k: "Provspring", t: "Testa nya aktiviteter", d: "Prova curling, boule, linedance, innebandy och mycket mer — helt gratis." },
            { img: "images/resecentrum.jpg", k: "Resecentrum", t: "Drömresor & bussutflykter", d: "Plocka hem vårens bästa reseidéer. Mässpriser hos 20+ researrangörer." }].
            map((h) =>
            <article key={h.k} style={{ background: "var(--sm-surface)", borderRadius: "var(--sm-radius-lg)", overflow: "hidden", boxShadow: "var(--sm-shadow-sm)" }}>
              <div style={{ aspectRatio: "16/10", backgroundImage: `url(${h.img})`, backgroundSize: "cover", backgroundPosition: "center" }} />
              <div style={{ padding: 28 }}>
                <div style={{ fontSize: 13, fontWeight: 700, letterSpacing: "0.16em", textTransform: "uppercase", color: "var(--sm-accent)" }}>{h.k}</div>
                <h3 style={{ fontSize: 24, marginTop: 10, marginBottom: 12 }}>{h.t}</h3>
                <p style={{ color: "var(--sm-ink-soft)", fontSize: 17, margin: 0 }}>{h.d}</p>
              </div>
            </article>
            )}
        </div>
      </div></section>

      <WaveDivider from="var(--sm-bg)" to="var(--sm-surface)" />
      <ExhibitorList />
    </>);

}

function ExhibitorPage({ onRegister }) {
  const packages = [
  { size: "2 × 2 m", price: 3820, ideal: "Standardmonter för enskild utställare", includes: ["El (230 V)", "Vita mässväggar", "Omnämning i program", "5 digitala entrébiljetter"] },
  { size: "2 × 3 m", price: 5730, ideal: "Mest populära paketet", includes: ["El (230 V)", "Vita mässväggar", "Omnämning i program", "5 digitala entrébiljetter"], featured: true },
  { size: "3 × 3 m", price: 8845, ideal: "Stor monter med extra synlighet", includes: ["El (230 V)", "Vita mässväggar", "Omnämning i program", "5 digitala entrébiljetter"] },
  { size: "Förening", price: 2360, priceLabel: "inkl. moms", ideal: "2 × 2 m · Föreningspris (ideella föreningar)", includes: ["El (230 V)", "Vita mässväggar", "Omnämning i program"] }];

  const addons = [
  ["Registreringsavgift (obligatorisk)", 800],
  ["Montermatta", 110],
  ["Wifi (premium)", 450],
  ["Monterbord", 350],
  ["Stol", 120],
  ["Matbiljett utställare", 180]];

  return (
    <>
      <PageHero
        eyebrow="För utställare"
        title="Möt ~2000 engagerade seniorer."
        body="Besökare tillbringar många timmar här och har god tid att besöka alla utställare — utan stress. Seniormässan är en etablerad mötesplats i Halland sedan 10+ år."
        tone="accent"
        cta="Gå till anmälan →"
        onCta={onRegister} />
      

      <WaveDivider from="var(--sm-accent)" to="var(--sm-bg)" />
      {/* Nyhet 2027 — eget avsnitt */}
      <section style={{ background: "var(--sm-bg)" }}>
        <div className="sm-container" style={{ padding: "88px 32px" }}>
          <div style={{
            background: "var(--sm-surface)",
            borderRadius: "var(--sm-radius-lg)",
            padding: "56px 56px",
            display: "grid",
            gridTemplateColumns: "auto 1fr",
            gap: 48,
            alignItems: "center",
            boxShadow: "var(--sm-shadow-sm)",
            position: "relative",
            overflow: "hidden"
          }}>
            <div aria-hidden="true" style={{
              position: "absolute", top: -40, right: -40,
              width: 220, height: 220, borderRadius: "50%",
              background: "var(--sm-gold)", opacity: 0.18
            }} />
            <div style={{
              width: 120, height: 120, borderRadius: "50%",
              background: "var(--sm-accent)", color: "#fff",
              display: "flex", alignItems: "center", justifyContent: "center",
              fontFamily: "var(--sm-font-display)", fontWeight: 800,
              fontSize: 22, lineHeight: 1.05, textAlign: "center",
              flexShrink: 0,
              transform: "rotate(-6deg)",
              boxShadow: "0 6px 18px rgba(198, 55, 29, 0.35)"
            }}>
              Nyhet<br />2027
            </div>
            <div style={{ position: "relative", zIndex: 1 }}>
              <div className="sm-eyebrow" style={{ marginBottom: 12 }}>Nyhet 2027</div>
              <h2 style={{ fontSize: "var(--sm-fs-xl)", marginBottom: 20, maxWidth: 720 }}>
                5 digitala entrébiljetter ingår — bjud in dina kunder.
              </h2>
              <p style={{ fontSize: "var(--sm-fs-lg)", color: "var(--sm-ink-soft)", maxWidth: 720, marginBottom: 0 }}>
                I varje bokad monter ingår 5 stycken kostnadsfria digitala entrébiljetter. Dessa digitala entrébiljetter kan du som utställare dela ut bland kunder, medlemmar eller någon som du önskar bjuda in till mässan.
              </p>
            </div>
          </div>
        </div>
      </section>

      {/* Full-bleed bild — glad besökare i monter */}
      <section style={{
        position: "relative",
        height: 480,
        backgroundImage: "url('images/senior-wide.jpg')",
        backgroundSize: "cover",
        backgroundPosition: "center 35%"
      }} />

      {/* Varför ställa ut? */}
      <section style={{ background: "var(--sm-bg)" }}><div className="sm-container" style={{ padding: "80px 32px 40px" }}>
        <div className="sm-eyebrow">Varför ställa ut?</div>
        <h2 style={{ fontSize: "var(--sm-fs-xl)", maxWidth: 800 }}>Kvalificerade möten, inte flyktiga förbipasseranden.</h2>
        <div style={{ display: "grid", gridTemplateColumns: "repeat(3, 1fr)", gap: 40, marginTop: 56 }}>
          {[
            { n: "01", t: "Målgrupp med tid & lust", d: "Seniorer är en målgrupp som växer varje år — med både tid, nyfikenhet och köpkraft." },
            { n: "02", t: "Långa kvalitetsmöten", d: "Besökare stannar många timmar tack vare scen, caféer, bar och lunchservering. Tid för riktiga samtal." },
            { n: "03", t: "Vi marknadsför åt dig", d: "Annonser i dagspress, digitala kanaler, vägpratare och via organisationer. 2027 även mot Sjuhärad." }].
            map((b) =>
            <div key={b.n}>
              <div style={{ fontFamily: "var(--sm-font-display)", fontSize: 64, fontWeight: 800, color: "var(--sm-accent)", letterSpacing: "-0.04em", lineHeight: 1 }}>{b.n}</div>
              <h3 style={{ fontSize: 24, marginTop: 16, marginBottom: 10 }}>{b.t}</h3>
              <p style={{ color: "var(--sm-ink-soft)", margin: 0 }}>{b.d}</p>
            </div>
            )}
        </div>
      </div></section>

      {/* Utställarscen — egen sektion */}
      <section style={{ background: "var(--sm-bg)" }}>
        <div className="sm-container" style={{ padding: "40px 32px 80px" }}>
          <div style={{
            background: "var(--sm-surface)",
            borderRadius: "var(--sm-radius-lg)",
            padding: "56px 56px",
            border: "1px solid var(--sm-line-soft)",
            display: "grid",
            gridTemplateColumns: "1.2fr 1fr",
            gap: 48,
            alignItems: "center"
          }}>
            <div>
              <div className="sm-eyebrow" style={{ marginBottom: 12 }}>Utställarscen</div>
              <h2 style={{ fontSize: "var(--sm-fs-xl)", marginBottom: 20, maxWidth: 560 }}>
                Nå ut med ditt budskap på vår utställarscen.
              </h2>
              <p style={{ fontSize: 18, color: "var(--sm-ink-soft)", margin: 0, maxWidth: 560 }}>
                Samtidigt som vi släpper upp monterbokningen släpper vi också upp tidsbokningen till vår utställarscen. Det är kostnadsfritt och först till kvarn som gäller — så passa på och knip dessa eftertraktade platserna.
              </p>
            </div>
            <div style={{
              aspectRatio: "4 / 3",
              borderRadius: "var(--sm-radius-md)",
              backgroundImage: "url('images/scenprogram.jpg')",
              backgroundSize: "cover",
              backgroundPosition: "center"
            }} />
          </div>
        </div>
      </section>
      <WaveDivider from="var(--sm-bg)" to="var(--sm-surface)" flip />
      <section style={{ background: "var(--sm-surface)" }}>
        <div className="sm-container" style={{ padding: "80px 32px" }}>
          <div className="sm-eyebrow">Monterpaket</div>
          <h2 style={{ fontSize: "var(--sm-fs-xl)" }}>Tre storlekar. Alla inklusive det grundläggande.</h2>
          <div style={{ display: "grid", gridTemplateColumns: "repeat(4, 1fr)", gap: 16, marginTop: 48 }} className="sm-pkg-grid">
            {packages.map((p, idx) =>
            <div key={idx} style={{
              background: p.featured ? "var(--sm-primary)" : "var(--sm-bg)",
              color: p.featured ? "#fff" : "var(--sm-ink)",
              borderRadius: "var(--sm-radius-lg)",
              padding: 28, position: "relative",
              border: p.featured ? "none" : "1px solid var(--sm-line)"
            }}>
                {p.featured &&
              <div style={{ position: "absolute", top: 14, right: 14, background: "var(--sm-accent)", color: "#fff", padding: "4px 10px", borderRadius: 4, fontSize: 12, fontWeight: 700, letterSpacing: "0.08em" }}>POPULÄRAST</div>
              }
                <div style={{ fontFamily: "var(--sm-font-display)", fontSize: 28, fontWeight: 800, letterSpacing: "-0.02em" }}>{p.size}</div>
                <div style={{ fontSize: 14, opacity: 0.75, marginTop: 4, minHeight: 40 }}>{p.ideal}</div>
                <div style={{ fontFamily: "var(--sm-font-display)", fontSize: 40, fontWeight: 800, marginTop: 16 }}>
                  {p.price.toLocaleString("sv-SE")} <span style={{ fontSize: 16, opacity: 0.7, fontWeight: 500 }}>kr</span>
                </div>
                <div style={{ fontSize: 13, opacity: 0.7, marginBottom: 18 }}>{p.priceLabel || "exkl. moms"}</div>
                <ul style={{ paddingLeft: 0, listStyle: "none", margin: 0, borderTop: `1px solid ${p.featured ? "rgba(255,255,255,0.2)" : "var(--sm-line)"}`, paddingTop: 14 }}>
                  {p.includes.map((i) =>
                <li key={i} style={{ fontSize: 15, padding: "6px 0", display: "flex", gap: 8, alignItems: "flex-start" }}>
                      <span style={{ color: p.featured ? "var(--sm-accent)" : "var(--sm-primary)", fontWeight: 700 }}>✓</span>{i}
                    </li>
                )}
                </ul>
              </div>
            )}
          </div>

          <div style={{ marginTop: 64 }}>
            <div className="sm-eyebrow">Tillägg</div>
            <h3 style={{ fontSize: 28, marginBottom: 8 }}>Skräddarsy din monter.</h3>
            <p style={{ fontSize: 17, color: "var(--sm-ink-soft)", marginBottom: 24, maxWidth: 720, lineHeight: 1.5 }}>
              När du gör din bokning kan du lägga till produkter i din monter — t.ex. golv i olika färger, bord, stolar, utställarlunch m.m.
            </p>
            <div style={{ display: "grid", gridTemplateColumns: "repeat(3, 1fr)", gap: 0, border: "1px solid var(--sm-line)", borderRadius: "var(--sm-radius-lg)", overflow: "hidden", background: "var(--sm-bg)" }}>
              {addons.map(([n, p], i) =>
              <div key={n} style={{
                padding: "20px 24px", display: "flex", justifyContent: "space-between",
                borderBottom: i < addons.length - 3 ? "1px solid var(--sm-line)" : "none",
                borderRight: (i + 1) % 3 !== 0 ? "1px solid var(--sm-line)" : "none"
              }}>
                  <span style={{ fontSize: 17 }}>{n}</span>
                  <span style={{ fontWeight: 700, color: "var(--sm-primary)" }}>{p} kr</span>
                </div>
              )}
            </div>
          </div>

          <div style={{ textAlign: "center", marginTop: 56 }}>
            <button className="sm-btn sm-btn--accent" onClick={onRegister}>Anmäl din monter →</button>
            <div style={{ marginTop: 14, fontSize: 15, color: "var(--sm-muted)" }}>Sista anmälningsdag: 15 augusti 2027</div>
          </div>
        </div>
      </section>

      <WaveDivider from="var(--sm-surface)" to="var(--sm-bg)" />
      <section style={{ background: "var(--sm-bg)" }}><div className="sm-container" style={{ padding: "80px 32px" }}>
        <div style={{ textAlign: "center", marginBottom: 32 }}>
          <div className="sm-eyebrow">Stämningen 2025</div>
          <h2 style={{ fontSize: "var(--sm-fs-xl)", maxWidth: 760, margin: "0 auto" }}>Så här ser det ut att ställa ut hos oss.</h2>
        </div>
        <div style={{ maxWidth: 960, margin: "0 auto" }}>
          <VimeoEmbed id="1178774214" title="Seniormässan 2025 — för utställare" />
        </div>
      </div></section>
      <WaveDivider from="var(--sm-bg)" to="var(--sm-bg)" flip />
      <section style={{ background: "var(--sm-bg)" }}><div className="sm-container" style={{ padding: "80px 32px" }}>
        <div style={{ display: "grid", gridTemplateColumns: "260px 1fr", gap: 48, alignItems: "center" }} className="sm-quote-grid">
          <img src="images/annett-wiktorsson.webp" alt="Annett Wiktorsson, Modestugan"
            style={{ width: "100%", aspectRatio: "1 / 1", objectFit: "cover", borderRadius: "50%", boxShadow: "var(--sm-shadow-md)" }} />
          <blockquote style={{ margin: 0, maxWidth: 820, fontSize: 30, fontFamily: "var(--sm-font-display)", fontWeight: 600, lineHeight: 1.3, letterSpacing: "-0.01em" }}>
            ”Många åker till vår butik i Åsa och handlar direkt efter att vi träffats på mässan.”
            <footer style={{ fontSize: 16, fontFamily: "var(--sm-font-body)", fontWeight: 500, marginTop: 20, color: "var(--sm-ink-soft)" }}>
              — Annett Wiktorsson, Modestugan <span style={{ opacity: 0.7 }}>(deltagit sedan 2015)</span>
            </footer>
          </blockquote>
        </div>
      </div></section>
    </>);

}

function ProgramPage() {
  const utstallarscenen = [
  { t: "11:00", n: "Glaukomföreningen", d: "Den lömska smygande ögonsjukdomen Glaukom" },
  { t: "11:30", n: "Hantverksmagasinet", d: "Kan hantverk förändra världen?" },
  { t: "12:00", n: "Teater Halland", d: "En plats för möten, samtal och starka teaterupplevelser" },
  { t: "12:30", n: "Jourhavande medmänniska", d: "Informerar om sin verksamhet" },
  { t: "13:00", n: "Varbergs kommun", d: "Välkommen på en digital promenad i framtidens Västerport!" },
  { t: "13:30", n: "Varberg Energi", d: "Få bättre koll på din energi — på ett enkelt sätt!" },
  { t: "14:00", n: "Fot & Fin", d: "Har vi slutat lita på våra fötter?" },
  { t: "14:30", n: "Samtalsbyrån Varberg", d: "Sorg genom livet — och vägarna som hjälper oss vidare" }];

  const storaScen = [
  { t: "10:00", n: "Peter Börjesson", d: "Välkomnar från scenen" },
  { t: "10:15", n: "Modevisning", d: "Rydholms Modehus & Dressmann" },
  { t: "11:00", n: "Lotta Engberg", d: "Livemusik med pianist" },
  { t: "11:45", n: "Elisabeth Wahlin", d: "100% livslust" },
  { t: "13:15", n: "Linedance", d: "Prova på med Tobias Herbertzon" },
  { t: "14:00", n: "Modevisning", d: "Modestugan visar höstens mode" },
  { t: "14:30", n: "Lotta Engberg", d: "Livemusik med pianist" },
  { t: "15:00", n: "Föreläsning om svenskt vin", d: "Branschorganisationen svenskt vin" },
  { t: "15:40", n: "Peter Börjesson", d: "Föreläsning om 60-talets Varberg" },
  { t: "16:00", n: "Elisabeth Wahlin", d: "100% livslust" }];


  const StageColumn = ({ title, rows, accent }) =>
  <div>
      <div style={{
      background: accent, color: "#fff",
      padding: "18px 24px", borderRadius: "var(--sm-radius-lg) var(--sm-radius-lg) 0 0",
      fontFamily: "var(--sm-font-display)", fontSize: 22, fontWeight: 800,
      letterSpacing: "0.04em", textTransform: "uppercase"
    }}>{title}</div>
      <div style={{
      background: "var(--sm-surface)",
      borderRadius: "0 0 var(--sm-radius-lg) var(--sm-radius-lg)",
      border: "1px solid var(--sm-line)",
      borderTop: "none",
      overflow: "hidden"
    }}>
        {rows.map((r, i) =>
      <div key={i} style={{
        display: "grid", gridTemplateColumns: "80px 1fr",
        padding: "18px 24px",
        borderTop: i === 0 ? "none" : "1px solid var(--sm-line-soft)",
        alignItems: "baseline", gap: 16
      }}>
            <div style={{ fontFamily: "var(--sm-font-display)", fontSize: 20, fontWeight: 700, color: accent }}>{r.t}</div>
            <div>
              <div style={{ fontSize: 18, fontWeight: 700, color: "var(--sm-ink)", lineHeight: 1.25 }}>{r.n}</div>
              <div style={{ fontSize: 15, color: "var(--sm-ink-soft)", marginTop: 4, lineHeight: 1.4 }}>{r.d}</div>
            </div>
          </div>
      )}
      </div>
    </div>;


  return (
    <>
      <PageHero eyebrow="Program" title="En dag, två scener, 18 programpunkter." body="Allt ingår i entrén — kom och gå som du vill." tone="primary" />
      <section className="sm-container" style={{ padding: "64px 32px 32px" }}>
        <div style={{ display: "flex", alignItems: "baseline", gap: 16, marginBottom: 8, flexWrap: "wrap" }}>
          <div className="sm-eyebrow" style={{ margin: 0 }}>Datum</div>
        </div>
        <h2 style={{ fontSize: 36, margin: "0 0 8px", color: "var(--sm-primary)" }}>Onsdag 24 februari</h2>
        <p style={{ fontSize: 17, color: "var(--sm-ink-soft)", margin: "0 0 40px" }}>
          Programmet uppdateras löpande inför mässan. Med reservation för ändringar.
        </p>

        <div style={{ display: "grid", gridTemplateColumns: "1fr 1fr", gap: 32 }} className="sm-program-grid">
          <StageColumn title="Stora Scen" rows={storaScen} accent="var(--sm-primary)" />
          <StageColumn title="Utställarscenen" rows={utstallarscenen} accent="var(--sm-accent)" />
        </div>
      </section>
    </>);

}

function InfoPage() {
  return (
    <>
      <PageHero eyebrow="Hitta hit" title="Arena Varberg — den stadsnära arenan." body="15 minuters promenad från stationen. Fri parkering med över 200 platser." tone="primary" />
      <section className="sm-container" style={{ padding: "48px 32px 96px" }}>
        <div style={{ display: "grid", gridTemplateColumns: "1.4fr 1fr", gap: 48 }}>
          <div className="sm-placeholder" style={{ height: 440 }}><span>KARTA<br />(embed: Google Maps / OpenStreetMap)</span></div>
          <div>
            <InfoBlock title="Adress" items={[["", "Engelbrektsgatan 6"], ["", "432 41 Varberg"]]} />
            <InfoBlock title="Fri parkering" items={[["", "Över 200 platser på området"], ["", "Kostnadsfritt under hela mässdagen"]]} />
            <InfoBlock title="Tillgänglighet" items={[["", "Ramp till huvudentré"], ["", "Tillgängliga toaletter på plan 1 & 2"], ["", "Ljudslinga i scenområdena"]]} />
          </div>
        </div>
      </section>
    </>);

}

function ContactPage() {
  return (
    <>
      <PageHero eyebrow="Kontakt" title="Prata med oss." body="Vi svarar inom ett arbetsdygn." tone="primary" />
      <section className="sm-container" style={{ padding: "48px 32px 96px" }}>
        <div style={{ display: "grid", gridTemplateColumns: "repeat(2, 1fr)", gap: 32 }}>
          {[
          { r: "Besökare & program", n: "Gästservice", e: "info@arenavarberg.se", p: "0340-690200" },
          { r: "Projektledare", n: "Camilla Bourdette", e: "camilla.bourdette@arenavarberg.se", p: "0340-690204" },
          { r: "Försäljning / Utställare", n: "Vivi Strindö", e: "vivi.strindo@arenavarberg.se", p: "0340-690213" },
          { r: "Försäljning / Utställare", n: "Susanne Carlsson", e: "susanne.carlsson@arenavarberg.se", p: "0340-690200" }].
          map((c) =>
          <div key={c.n} className="sm-card">
              <div style={{ fontSize: 13, fontWeight: 700, letterSpacing: "0.16em", textTransform: "uppercase", color: "var(--sm-accent)" }}>{c.r}</div>
              <div style={{ fontFamily: "var(--sm-font-display)", fontSize: 26, fontWeight: 700, marginTop: 10 }}>{c.n}</div>
              <div style={{ fontSize: 17, marginTop: 16, color: "var(--sm-ink-soft)" }}>{c.e}<br />{c.p}</div>
            </div>
          )}
        </div>
      </section>
    </>);

}

/* Hjälp-komponenter */
function PageHero({ eyebrow, title, body, tone = "primary", cta, onCta }) {
  const isAccent = tone === "accent";
  return (
    <section style={{
      background: isAccent ? "var(--sm-accent)" : "var(--sm-primary)",
      color: "#fff"
    }}>
      <div className="sm-container" style={{ padding: "80px 32px 80px", maxWidth: 900 }}>
        <div style={{ fontSize: 13, fontWeight: 700, letterSpacing: "0.22em", textTransform: "uppercase", opacity: 0.85 }}>{eyebrow}</div>
        <h1 style={{ fontSize: "var(--sm-fs-xl)", marginTop: 16, color: "#fff" }}>{title}</h1>
        {body && <p style={{ fontSize: "var(--sm-fs-lg)", opacity: 0.9, marginTop: 16, fontWeight: 300 }}>{body}</p>}
        {cta &&
        <button onClick={onCta} style={{
          marginTop: 32, background: "#fff", color: isAccent ? "var(--sm-accent)" : "var(--sm-primary)",
          border: "none", padding: "16px 28px", borderRadius: 999, fontWeight: 700, fontSize: 18
        }}>{cta}</button>
        }
      </div>
    </section>);

}

function InfoBlock({ title, items }) {
  return (
    <div style={{ marginBottom: 40 }}>
      <div style={{ fontSize: 13, fontWeight: 700, letterSpacing: "0.18em", textTransform: "uppercase", color: "var(--sm-accent)", marginBottom: 12 }}>{title}</div>
      {items.map(([k, v], i) =>
      <div key={i} style={{ display: "flex", justifyContent: "space-between", fontSize: 19, borderBottom: "1px solid var(--sm-line-soft)", padding: "12px 0" }}>
          <span style={{ fontWeight: 600 }}>{k}</span>
          <span style={{ color: "var(--sm-ink-soft)", textAlign: "right" }}>{v}</span>
        </div>
      )}
    </div>);

}

function CTABand({ title, body }) {
  return (
    <section style={{ background: "var(--sm-primary)", color: "#fff" }}>
      <div className="sm-container" style={{ padding: "64px 32px", textAlign: "center" }}>
        <h2 style={{ fontSize: "var(--sm-fs-xl)", color: "#fff" }}>{title}</h2>
        <p style={{ fontSize: "var(--sm-fs-lg)", opacity: 0.9, marginTop: 16, fontWeight: 300 }}>{body}</p>
      </div>
    </section>);

}

Object.assign(window, { VisitorPage, ExhibitorPage, ProgramPage, InfoPage, ContactPage, PageHero, InfoBlock, CTABand, WaveDivider, VimeoEmbed });