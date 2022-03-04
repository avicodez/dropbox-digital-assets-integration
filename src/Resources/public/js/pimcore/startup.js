pimcore.registerNS("pimcore.plugin.DropboxDigitalAssetsIntegrationBundle");

pimcore.plugin.DropboxDigitalAssetsIntegrationBundle = Class.create(pimcore.plugin.admin, {
    getClassName: function () {
        return "pimcore.plugin.DropboxDigitalAssetsIntegrationBundle";
    },

    initialize: function () {
        pimcore.plugin.broker.registerPlugin(this);

        this.navEl = Ext.get('pimcore_menu_search').insertSibling('<li id="pimcore_menu_import" data-menu-tooltip="'
            + t('plugin_twohats_dropbox_mainmenu') +
            '" class="pimcore_menu_item pimcore_menu_needs_children"><img src="/bundles/dropboxdigitalassetsintegration/icons/import.svg"></li>', 'before');
        this.menu = new Ext.menu.Menu({cls: 'pimcore_navigation_flyout'});

        pimcore.layout.toolbar.prototype.importMenu = this.menu;
    },

    pimcoreReady: function (params, broker) {
        this.initToolbar();
    },

    initToolbar: function () {
        var toolbar = pimcore.globalmanager.get('layout_toolbar');
        var user = pimcore.globalmanager.get('user');

        // settings view
        var settingsViewPanelId = 'plugin_twohats_dropbox_settingsview';
        var menuOptions = null;


        var settingsMenu = Ext.create('Ext.menu.Item', {
            text: t('plugin_twohats_dropbox_settingsview'),
            iconCls: 'plugin_twohats_dropbox_settings',
            hideOnClick: false,
            menu: menuOptions,
            handler: function () {
                try {
                    pimcore.globalmanager.get(settingsViewPanelId).activate();
                }
                catch (e) {
                    pimcore.globalmanager.add(
                        settingsViewPanelId,
                        new pimcore.tool.genericiframewindow(
                            settingsViewPanelId,
                            'dropbox_digital_assets_integration/settings',
                            'plugin_twohats_dropbox_settings',
                            t('plugin_twohats_dropbox_settingsview')
                        )
                    );
                }
            }
        });

        // add to menu
        this.menu.add(settingsMenu);
        

        this.navEl.on('mousedown', toolbar.showSubMenu.bind(toolbar.importMenu));
        pimcore.plugin.broker.fireEvent("importMenuReady", toolbar.importMenu);
    }
});

var DropboxDigitalAssetsIntegrationBundlePlugin = new pimcore.plugin.DropboxDigitalAssetsIntegrationBundle();
