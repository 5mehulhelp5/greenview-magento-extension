/**
 * Last Sync Component
 */
define([
    'uiComponent',
    'jquery',
    'mage/url'
], function (Component, $, urlBuilder) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'GreenView_Viewer/grid/last-sync',
            lastSyncTime: null,
            lastSyncFormatted: '',
            updateInterval: 60000 // Update every minute
        },

        /**
         * Initialize component
         */
        initialize: function () {
            this._super();
            this.loadLastSyncTime();
            this.startAutoUpdate();
            return this;
        },

        /**
         * Load last sync time from server
         */
        loadLastSyncTime: function () {
            var self = this;

            $.ajax({
                url: urlBuilder.build('greenview/sync/lasttime'),
                type: 'GET',
                dataType: 'json',
                showLoader: false,
                success: function (response) {
                    if (response.timestamp) {
                        self.lastSyncTime = response.timestamp;
                        self.updateFormattedTime();
                    }
                }
            });
        },

        /**
         * Update formatted time string
         */
        updateFormattedTime: function () {
            if (!this.lastSyncTime) {
                this.lastSyncFormatted = 'Never';
                return;
            }

            var now = Math.floor(Date.now() / 1000);
            var diff = now - this.lastSyncTime;

            if (diff < 60) {
                this.lastSyncFormatted = 'Just now';
            } else if (diff < 3600) {
                var minutes = Math.floor(diff / 60);
                this.lastSyncFormatted = minutes + (minutes === 1 ? ' minute ago' : ' minutes ago');
            } else if (diff < 86400) {
                var hours = Math.floor(diff / 3600);
                this.lastSyncFormatted = hours + (hours === 1 ? ' hour ago' : ' hours ago');
            } else {
                var days = Math.floor(diff / 86400);
                this.lastSyncFormatted = days + (days === 1 ? ' day ago' : ' days ago');
            }
        },

        /**
         * Start auto-update timer
         */
        startAutoUpdate: function () {
            var self = this;

            setInterval(function () {
                self.updateFormattedTime();
            }, this.updateInterval);
        }
    });
});
