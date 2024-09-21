(function($) {
    'use strict';

    class FloatingContacts {
        constructor() {
            this.container = $('.FloatingContacts');
            this.button = this.container.find('.FloatingContacts__button');
            this.list = this.container.find('.FloatingContacts__list');
            this.listItems = this.list.find('.FloatingContacts__list-item');
            this.isMobile = 'ontouchstart' in window || navigator.maxTouchPoints > 0 || navigator.msMaxTouchPoints > 0;

            this.init();
        }

        init() {
            this.container.attr('data-device', this.isMobile ? 'mobile' : 'desktop');
            this.setCustomColors();
            this.bindEvents();
            this.container.addClass('FloatingContacts_loaded');
        }

        setCustomColors() {
            const bgColor = this.container.data('bg-color');
            if (bgColor) {
                this.container.css('--fc-bg-color', bgColor);
                this.container.css('--fc-hover-color', this.adjustBrightness(bgColor, 20));
            }
        }

        bindEvents() {
            if (this.isMobile) {
                $(document).on('touchstart', this.handleTouchStart.bind(this));
            } else {
                this.button.on('mouseenter', this.openDialog.bind(this));
                this.container.on('mouseleave', this.closeDialog.bind(this));
            }

            this.listItems.on('click', this.handleItemClick.bind(this));
        }

        handleTouchStart(event) {
            const target = $(event.target);
            if (target.closest('.FloatingContacts__button').length && !this.container.hasClass('show')) {
                this.openDialog();
            } else if (!target.closest('.FloatingContacts__list').length) {
                this.closeDialog();
            }
        }

        openDialog() {
            this.container.addClass('show');
            this.button.addClass('FloatingContacts__button_dialog-open');
        }

        closeDialog() {
            this.container.removeClass('show');
            this.button.removeClass('FloatingContacts__button_dialog-open');
        }

        handleItemClick(event) {
            const item = $(event.currentTarget);
            const icon = item.data('icon');
            this.closeDialog();
        }

        adjustBrightness(hex, percent) {
            hex = hex.replace(/^\s*#|\s*$/g, '');
            if(hex.length == 3) {
                hex = hex.replace(/(.)/g, '$1$1');
            }
            var r = parseInt(hex.substr(0, 2), 16),
                g = parseInt(hex.substr(2, 2), 16),
                b = parseInt(hex.substr(4, 2), 16);
            return '#' +
                ((0|(1<<8) + r + (256 - r) * percent / 100).toString(16)).substr(1) +
                ((0|(1<<8) + g + (256 - g) * percent / 100).toString(16)).substr(1) +
                ((0|(1<<8) + b + (256 - b) * percent / 100).toString(16)).substr(1);
        }
    }

    $(document).ready(() => {
        try {
            new FloatingContacts();
        } catch (error) {
            console.error('Error initializing Floating Contacts:', error);
        }
    });

})(jQuery);