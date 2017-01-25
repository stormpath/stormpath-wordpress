/*
 * Stormpath WordPress is a WordPress plugin to authenticate against a Stormpath Directory.
 * Copyright (C) 2016  Stormpath
 *
 * This file is part of Stormpath WordPress.
 *
 * Stormpath WordPress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * Stormpath WordPress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Foobar.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package Stormpath\WordPress;
 */


/*! ========================================================================
 * Bootstrap Toggle: bootstrap-toggle.js v2.2.0
 * http://www.bootstraptoggle.com
 * ========================================================================
 * Copyright 2014 Min Hur, The New York Times Company
 * Licensed under MIT
 * ======================================================================== */
+function(a){"use strict";function b(b){return this.each(function(){var d=a(this),e=d.data("bs.toggle"),f="object"==typeof b&&b;e||d.data("bs.toggle",e=new c(this,f)),"string"==typeof b&&e[b]&&e[b]()})}var c=function(b,c){this.$element=a(b),this.options=a.extend({},this.defaults(),c),this.render()};c.VERSION="2.2.0",c.DEFAULTS={on:"On",off:"Off",onstyle:"primary",offstyle:"default",size:"normal",style:"",width:null,height:null},c.prototype.defaults=function(){return{on:this.$element.attr("data-on")||c.DEFAULTS.on,off:this.$element.attr("data-off")||c.DEFAULTS.off,onstyle:this.$element.attr("data-onstyle")||c.DEFAULTS.onstyle,offstyle:this.$element.attr("data-offstyle")||c.DEFAULTS.offstyle,size:this.$element.attr("data-size")||c.DEFAULTS.size,style:this.$element.attr("data-style")||c.DEFAULTS.style,width:this.$element.attr("data-width")||c.DEFAULTS.width,height:this.$element.attr("data-height")||c.DEFAULTS.height}},c.prototype.render=function(){this._onstyle="btn-"+this.options.onstyle,this._offstyle="btn-"+this.options.offstyle;var b="large"===this.options.size?"btn-lg":"small"===this.options.size?"btn-sm":"mini"===this.options.size?"btn-xs":"",c=a('<label class="btn">').html(this.options.on).addClass(this._onstyle+" "+b),d=a('<label class="btn">').html(this.options.off).addClass(this._offstyle+" "+b+" active"),e=a('<span class="toggle-handle btn btn-default">').addClass(b),f=a('<div class="toggle-group">').append(c,d,e),g=a('<div class="toggle btn" data-toggle="toggle">').addClass(this.$element.prop("checked")?this._onstyle:this._offstyle+" off").addClass(b).addClass(this.options.style);this.$element.wrap(g),a.extend(this,{$toggle:this.$element.parent(),$toggleOn:c,$toggleOff:d,$toggleGroup:f}),this.$toggle.append(f);var h=this.options.width||Math.max(c.outerWidth(),d.outerWidth())+e.outerWidth()/2,i=this.options.height||Math.max(c.outerHeight(),d.outerHeight());c.addClass("toggle-on"),d.addClass("toggle-off"),this.$toggle.css({width:h,height:i}),this.options.height&&(c.css("line-height",c.height()+"px"),d.css("line-height",d.height()+"px")),this.update(!0),this.trigger(!0)},c.prototype.toggle=function(){this.$element.prop("checked")?this.off():this.on()},c.prototype.on=function(a){return this.$element.prop("disabled")?!1:(this.$toggle.removeClass(this._offstyle+" off").addClass(this._onstyle),this.$element.prop("checked",!0),void(a||this.trigger()))},c.prototype.off=function(a){return this.$element.prop("disabled")?!1:(this.$toggle.removeClass(this._onstyle).addClass(this._offstyle+" off"),this.$element.prop("checked",!1),void(a||this.trigger()))},c.prototype.enable=function(){this.$toggle.removeAttr("disabled"),this.$element.prop("disabled",!1)},c.prototype.disable=function(){this.$toggle.attr("disabled","disabled"),this.$element.prop("disabled",!0)},c.prototype.update=function(a){this.$element.prop("disabled")?this.disable():this.enable(),this.$element.prop("checked")?this.on(a):this.off(a)},c.prototype.trigger=function(b){this.$element.off("change.bs.toggle"),b||this.$element.change(),this.$element.on("change.bs.toggle",a.proxy(function(){this.update()},this))},c.prototype.destroy=function(){this.$element.off("change.bs.toggle"),this.$toggleGroup.remove(),this.$element.removeData("bs.toggle"),this.$element.unwrap()};var d=a.fn.bootstrapToggle;a.fn.bootstrapToggle=b,a.fn.bootstrapToggle.Constructor=c,a.fn.toggle.noConflict=function(){return a.fn.bootstrapToggle=d,this},a(function(){a("input[type=checkbox][data-toggle^=toggle]").bootstrapToggle()}),a(document).on("click.bs.toggle","div[data-toggle^=toggle]",function(b){var c=a(this).find("input[type=checkbox]");c.bootstrapToggle("toggle"),b.preventDefault()})}(jQuery);


