<?php
/**
 * Utställarlista — fylls på automatiskt från sm_registration-posts.
 *
 * Lägg in via get_template_part( 'template-parts/exhibitor-list' ) på de sidor
 * där listan ska visas (för närvarande front-page.php).
 *
 * Endast publicerade anmälningar med status != 'cancelled' visas. Endast
 * företagsnamn, monter(ar) och webbplats exponeras (ingen PII).
 */

if ( ! defined( 'ABSPATH' ) ) {
	return;
}

$query = new WP_Query( array(
	'post_type'      => 'sm_registration',
	'post_status'    => 'publish',
	'posts_per_page' => -1,
	'orderby'        => 'meta_value',
	'meta_key'       => '_sm_company',
	'order'          => 'ASC',
	'meta_query'     => array(
		array(
			'key'     => '_sm_status',
			'value'   => 'cancelled',
			'compare' => '!=',
		),
	),
) );

$exhibitors = array();
foreach ( $query->posts as $post ) {
	$company = get_post_meta( $post->ID, '_sm_company', true );
	if ( ! $company ) {
		continue;
	}
	$booths      = get_post_meta( $post->ID, '_sm_booths', true ) ?: array();
	$website     = get_post_meta( $post->ID, '_sm_website', true );
	$no_website  = get_post_meta( $post->ID, '_sm_no_website', true );
	$exhibitors[] = array(
		'n' => $company,
		'b' => is_array( $booths ) ? implode( ', ', $booths ) : '',
		'w' => $no_website ? '' : (string) $website,
	);
}

// Sortera svenskt (åäö sist)
usort( $exhibitors, function ( $a, $b ) {
	return strcoll( mb_strtolower( $a['n'] ), mb_strtolower( $b['n'] ) );
} );
?>

