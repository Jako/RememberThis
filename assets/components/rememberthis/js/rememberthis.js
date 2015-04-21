/**
 * RememberThis
 *
 * Copyright 2008-2015 by Thomas Jakobi <thomas.jakobi@partout.info>
 *
 * @package rememberthis
 * @subpackage javascript
 */
;
(function ($, window, document, undefined) {

    var pluginName = 'rememberThis';
    // default settings
    var defaults = {
        ajaxLoaderImg: '',
        onBeforeAdd: function (list, elem, id) {
        },
        onBeforeDelete: function (list, elem, id) {
        }
    };

    // plugin function
    function Plugin(el, options) {
        // Extending options
        this.options = $.extend({}, defaults, options);

        // Private
        this._defaults = defaults;
        this._name = pluginName;
        this.$el = $(el);

        this.init();
    }

    // Separate functionality from object creation
    Plugin.prototype = {
        init: function () {
            var _this = this;

            if (_this.options.ajaxLoaderImg !== '') {
                _this.options.loadImage = $('<img>').addClass('rememberload').attr('src', _this.options.ajaxLoaderImg);
            }
            else {
                _this.options.loadImage = $('<i>').addClass('fa fa-refresh fa-spin rememberload');
            }
            $('.rememberadd').on('click', function (e) {
                e.preventDefault();
                var rememberid = $(this).data('add');
                $(this).append(_this.options.loadImage.clone());
                _this.options.onBeforeAdd.call(_this.$el, this, rememberid);
                _this.onAdd(_this.$el, this, rememberid);
            });
            _this.$el.on('click', '.rememberdelete', function (e) {
                e.preventDefault();
                var deleteid = $(this).data('delete');
                $(this).hide().after(_this.options.loadImage.clone());
                _this.options.onBeforeDelete.call(_this.$el, this, deleteid);
                _this.onDelete(_this.$el, this, deleteid);
            });
        },
        onAdd: function (list, elem, id) {
            var _this = this;
            $.ajax({
                type: 'GET',
                url: _this.options.connectorUrl,
                data: {
                    language: _this.options.language,
                    action: 'remember',
                    add: id
                },
                success: function (data) {
                    $('.rememberload').remove();
                    if ($('.rememberempty', list).length) {
                        list.slideUp('fast', function () {
                            $(this).html(data.result).slideDown('slow');
                        });
                    } else {
                        if (data.result.length) {
                            var newDoc = $(data.result).attr('style', 'display: none');
                            list.append(newDoc);
                            newDoc.slideDown('slow');
                        }
                    }
                }
            });
        },
        onDelete: function (list, elem, id) {
            var _this = this;
            $.ajax({
                type: 'GET',
                url: _this.options.connectorUrl,
                data: {
                    language: _this.options.language,
                    action: 'remember',
                    delete: id
                },
                success: function (data) {
                    if (isNaN(data.result)) {
                        list.slideUp('slow', function () {
                            $(this).html($.trim(data.result)).slideDown('fast');
                        });
                    } else {
                        $(elem).parent().slideUp('slow', function () {
                            $(this).remove();
                        });
                    }
                }
            });
        }
    };

    // The actual plugin
    $.fn[pluginName] = function (options) {
        var args = arguments;
        if (options === undefined || typeof options === 'object') {
            return this.each(function () {
                if (!$.data(this, 'plugin_' + pluginName)) {
                    $.data(this, 'plugin_' + pluginName, new Plugin(this, options));
                }
            });
        } else if (typeof options === 'string' && options[0] !== '_' && options !== 'init') {
            var returns;
            this.each(function () {
                var instance = $.data(this, 'plugin_' + pluginName);
                if (instance instanceof Plugin && typeof instance[options] === 'function') {
                    returns = instance[options].apply(instance, Array.prototype.slice.call(args, 1));
                }
                if (options === 'destroy') {
                    $.data(this, 'plugin_' + pluginName, null);
                }
            });
            return returns !== undefined ? returns : this;
        }
    };
}(jQuery, window, document));