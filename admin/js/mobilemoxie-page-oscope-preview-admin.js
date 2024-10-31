(function( $ ) {
	'use strict';

	// console.log('mobilemoxie_page_oscope_preview_script_vars',mobilemoxie_page_oscope_preview_script_vars);

	if( mobilemoxie_page_oscope_preview_script_vars.isGutenberg === '1' ) {
		var el = wp.element.createElement;
		var __ = wp.i18n.__;
		var registerPlugin = wp.plugins.registerPlugin;
		var PluginPostStatusInfo = wp.editPost.PluginPostStatusInfo;
		var ExternalLink = wp.components.ExternalLink;

		function MobileMoxiePageOscopePreviewStatusInfo({}) {
			return el(
				PluginPostStatusInfo,
				{
					className: 'mobilemoxie-page-oscope-preview-info'
				},
				el('img', {
					src: mobilemoxie_page_oscope_preview_script_vars.pageoscopeIconUrl,
					width:30
				}),
				el(
					ExternalLink,
					{
						id: 'mobilemoxie-page-oscope-preview-external-link',
						href: mobilemoxie_page_oscope_preview_script_vars.mobilemoxieExternalToolPageoscopeUrl,
						title: __('Preview this page on the Page-oscope by MobileMoxie')
					}, 'Page-oscope Preview'
				),
				el('a', {
					href: mobilemoxie_page_oscope_preview_script_vars.pluginSettingsUrl,
					title:'About the Page-oscope Preview plugin'
				},'[ About ]')
			);
		}

		registerPlugin('mobilemoxie-page-oscope-preview-info-plugin', {
			render: MobileMoxiePageOscopePreviewStatusInfo
		});
	}

})( jQuery );
