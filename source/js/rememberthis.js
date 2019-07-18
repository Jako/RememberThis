/**
 * RememberThis
 *
 * @author Thomas Jakobi <thomas.jakobi@partout.info>
 *
 * @package rememberthis
 * @subpackage jqueryplugin
 */
;(function ($, window, document, undefined) {

    var pluginName = 'rememberThis',
        defaults = {
            ajaxLoaderImg: '',
            listSelector: '.rememberthis',
            onBeforeAdd: function (list, otherlists, elem, id, data) {
            },
            onBeforeDelete: function (list, otherlists, elem, id, data) {
            },
            onAfterAdd: function (list, otherlists, elem, id, data) {
            },
            onAfterDelete: function (list, otherlists, elem, id, data) {
            }
        };

    // The actual Plugin constructor
    function Plugin(el, options, firstCall) {
        // Extending options
        this.options = $.extend({}, defaults, options);

        // Private
        this._defaults = defaults;
        this._name = pluginName;
        this.$el = $(el);
        this.$other = $(this.options.listSelector).not(this.$el);

        this.init(firstCall);
    }

    // Separate functionality from object creation
    Plugin.prototype = {
        init: function (firstCall) {
            var _this = this;

            if (_this.options.ajaxLoaderImg !== '') {
                _this.options.loadImage = $('<img class="rememberload" alt="Loading" src="' + _this.options.ajaxLoaderImg + '">');
            }
            else {
                _this.options.loadImage = $('<i class="fa fa-refresh fa-spin rememberload">');
            }
            if (firstCall) {
                $(document).on('click', '.rememberadd', function (e) {
                    e.preventDefault();
                    var rememberid = $(this).data('add');
                    var properties = $(this).data();
                    delete properties.add;
                    $(this).append(_this.options.loadImage.clone());
                    _this.options.onBeforeAdd.call(_this.$el, _this.$other, this, rememberid, properties);
                    _this.onAdd(_this.$el, _this.$other, this, rememberid, properties);
                    _this.options.onAfterAdd.call(_this.$el, _this.$other, this, rememberid, properties);
                });
                $(document).on('submit', '.rememberaddform', function (e) {
                    e.preventDefault();
                    var rememberid = $(this).data('add');
                    var serializedForm = {};
                    $.each($(this).serializeArray(), function (i, input) {
                        if (serializedForm.hasOwnProperty(input.name)) {
                            if (typeof serializedForm[input.name] === 'string') {
                                serializedForm[input.name] = [serializedForm[input.name]];
                            }
                            serializedForm[input.name].push(input.value);
                        } else {
                            serializedForm[input.name] = input.value;
                        }
                    });
                    $(this).append(_this.options.loadImage.clone());
                    _this.options.onBeforeAdd.call(_this.$el, _this.$other, this, rememberid, serializedForm);
                    _this.onAdd(_this.$el, _this.$other, this, rememberid, serializedForm);
                    _this.options.onAfterAdd.call(_this.$el, _this.$other, this, rememberid, serializedForm);
                });
            }
            _this.$el.on('click', '.rememberdelete', function (e) {
                e.preventDefault();
                var deleteid = $(this).data('delete');
                $(this).hide().after(_this.options.loadImage.clone());
                _this.options.onBeforeDelete.call(_this.$el, _this.$other, this, deleteid);
                _this.onDelete(_this.$el, _this.$other, this, deleteid);
                _this.options.onAfterDelete.call(_this.$el, _this.$other, this, deleteid);
            });
        },
        onAdd: function (list, otherlists, elem, id, addproperties) {
            var _this = this;
            $.ajax({
                type: 'GET',
                url: _this.options.connectorUrl,
                data: {
                    language: _this.options.language,
                    action: 'remember',
                    add: id,
                    addproperties: addproperties
                },
                success: function (data) {
                    $('.rememberload').remove();
                    if (data.result) {
                        if ($('.rememberempty', list).length) {
                            list.slideUp('fast', function () {
                                $('.remembercount').html(data.count);
                                $(this).html(data.result).slideDown('slow');
                            });
                            if (otherlists.length) {
                                otherlists.each(function () {
                                    $(this).slideUp('fast', function () {
                                        $(this).html($.trim(data.result)).slideDown('slow');
                                    });
                                });
                            }
                        } else {
                            var newDoc = $(data.result).attr('style', 'display: none');
                            list.append(newDoc);
                            $('.remembercount').html(data.count);
                            if (otherlists.length) {
                                otherlists.each(function () {
                                    var otherDoc = newDoc.clone();
                                    $(this).append(otherDoc);
                                    otherDoc.slideDown('slow');
                                });
                            }
                            newDoc.slideDown('slow');
                        }
                    }
                    if (data.debug) {
                        $('.rememberdebug').html(data.debug);
                    }
                }
            });
        },
        onDelete: function (list, otherlists, elem, id) {
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
                    if (!data.count || data.count === '0') {
                        list.slideUp('slow', function () {
                            $('.remembercount').html(data.count);
                            $(this).html($.trim(data.result)).slideDown('fast');
                        });
                        if (otherlists.length) {
                            otherlists.each(function () {
                                $(this).slideUp('slow', function () {
                                    $(this).html($.trim(data.result)).slideDown('fast');
                                });
                            });
                        }
                    } else {
                        var listelem = $(elem).parent();
                        var listindex = listelem.index();
                        listelem.slideUp('slow', function () {
                            $('.remembercount').html(data.count);
                            $(this).remove();
                        });
                        if (otherlists.length) {
                            otherlists.each(function () {
                                $(this).children().eq(listindex).slideUp('slow', function () {
                                    $(this).remove();
                                });
                            });
                        }
                    }
                    if (data.debug) {
                        $('.rememberdebug').html(data.debug);
                    }
                }
            });
        }
    };

    // The actual plugin
    $.fn[pluginName] = function (options) {
        var args = arguments;
        if (options === undefined || typeof options === 'object') {
            var firstCall = true;
            return this.each(function () {
                if (!$.data(this, 'plugin_' + pluginName)) {
                    $.data(this, 'plugin_' + pluginName, new Plugin(this, options, firstCall));
                }
                firstCall = false;
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
