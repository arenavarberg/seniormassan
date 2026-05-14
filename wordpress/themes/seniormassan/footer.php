<?php
/**
 * Footer template.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
?>
</main>

<footer class="sm-footer">
    <div class="sm-container sm-footer__grid">
        <div class="sm-footer__brand">
            <div class="sm-footer__brand-mark">
                <img
                    class="sm-footer__logo"
                    src="<?php echo esc_url( SENIORMASSAN_THEME_URI . '/assets/images/senior-logo-2026-transparent.png' ); ?>"
                    alt="Senior"
                    width="200" height="56">
                <span class="sm-footer__wordmark" aria-hidden="true">
                    <span class="sm-footer__rule"></span>
                    <span class="sm-footer__wordmark-text">MÄSSAN</span>
                    <span class="sm-footer__rule"></span>
                </span>
            </div>
            <div class="sm-footer__since">Sedan 2013</div>
            <p class="sm-footer__about">En mötesplats för seniorer i Halland — arrangerad av Arena Varberg.</p>
            <div class="sm-footer__organizer">
                <span class="sm-footer__organizer-label">Arrangör</span>
                <img
                    src="<?php echo esc_url( SENIORMASSAN_THEME_URI . '/assets/images/arena-varberg-logo-white.png' ); ?>"
                    alt="Arena Varberg"
                    width="180" height="56">
            </div>
        </div>

        <div class="sm-footer__col">
            <h2 class="sm-footer__heading">Mässan</h2>
            <ul class="sm-footer__list">
                <li><a href="<?php echo esc_url( home_url( '/program/' ) ); ?>">Program</a></li>
                <li><a href="<?php echo esc_url( home_url( '/hitta-hit/' ) ); ?>">Hitta hit</a></li>
                <li><a href="<?php echo esc_url( home_url( '/hitta-hit/#parkering' ) ); ?>">Parkering</a></li>
                <li><a href="<?php echo esc_url( home_url( '/hitta-hit/#tillganglighet' ) ); ?>">Tillgänglighet</a></li>
            </ul>
        </div>

        <div class="sm-footer__col">
            <h2 class="sm-footer__heading">Utställare</h2>
            <ul class="sm-footer__list">
                <li><a href="<?php echo esc_url( home_url( '/for-utstallare/' ) ); ?>">Bli utställare</a></li>
                <li><a href="<?php echo esc_url( home_url( '/for-utstallare/#priser' ) ); ?>">Priser & paket</a></li>
                <li><a href="<?php echo esc_url( home_url( '/anmalan/' ) ); ?>">Anmäl monter</a></li>
                <li><a href="<?php echo esc_url( home_url( '/for-utstallare/#villkor' ) ); ?>">Villkor</a></li>
            </ul>
        </div>

        <div class="sm-footer__col">
            <h2 class="sm-footer__heading">Kontakt</h2>
            <address class="sm-footer__address">
                Arena Varberg<br>
                <a href="tel:+4634069020">0340-69 02 00</a><br>
                <a href="mailto:info@arenavarberg.se">info@arenavarberg.se</a>
            </address>
        </div>
    </div>
    <div class="sm-footer__legal">
        © <?php echo esc_html( date( 'Y' ) ); ?> Arena Varberg AB
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