var StormpathSettings = Backbone.View.extend({

    events: {
        "change .enable-id-site": "toggleIdSite",
        "click #update-id-site-settings": "updateIdSiteSettings",
        "change #stormpath_cache_driver": "updateCacheDriverOptions",
        "click #update-stormpath-cache-settings": "updateCacheSettings"
    },
    toggleIdSite: function() {
        var position = jQuery('.enable-id-site').prop('checked') == true ? 1 : 0;
        var vm = this;
        if ( position ) {
            vm.getIdSiteSettings();
            jQuery('#stormpath-id-site .stormpath-id-site-settings').slideDown();
        } else {
            jQuery('#stormpath-id-site .stormpath-id-site-settings').slideUp();
        }
        jQuery(document).ready(function($) {

            var data = {
                'action': 'set_stormpath_option',
                '_nonce': jQuery('#stormpath-settings').data('nonce'),
                'option': 'stormpath_id_site',
                'value': position == 1 ? 1 : ''
            };

            jQuery.post(ajaxurl, data, function(response) {

            });
        });
    },
    updateIdSiteSettings: function(e) {
        e.preventDefault();
        jQuery(document).ready(function($) {
            var oldValue = jQuery('#update-id-site-settings').val();
            jQuery('#update-id-site-settings').val('Saving Settings').attr('disabled','disabled');

            jQuery('.stormpath-id-site-settings .stormpath-error-text').html('');
            var data = {
                'action': 'stormpath_update_id_site_settings',
                '_nonce': jQuery('#stormpath-settings').data('nonce'),
                'data': {
                    "sslPublic": jQuery('#stormpath_id_site_ssl_public_chain').val(),
                    "sslPrivate": jQuery('#stormpath_id_site_ssl_private').val(),
                    "authorizedOrigins": jQuery('#stormpath_id_site_authorized_javascript_origin').val(),
                    "redirectUris": jQuery('#stormpath_id_site_authorized_redirect_urls').val(),
                    "tti": jQuery('#stormpath_id_site_session_idle_timeout').val(),
                    "ttl": jQuery('#stormpath_id_site_session_max_age').val(),
                },
            };

            jQuery.post(ajaxurl, data, function(response) {
                if(response.success === false) {
                    jQuery('.stormpath-id-site-settings .stormpath-error').fadeIn(200).delay(1000).fadeOut(300);
                    jQuery('.stormpath-id-site-settings .stormpath-error-text').html(response.data);
                    jQuery('#update-id-site-settings').val(oldValue).attr('disabled',false);
                }
                else {
                    jQuery('.stormpath-id-site-settings .stormpath-updated').fadeIn(200).delay(1000).fadeOut(300);
                    jQuery('#update-id-site-settings').val(oldValue).attr('disabled',false);
                }
            }).fail(function(response) {
                jQuery('.stormpath-id-site-settings .stormpath-error').fadeIn(200).delay(1000).fadeOut(300);
                jQuery('#update-id-site-settings').val(oldValue).attr('disabled',false);
            });
        });
    },

    updateCacheDriverOptions: function(e) {
        e.preventDefault();

        var selected = jQuery('select[name="stormpath_cache_driver"]').val();
        var $memcachedSettings = jQuery('#stormpath_cache_driver_memcached_settings');
        var $redisSettings = jQuery('#stormpath_cache_driver_redis_settings');

        switch(selected.toLowerCase()) {
            case 'memcached' :
                $memcachedSettings.show();
                $redisSettings.hide();
                break;
            case 'redis' :
                $memcachedSettings.hide();
                $redisSettings.show();
                break;
            default :
                $memcachedSettings.hide();
                $redisSettings.hide();
                break;
        }

    },

    updateCacheSettings: function(e) {
        e.preventDefault();
        jQuery(document).ready(function($) {
            var oldValue = jQuery('#update-stormpath-cache-settings').val();
            jQuery('#update-stormpath-cache-settings').val('Saving Settings').attr('disabled','disabled');

            jQuery('.stormpath-cache-settings .stormpath-error-text').html('');
            var data = {
                'action': 'stormpath_update_cache_settings',
                '_nonce': jQuery('#stormpath-settings').data('nonce'),
                'data': {
                    "driver": jQuery('select[name="stormpath_cache_driver"]').val(),
                    "memcached_host": jQuery('#stormpath_memcached_host').val(),
                    "memcached_port": jQuery('#stormpath_memcached_port').val(),
                    "redis_host": jQuery('#stormpath_redis_host').val(),
                    "redis_password": jQuery('#stormpath_redis_password').val()
                }
            };

            console.log(data);
            jQuery.post(ajaxurl, data, function(response) {
                if(response.success === false) {
                    jQuery('.stormpath-cache-settings .stormpath-error').fadeIn(200).delay(1000).fadeOut(300);
                    jQuery('.stormpath-cache-settings .stormpath-error-text').html(response.data);
                    jQuery('#update-stormpath-cache-settings').val(oldValue).attr('disabled',false);
                }
                else {
                    jQuery('.stormpath-cache-settings .stormpath-updated').fadeIn(200).delay(1000).fadeOut(300);
                    jQuery('#update-stormpath-cache-settings').val(oldValue).attr('disabled',false);
                }
            }).fail(function(response) {
                jQuery('.stormpath-cache-settings .stormpath-error').fadeIn(200).delay(1000).fadeOut(300);
                jQuery('#update-stormpath-cache-settings').val(oldValue).attr('disabled',false);
            });
        });
    },

    initialize: function() {
        var position = jQuery('.enable-id-site').prop('checked');
        if ( position ) {

            this.getIdSiteSettings();

        }
    },
    getIdSiteSettings: function() {
        jQuery(document).ready(function($) {

            var data = {
                'action': 'stormpath_get_id_site_settings',
                '_nonce': jQuery('#stormpath-settings').data('nonce')
            };

            jQuery.get(ajaxurl, data, function(response) {
                console.log(response);
                jQuery('#stormpath_id_site_domain_name').val(response.domainName);
                jQuery('#stormpath_id_site_ssl_public_chain').val(response.tlsPublicCert);
                jQuery('#stormpath_id_site_ssl_private').val(response.tlsPrivateKey);
                jQuery('#stormpath_id_site_authorized_javascript_origin').val(response.authorizedOriginUris.join("\n"));
                jQuery('#stormpath_id_site_authorized_redirect_urls').val(response.authorizedRedirectUris.join("\n"));
                jQuery('#stormpath_id_site_session_idle_timeout').val(response.sessionTti);
                jQuery('#stormpath_id_site_session_max_age').val(response.sessionTtl);
                jQuery('#stormpath-id-site .stormpath-id-site-settings').slideDown();
            });
        });
    }
});


var stormpathSettings = new StormpathSettings({ el: jQuery('#stormpath-settings') });



jQuery(function() {
    jQuery('.stormpath-option-toggle').bootstrapToggle();
});











