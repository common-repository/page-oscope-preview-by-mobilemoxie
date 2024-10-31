<div id="mobilemoxie-page-oscope-preview-publishbox-section">
    <div class="misc-pub-section">
        <img src="<?php echo esc_url( $pageoscopeIconUrl ) ?>" style="vertical-align: middle" width="30"/>
        <?php if ( $isPublished ) { ?>
            <a href="<?php echo esc_url( $mobilemoxieExternalToolPageoscopeUrl ) ?>" target="_blank" title="Preview this page on the Page-oscope by MobileMoxie">Page-oscope Preview</a>
        <?php } else { ?>
            <a href="<?php echo esc_url( $mobilemoxieExternalToolPageoscopeUrl ) ?>" target="_blank" title="Preview this unpublished page on the Page-oscope by MobileMoxie">Page-oscope Preview</a>
        <?php } ?>
        [ <a href="<?php echo esc_url( $pluginSettingsUrl ) ?>" title="About the Page-oscope Preview plugin">About</a> ]
        <?php if ( !$isPublished ) { ?>
            <br />(Note that the preview link will only work for 24 hours, since the page is not currently published.)
        <?php } ?>
    </div>
</div>