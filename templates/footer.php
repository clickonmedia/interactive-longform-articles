<?php
/**
 * Default footer template
 */
?>

<!-- Load React / development
<script src="https://unpkg.com/react@16/umd/react.development.js" crossorigin></script>
<script src="https://unpkg.com/react-dom@16/umd/react-dom.development.js" crossorigin></script>
-->

<!-- Load React / production -->
<script src="https://unpkg.com/react@16/umd/react.production.min.js" crossorigin></script>
<script src="https://unpkg.com/react-dom@16.12.0/umd/react-dom.production.min.js" crossorigin></script>

<!-- Load our React component. -->
<script src="<?php echo plugin_dir_url( dirname(__DIR__, 1) . '/interactive-longform-articles.php' ) . 'react/like_button.js' ?>"></script>


<?php wp_footer(); ?>

</body>
</html>
