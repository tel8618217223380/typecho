var typechoGuid = function (el, config) {
    var _dl  = $(el);
    var _dt  = _dl.getElements('dt');
    var _dd  = _dl.getElements('dd');
    var _cur = null, _timer = null;

    var handle = {
       reSet: function() {
           /*
           if (_cur) {
               console.info(_cur);
                //_cur.removeClass('current');
                //_cur.getNext('dd').setStyle('display', 'none');
                delete _cur;
           } else {
           */
                _dt.removeClass('current');
                _dd.setStyle('display', 'none');
            //}
        },

        popUp: function(el) {
            el = _cur =  $(el) || el;
            el.addClass('current');
            var _d = el.getNext('dd');
            if (_d) {
                _d.setStyle('left', el.getPosition().x - config.offset);
                if (_d.getStyle('display') != 'none') {
                    _d.setStyle('display', 'none');
                } else {
                    _d.setStyle('display', 'block');
                }
            }
        }
    }

   if (config.type == 'mouse') {
        _dt.addEvent('mouseenter', function(event){
            _timer = $clear(_timer); handle.reSet();
            if (event.target.nodeName.toLowerCase() == 'a') {
                event.target = $(event.target).getParent('dt');
            }

            handle.popUp(event.target);
        });

        _dt.addEvent('mouseout', function(event){
            if (!_timer) {
                _timer = handle.reSet.delay(500);
            }
        });

        _dd.addEvent('mouseenter', function(event){
            if (_timer) {
                _timer = $clear(_timer);
            }
        });

        _dd.addEvent('mouseleave', function(event){
            if (!_timer) {
                _timer = handle.reSet.delay(50);
            }
        });
    }

    if (config.type == 'click') {
        _dt.addEvent('click', function(event){
            handle.reSet();
            if (event.target.nodeName.toLowerCase() == 'a') {
                event.target = $(event.target).getParent('dt');
            }

            handle.popUp(event.target);
            event.stop(); // 停止事件传播
        });
        $(document).addEvent('click', handle.reSet);
    }

    return handle;
};

var typechoMessage = function () {
    var message = $(document).getElement('.popup');
    if (message) {
        var messageEffect = new Fx.Morph(message, {duration: 'short', transition: Fx.Transitions.Sine.easeOut});
        messageEffect.addEvent('complete', function () {
            this.element.style.display = 'none';
        });
        messageEffect.start({'margin-top': [30, 0], 'height': [21, 0], 'opacity': [1, 0]});
    }
};

var typechoOperate = function (selector, op) {
    /** 获取元素 */
    var el = $(document).getElement(selector);
    
    if (el && 'table' == el.get('tag')) {
        /** 如果是标准表格 */
        var elements = el.getElements('tbody tr td input[type=checkbox]');
        switch (op) {
            case 'selectAll':
                elements.each(function(item) {
                    $(item).getParent('tr').addClass('checked');
                    $(item).setProperty('checked', 'true');
                });
                break;
            case 'selectNone':
                elements.each(function(item) {
                    $(item).getParent('tr').removeClass('checked');
                    $(item).removeProperty('checked');
                });
                break;
            default:
                break;
        }
    } else if (el && 'ul' == el.get('tag')) {
        /** 如果是列表形式 */
        var elements = el.getElements('li input[type=checkbox]');
        switch (op) {
            case 'selectAll':
                elements.each(function(item) {
                    $(item).getParent('li').addClass('checked');
                    $(item).setProperty('checked', 'true');
                });
                break;
            case 'selectNone':
                elements.each(function(item) {
                    $(item).getParent('li').removeClass('checked');
                    $(item).removeProperty('checked');
                });
                break;
            default:
                break;
        }
    }
};

var typechoTableListener = function (selector) {
    /** 获取元素 */
    var el = $(document).getElement(selector);
    
    if (el && 'table' == el.get('tag')) {
        /** 如果是标准表格 */
        
        /** 监听click事件 */
        el.getElements('tbody tr td input[type=checkbox]').each(function(item) {
            $(item).addEvent('click', function() {
                if ($(this).getProperty('checked')) {
                    $(this).getParent('tr').addClass('checked');
                } else {
                    $(this).getParent('tr').removeClass('checked');
                }
            });
        });
        
        /** 监听鼠标事件 */
        el.getElements('tbody tr').each(function(item) {
            $(item).addEvents({'mouseover': function() {
                $(this).addClass('hover');
            },
            'mouseleave': function() {
                $(this).removeClass('hover');
            },
            'click': function() {
                var checkBox = $(this).getElement('input[type=checkbox]');
                if (checkBox) {
                    checkBox.click();
                }
            }
            });
        });
    } else if (el && 'ul' == el.get('tag')) {
        /** 如果是列表形式 */
        el.getElements('li input[type=checkbox]').each(function(item) {
            $(item).addEvent('click', function() {
                if ($(this).getProperty('checked')) {
                    $(this).getParent('li').addClass('checked');
                } else {
                    $(this).getParent('li').removeClass('checked');
                }
            });
        });
        
        /** 监听鼠标事件 */
        el.getElements('li').each(function(item) {
            $(item).addEvents({'mouseover': function() {
                $(this).addClass('hover');
            },
            'mouseleave': function() {
                $(this).removeClass('hover');
            },
            'click': function() {
                var checkBox = $(this).getElement('input[type=checkbox]');
                if (checkBox) {
                    checkBox.click();
                }
            }
            });
        });
    }
};