<section style="background: var(--sm-surface);">
	<div class="sm-container" style="padding: 96px 32px;">
		<div style="text-align: center; margin-bottom: 12px;">
			<div class="sm-eyebrow">Utställare 2027</div>
			<h2 style="font-size: var(--sm-fs-xl); margin-top: 8px;">Utställarlistan</h2>
			<p style="color: var(--sm-ink-soft); font-size: 18px; margin-top: 8px;">
				Listan uppdateras löpande
			</p>
		</div>

		<?php if ( empty( $exhibitors ) ) : ?>
			<div style="max-width: 540px; margin: 32px auto 0; padding: 32px 24px; background: var(--sm-bg); border: 1px dashed var(--sm-line); border-radius: var(--sm-radius-lg); text-align: center; color: var(--sm-ink-soft);">
				Anmälan är öppen — listan fylls på när utställare börjar boka. Vill du synas här? <a href="<?php echo esc_url( home_url( '/anmalan/' ) ); ?>" style="color: var(--sm-primary); font-weight: 700;">Anmäl din monter →</a>
			</div>
		<?php else : ?>

			<!-- Sökruta -->
			<div style="max-width: 540px; margin: 32px auto 16px; position: relative;">
				<input type="search" id="sm-ex-search" placeholder="Sök utställare…" style="width: 100%; padding: 16px 20px 16px 52px; font-size: 17px; border: 1px solid var(--sm-line); border-radius: 999px; background: #fff; font-family: inherit;">
				<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="position: absolute; left: 20px; top: 50%; transform: translateY(-50%); color: var(--sm-muted);">
					<circle cx="11" cy="11" r="7"></circle>
					<path d="M21 21l-4.3-4.3" stroke-linecap="round"></path>
				</svg>
			</div>

			<!-- Bokstavsfilter -->
			<div id="sm-ex-letters" style="display: flex; flex-wrap: wrap; justify-content: center; gap: 6px; margin-bottom: 32px;">
				<button type="button" class="sm-ex-letter is-active" data-letter="" style="padding: 8px 14px; border-radius: 8px; font-size: 14px; font-weight: 700; border: 1px solid var(--sm-line); cursor: pointer; font-family: inherit; min-width: 36px;">Alla</button>
				<?php
				$letters_set = array();
				foreach ( $exhibitors as $e ) {
					$letters_set[ mb_strtoupper( mb_substr( $e['n'], 0, 1 ) ) ] = true;
				}
				$alphabet = array( 'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','Å','Ä','Ö' );
				foreach ( $alphabet as $l ) :
					if ( ! isset( $letters_set[ $l ] ) ) continue;
					?>
					<button type="button" class="sm-ex-letter" data-letter="<?php echo esc_attr( $l ); ?>" style="padding: 8px 12px; border-radius: 8px; font-size: 14px; font-weight: 700; border: 1px solid var(--sm-line); background: #fff; color: var(--sm-ink); cursor: pointer; font-family: inherit; min-width: 36px;"><?php echo esc_html( $l ); ?></button>
				<?php endforeach; ?>
			</div>

			<!-- Resultat-count -->
			<div id="sm-ex-count" style="text-align: center; color: var(--sm-muted); font-size: 14px; margin-bottom: 24px;">
				Visar <span id="sm-ex-shown">0</span> av <span id="sm-ex-total"><?php echo count( $exhibitors ); ?></span> utställare
			</div>

			<!-- Grid -->
			<div id="sm-ex-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px;">
				<?php foreach ( $exhibitors as $i => $e ) :
					$letter = mb_strtoupper( mb_substr( $e['n'], 0, 1 ) );
					$href   = '';
					if ( $e['w'] ) {
						$href = preg_match( '#^https?://#i', $e['w'] ) ? $e['w'] : 'https://' . ltrim( $e['w'], '/' );
					}
					?>
					<div class="sm-ex-card" data-name="<?php echo esc_attr( mb_strtolower( $e['n'] ) ); ?>" data-letter="<?php echo esc_attr( $letter ); ?>" data-index="<?php echo (int) $i; ?>" style="background: var(--sm-bg); border: 1px solid var(--sm-line-soft); border-radius: 12px; padding: 20px 22px; display: flex; flex-direction: column; align-items: center; text-align: center; gap: 8px;">
						<div style="font-size: 16px; font-weight: 700; line-height: 1.3;"><?php echo esc_html( $e['n'] ); ?></div>
						<div style="display: flex; gap: 14px; align-items: center; font-size: 14px; color: var(--sm-ink-soft); flex-wrap: wrap; justify-content: center;">
							<?php if ( $e['b'] ) : ?>
								<span style="display: inline-flex; align-items: center; gap: 4px;">
									<svg width="14" height="14" viewBox="0 0 24 24" fill="var(--sm-accent)">
										<path d="M12 2C8 2 5 5 5 9c0 5 7 13 7 13s7-8 7-13c0-4-3-7-7-7zm0 9.5a2.5 2.5 0 110-5 2.5 2.5 0 010 5z"/>
									</svg>
									<?php echo esc_html( $e['b'] ); ?>
								</span>
							<?php endif; ?>
							<?php if ( $href ) : ?>
								<a href="<?php echo esc_url( $href ); ?>" target="_blank" rel="noopener noreferrer" style="display: inline-flex; align-items: center; gap: 4px; color: var(--sm-primary); text-decoration: none;">
									<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
										<path d="M10 13a5 5 0 007 0l3-3a5 5 0 00-7-7l-1 1" stroke-linecap="round"/>
										<path d="M14 11a5 5 0 00-7 0l-3 3a5 5 0 007 7l1-1" stroke-linecap="round"/>
									</svg>
									Hemsida
								</a>
							<?php endif; ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>

			<div id="sm-ex-empty" style="display: none; text-align: center; padding: 48px 0; color: var(--sm-muted);">
				Inga utställare matchar din sökning.
			</div>

			<div style="text-align: center; margin-top: 32px;">
				<button type="button" id="sm-ex-toggle" class="sm-btn sm-btn--ghost" style="display: none;">Visa alla <?php echo count( $exhibitors ); ?> utställare →</button>
			</div>

			<script>
			(function () {
				var INITIAL = 24;
				var cards   = Array.prototype.slice.call(document.querySelectorAll('.sm-ex-card'));
				var search  = document.getElementById('sm-ex-search');
				var letters = document.querySelectorAll('.sm-ex-letter');
				var grid    = document.getElementById('sm-ex-grid');
				var emptyEl = document.getElementById('sm-ex-empty');
				var toggle  = document.getElementById('sm-ex-toggle');
				var shown   = document.getElementById('sm-ex-shown');
				var query   = '';
				var letter  = '';
				var showAll = false;

				function activeLetter(btn) {
					letters.forEach(function (b) {
						var isActive = b === btn;
						b.classList.toggle('is-active', isActive);
						b.style.background = isActive ? 'var(--sm-primary)' : '#fff';
						b.style.color      = isActive ? '#fff' : 'var(--sm-ink)';
					});
				}

				function render() {
					var matching = cards.filter(function (c) {
						var nameMatch   = !query  || c.dataset.name.indexOf(query) !== -1;
						var letterMatch = !letter || c.dataset.letter === letter;
						return nameMatch && letterMatch;
					});

					var visible = (showAll || query || letter) ? matching : matching.slice(0, INITIAL);
					var visibleSet = new Set(visible);

					cards.forEach(function (c) {
						c.style.display = visibleSet.has(c) ? '' : 'none';
					});

					emptyEl.style.display = matching.length === 0 ? 'block' : 'none';
					grid.style.display    = matching.length === 0 ? 'none' : 'grid';
					shown.textContent     = visible.length;

					if (!showAll && !query && !letter && matching.length > INITIAL) {
						toggle.style.display = 'inline-flex';
						toggle.textContent   = 'Visa alla ' + matching.length + ' utställare →';
					} else if (showAll && !query && !letter && matching.length > INITIAL) {
						toggle.style.display = 'inline-flex';
						toggle.textContent   = 'Visa färre';
					} else {
						toggle.style.display = 'none';
					}
				}

				search.addEventListener('input', function (e) {
					query   = e.target.value.toLowerCase().trim();
					showAll = true;
					render();
				});

				letters.forEach(function (btn) {
					btn.addEventListener('click', function () {
						letter  = btn.dataset.letter;
						showAll = !!letter;
						activeLetter(btn);
						render();
					});
				});

				toggle.addEventListener('click', function () {
					showAll = !showAll;
					render();
				});

				// Initiera aktiv "Alla"-knapp
				activeLetter(document.querySelector('.sm-ex-letter[data-letter=""]'));
				render();
			})();
			</script>
		<?php endif; ?>
	</div>
</section>
