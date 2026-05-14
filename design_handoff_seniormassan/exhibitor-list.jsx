/* Seniormässan — Utställarlista
   Sökbar lista med bokstavsfilter och progressive disclosure.
   Datan är en placeholder — uppdatera EXHIBITORS-arrayen med faktiska utställare.
*/

const EXHIBITORS = [];

function ExhibitorList() {
  const [query, setQuery] = React.useState("");
  const [letter, setLetter] = React.useState(null);
  const [showAll, setShowAll] = React.useState(false);
  const [liveRegs, setLiveRegs] = React.useState([]);

  // Lyssna på registreringar i localStorage (från anmälningsformuläret)
  React.useEffect(() => {
    const load = () => {
      try {
        const raw = JSON.parse(localStorage.getItem("sm-registrations") || "[]");
        setLiveRegs(raw);
      } catch (e) { setLiveRegs([]); }
    };
    load();
    const onStorage = (e) => { if (e.key === "sm-registrations") load(); };
    window.addEventListener("storage", onStorage);
    // Refresh även när fliken kommer tillbaka i fokus
    window.addEventListener("focus", load);
    return () => {
      window.removeEventListener("storage", onStorage);
      window.removeEventListener("focus", load);
    };
  }, []);

  const sorted = React.useMemo(() => {
    // Konvertera live-registreringar till samma format som EXHIBITORS
    const live = liveRegs.map((r) => ({
      n: r.company || "",
      b: (r.booths || []).join(","),
      w: r.website || "",
      live: true,
    })).filter((x) => x.n);

    // Slå ihop: live-registreringar går först (samma företagsnamn ersätter statiska)
    const liveNames = new Set(live.map((x) => x.n.toLowerCase()));
    const staticOnly = EXHIBITORS.filter((x) => !liveNames.has(x.n.toLowerCase()));
    const merged = [...live, ...staticOnly];

    return merged.sort((a, b) => a.n.localeCompare(b.n, "sv"));
  }, [liveRegs]);

  const filtered = React.useMemo(() => {
    return sorted.filter((e) => {
      const matchesQuery = !query || e.n.toLowerCase().includes(query.toLowerCase());
      const matchesLetter = !letter || e.n.toUpperCase().startsWith(letter);
      return matchesQuery && matchesLetter;
    });
  }, [sorted, query, letter]);

  const initial = 24;
  const visible = (showAll || query || letter) ? filtered : filtered.slice(0, initial);

  // Available letters from data
  const letters = React.useMemo(() => {
    const set = new Set(sorted.map((e) => e.n[0].toUpperCase()));
    return ["A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z","Å","Ä","Ö"].filter((l) => set.has(l));
  }, [sorted]);

  return (
    <section style={{ background: "var(--sm-surface)" }}>
      <div className="sm-container" style={{ padding: "96px 32px" }}>
        <div style={{ textAlign: "center", marginBottom: 12 }}>
          <div className="sm-eyebrow">Utställare 2027</div>
          <h2 style={{ fontSize: "var(--sm-fs-xl)", marginTop: 8 }}>Utställarlistan</h2>
          <p style={{ color: "var(--sm-ink-soft)", fontSize: 18, marginTop: 8 }}>
            Listan uppdateras löpande
          </p>
        </div>

        {/* Sök */}
        <div style={{ maxWidth: 540, margin: "32px auto 16px", position: "relative" }}>
          <input
            type="search"
            value={query}
            onChange={(e) => { setQuery(e.target.value); setShowAll(true); }}
            placeholder="Sök utställare…"
            style={{
              width: "100%",
              padding: "16px 20px 16px 52px",
              fontSize: 17,
              border: "1px solid var(--sm-line)",
              borderRadius: 999,
              background: "#fff",
              fontFamily: "inherit",
            }}
          />
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"
            style={{ position: "absolute", left: 20, top: "50%", transform: "translateY(-50%)", color: "var(--sm-muted)" }}>
            <circle cx="11" cy="11" r="7" />
            <path d="M21 21l-4.3-4.3" strokeLinecap="round" />
          </svg>
        </div>

        {/* Bokstavsfilter */}
        <div style={{ display: "flex", flexWrap: "wrap", justifyContent: "center", gap: 6, marginBottom: 32 }}>
          <button onClick={() => { setLetter(null); setShowAll(false); }}
            style={{
              padding: "8px 14px", borderRadius: 8, fontSize: 14, fontWeight: 700,
              background: letter === null ? "var(--sm-primary)" : "#fff",
              color: letter === null ? "#fff" : "var(--sm-ink)",
              border: "1px solid var(--sm-line)",
              cursor: "pointer",
            }}>Alla</button>
          {letters.map((l) => (
            <button key={l} onClick={() => { setLetter(l); setShowAll(true); }}
              style={{
                padding: "8px 12px", borderRadius: 8, fontSize: 14, fontWeight: 700,
                background: letter === l ? "var(--sm-primary)" : "#fff",
                color: letter === l ? "#fff" : "var(--sm-ink)",
                border: "1px solid var(--sm-line)",
                cursor: "pointer",
                minWidth: 36,
              }}>{l}</button>
          ))}
        </div>

        {/* Resultat-count */}
        <div style={{ textAlign: "center", color: "var(--sm-muted)", fontSize: 14, marginBottom: 24 }}>
          Visar {visible.length} av {filtered.length} utställare
        </div>

        {/* Grid */}
        {filtered.length === 0 ? (
          <div style={{ textAlign: "center", padding: "48px 0", color: "var(--sm-muted)" }}>
            Inga utställare matchar din sökning.
          </div>
        ) : (
          <div style={{ display: "grid", gridTemplateColumns: "repeat(3, 1fr)", gap: 16 }}>
            {visible.map((e, i) => (
              <div key={i} style={{
                background: "var(--sm-bg)",
                border: "1px solid var(--sm-line-soft)",
                borderRadius: 12,
                padding: "20px 22px",
                display: "flex",
                flexDirection: "column",
                alignItems: "center",
                textAlign: "center",
                gap: 8,
              }}>
                <div style={{ fontSize: 16, fontWeight: 700, lineHeight: 1.3 }}>{e.n}</div>
                <div style={{ display: "flex", gap: 14, alignItems: "center", fontSize: 14, color: "var(--sm-ink-soft)", flexWrap: "wrap", justifyContent: "center" }}>
                  <span style={{ display: "inline-flex", alignItems: "center", gap: 4 }}>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="var(--sm-accent)">
                      <path d="M12 2C8 2 5 5 5 9c0 5 7 13 7 13s7-8 7-13c0-4-3-7-7-7zm0 9.5a2.5 2.5 0 110-5 2.5 2.5 0 010 5z" />
                    </svg>
                    {e.b}
                  </span>
                  {e.w && (
                    <a href={/^https?:\/\//i.test(e.w) ? e.w : `https://${e.w.replace(/^\/+/, "")}`} target="_blank" rel="noopener noreferrer"
                      style={{ display: "inline-flex", alignItems: "center", gap: 4, color: "var(--sm-primary)", textDecoration: "none" }}>
                      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                        <path d="M10 13a5 5 0 007 0l3-3a5 5 0 00-7-7l-1 1" strokeLinecap="round" />
                        <path d="M14 11a5 5 0 00-7 0l-3 3a5 5 0 007 7l1-1" strokeLinecap="round" />
                      </svg>
                      Hemsida
                    </a>
                  )}
                </div>
              </div>
            ))}
          </div>
        )}

        {/* Visa fler / mindre */}
        {!showAll && !query && !letter && filtered.length > initial && (
          <div style={{ textAlign: "center", marginTop: 32 }}>
            <button onClick={() => setShowAll(true)} className="sm-btn sm-btn--ghost">
              Visa alla {filtered.length} utställare →
            </button>
          </div>
        )}
        {showAll && !query && !letter && (
          <div style={{ textAlign: "center", marginTop: 32 }}>
            <button onClick={() => setShowAll(false)} className="sm-btn sm-btn--ghost">
              Visa färre
            </button>
          </div>
        )}
      </div>
    </section>
  );
}

window.ExhibitorList = ExhibitorList;
