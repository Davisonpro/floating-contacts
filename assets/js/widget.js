(function ($) {
    'use strict';

    window.FC = window.FC || {};

    FC.FloatingContacts = {
        container: null,
        button: null,
        list: null,
        listItems: null,
        isMobile: false,

        /**
         * Initialize the Floating Contacts module
         */
        init() {
            this.cacheDOM();
            this.detectDevice();
            this.setCustomColors();
            this.bindEvents();
            this.container.addClass('FloatingContacts_loaded');
        },

        /**
         * Cache DOM elements for better performance
         */
        cacheDOM() {
            this.container = $('.FloatingContacts');
            this.button = this.container.find('.FloatingContacts__button');
            this.list = this.container.find('.FloatingContacts__list');
            this.listItems = this.list.find('.FloatingContacts__list-item');
        },

        /**
         * Detect if the device is mobile
         */
        detectDevice() {
            this.isMobile = 'ontouchstart' in window || navigator.maxTouchPoints > 0 || navigator.msMaxTouchPoints > 0;
            this.container.attr('data-device', this.isMobile ? 'mobile' : 'desktop');
        },

        /**
         * Set custom colors based on data attributes
         */
        setCustomColors() {
            const bgColor = this.container.data('bg-color');
            if (bgColor) {
                this.container.css('--fc-bg-color', bgColor);
                this.container.css('--fc-hover-color', this.adjustBrightness(bgColor, 20));
            }
        },

        /**
         * Bind event listeners
         */
        bindEvents() {
            if (this.isMobile) {
                $(document).on('touchstart', this.handleTouchStart.bind(this));
            } else {
                this.button.on('mouseenter', this.openDialog.bind(this));
                this.container.on('mouseleave', this.closeDialog.bind(this));
            }

            this.listItems.on('click', this.handleItemClick.bind(this));
        },

        /**
         * Handle touch start events on mobile devices
         * @param {Event} event - The touch event
         */
        handleTouchStart(event) {
            const target = $(event.target);
            if (target.closest('.FloatingContacts__button').length && !this.container.hasClass('show')) {
                this.openDialog();
            } else if (!target.closest('.FloatingContacts__list').length) {
                this.closeDialog();
            }
        },

        /**
         * Open the contacts dialog
         */
        openDialog() {
            this.container.addClass('show');
            this.button.addClass('FloatingContacts__button_dialog-open');
        },

        /**
         * Close the contacts dialog
         */
        closeDialog() {
            this.container.removeClass('show');
            this.button.removeClass('FloatingContacts__button_dialog-open');
        },

        /**
         * Handle click events on list items
         * @param {Event} event - The click event
         */
        handleItemClick(event) {
            const item = $(event.currentTarget);
            const icon = item.data('icon');
            // TODO: Implement specific actions for each icon type
            this.closeDialog();
        },

        /**
         * Adjust the brightness of a hex color
         * @param {string} hex - The hex color to adjust
         * @param {number} percent - The percentage to adjust by
         * @returns {string} The adjusted hex color
         */
        adjustBrightness(hex, percent) {
            // Remove # if present
            hex = hex.replace(/^\s*#|\s*$/g, '');

            // Convert 3-digit hex to 6-digits.
            if (hex.length === 3) {
                hex = hex.replace(/(.)/g, '$1$1');
            }

            const r = parseInt(hex.substr(0, 2), 16);
            const g = parseInt(hex.substr(2, 2), 16);
            const b = parseInt(hex.substr(4, 2), 16);

            const calculateAdjustment = (value) => {
                return Math.round(Math.min(Math.max(0, value + (value * percent / 100)), 255)).toString(16).padStart(2, '0');
            };

            return `#${calculateAdjustment(r)}${calculateAdjustment(g)}${calculateAdjustment(b)}`;
        }
    };

    $(document).ready(() => {
        FC.FloatingContacts.init();
    });

})(jQuery);