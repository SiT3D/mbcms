/* global save_menu_params, mbcms */

mbcms.admin.tab.$currentTab = null;
//mbcms.admin.tab.$lastTab = {};
mbcms.admin.tab.CURRENT_MBCMS_TAB_ID = 0;
mbcms.admin.tab.$currentWindow = null;
mbcms.admin.tab.$currentRefreshTab = {};
mbcms.admin.tab.$ajax = {};
mbcms.admin.tab.stack = [];
mbcms.admin.tab.MBCMS_MAIN_CONTROLL_TAB_CREATE = 'MBCMS_MAIN_CONTROLL_TAB_CREATE';
mbcms.admin.tab.MBCMS_MAIN_IFRAME_TAB_CREATE = 'MBCMS_MAIN_IFRAME_TAB_CREATE';
mbcms.admin.tab.MBCMS_MAIN_SCHEMATIC_TAB_CREATE = 'MBCMS_MAIN_SCHEMATIC_TAB_CREATE';
mbcms.admin.tab.MBCMS_MAIN_TEMPLATES_TAB_CREATE = 'MBCMS_MAIN_TEMPLATES_TAB_CREATE';
mbcms.admin.tab.MBCMS_ALL_STANDART_TABS_INIT = 'MBCMS_ALL_STANDART_TABS_INIT';
mbcms.admin.tab.MBCMS_ALL_ALL_TABS_INIT = 'MBCMS_ALL_ALL_TABS_INIT';
mbcms.admin.tab._allAllArray = [];
mbcms.admin.tab._allTabs = {};



/**
 * Создает закладку, и вызывает все js подписаные на создание этой закладки
 * 
 * @param {string} tabName название taba
 * @param {string} moduleClass название класса к которому обратится аякс
 * @param {string} tabID  уникальный ID для рассылки события
 * @param {object} $serverData объект для подстановки в ajax запрос
 * @param {object} $tabData объект который будет добавлен к обратному вызову
 * @param {bool} beforeSend выводит в консоль параметры запроса к серверу
 * @returns {undefined}
 */
mbcms.admin.tab.createTab = function (tabName, moduleClass, tabID, $serverData, $tabData, beforeSend)
{
    $serverData = typeof $serverData !== 'undefined' ? $serverData : {};
    $tabData = typeof $tabData !== 'undefined' ? $tabData : {};
    $serverData['class'] = moduleClass;
    $serverData[mbcms.ADMIN_STATUS] = true;

    if (typeof this.$ajax[tabID] !== 'undefined')
    {
        this.$ajax[tabID].abort();
    }

    this.$ajax[tabID] = $.ajax(
            {
                url: '/ajax',
                type: 'GET',
                data: $serverData,
                success: function (msg)
                {
                    var $module = $(msg);
                    var $tab = mbcms.admin.tab.tabConstructor(tabName, tabID);
                    $('#admin_modules_content').append($module);
                    $module.hide();

                    mbcms.admin.tab.tabInit($tab, $module, moduleClass, $serverData, $tabData, tabID);
                    var e = {
                        tab: $tab,
                        module: $module,
                        name: tabName,
                        tabID: tabID,
                        class: moduleClass,
                        serverData: $serverData,
                        tabData: $tabData
                    };

                    mbcms.admin.tab.listnersCall(e, tabID);
                    delete mbcms.admin.tab.$ajax[tabID];
                },
                beforeSend: function (a, b)
                {
                    if (beforeSend)
                    {
                        console.log(a);
                        console.log(b);
                    }
                }

            });
};


/**
 * Дает возможность закрывать вкладку.
 * @param {$} $tab
 * @returns {undefined}
 */
mbcms.admin.tab.setCloser = function ($tab)
{
    if (!$tab.hasClass('closeble'))
    {
        $tab.addClass('closeble');
        $tab.mousemove(function (e)
        {
            if (typeof $(this).data('mover') === 'undefined')
            {
                $(this).data('mover', 0);
            }
            
            if (e.clientX < 20 && $(this).data('mover') <= 21 )
            {
                $(this).data('mover', $(this).data('mover') + 1);
            }
            else if (e.clientX < 20 && $(this).data('mover') > 20)
            {
                $tab.close();
            }
            else
            {
                $(this).data('mover', 0);
            }
                
        }).dblclick(function ()
        {
            $tab.close();
        });
    }
};

mbcms.admin.tab.getLastStackTab = function ()
{
    var $lastInStack = this.stack.pop();
    if (typeof $lastInStack !== 'undefined')
    {
        var $lastTab = mbcms.admin.tab.getTab($lastInStack.prop('id'), true);
        if (typeof $lastTab !== 'undefined')
        {
            return $lastTab;
        }
    }

    return undefined;
};

mbcms.admin.tab.tabConstructor = function (name, tabID)
{
    var $tab = $('<div id="' + tabID + '" class="tab" />');
    var $text = $('<span class="text">' + name + '</span>');
    var $refresher = $('<span class="tab-refresher ico refresh-my" />');
    $tab.append($text).append($refresher);

    $('#admin_tabs').append($tab);

    $tab.mousemove(function ()
    {
        return false;
    }).hover(function ()
    {
        $refresher.show();
    }, function ()
    {
        $refresher.hide();
    });

    $refresher.click(function ()
    {
        $tab.refresh();
        return false;
    });

    $text.mousemove(function ()
    {
        return false;
    }).mousedown(function ()
    {
        return false;
    });

    $tab.mbcms_tab_id = this.CURRENT_MBCMS_TAB_ID;
    this.CURRENT_MBCMS_TAB_ID++;

    if (typeof this._allTabs[tabID] === 'undefined')
    {
        this._allTabs[tabID] = {};
        this._allTabs[tabID].numb = 0;
    }

    this._allTabs[tabID][this._allTabs[tabID].numb] = $tab;
    $tab.allTabsID = this._allTabs[tabID].numb;
    this._allTabs[tabID].numb++;

    return $tab;
};

mbcms.admin.tab._activeTab = function ($tab, $window)
{
    $window.show();
    $tab.addClass('active');

    if (!$window.hasClass('window'))
    {
        if (mbcms.admin.tab.$currentTab !== null)
        {
            mbcms.admin.tab.$currentTab.removeClass('active');
            //сохр позицию прокрутки
            mbcms.admin.tab.$currentTab.data('currentScroll', $('#admin_modules_content').scrollTop());
            mbcms.admin.tab.$currentWindow.hide();
        }

        //загр если есть
        $window.show();
        $tab.addClass('active');
        if (typeof $tab.data('currentScroll') !== 'undefined')
        {
            $('#admin_modules_content').scrollTop($tab.data('currentScroll'));
        }
        mbcms.admin.tab.$currentTab = $tab;
        mbcms.admin.tab.$currentWindow = $window;
    }
};

mbcms.admin.tab._deactiveTab = function ($tab, $window)
{
    $window.hide();
    $tab.removeClass('active');
    if (!$window.hasClass('window'))
    {
        mbcms.admin.tab.$currentTab = null;
        mbcms.admin.tab.$currentWindow = null;
    }
};

mbcms.admin.tab._setTop = function ($tab)
{
    $('.top-window').removeClass('top-window');
    $tab.mbcms_window.addClass('top-window');
};

mbcms.admin.tab.tabInit = function ($tab, $window, moduleClass, serverData, tabData, tabID)
{

    $tab.mbcms_window = $window;
    $window.mbcms_tab = $tab;
    var $this = this;

    $tab.click(function ()
    {
        mbcms.admin.tab.clickk($tab, $window);
        return false;
    });

    $tab.serverData = serverData;
    $tab.tabData = tabData;

    $tab.active = function (active, time, timeCallback)
    {
        var $window = $tab.mbcms_window;
        mbcms.admin.tab._setTop($tab);
        $this.stack.push($tab);
        if ($this.stack.length > 10)
        {
            $this.stack.shift();
        }

        if (active)
        {
            if (typeof time !== 'undefined')
            {
                $tab.active_deactive_timer = setTimeout(function ()
                {
                    mbcms.admin.tab._activeTab($tab, $window);
                    if (typeof timeCallback !== 'undefined')
                    {
                        timeCallback($tab);
                    }
                }, time);
            }
            else
            {
                mbcms.admin.tab._activeTab($tab, $window);
            }
        }
        else
        {
            if (typeof time !== 'undefined')
            {
                $tab.active_deactive_timer = setTimeout(function ()
                {
                    mbcms.admin.tab._deactiveTab($tab, $window);
                    if (typeof timeCallback !== 'undefined')
                    {
                        timeCallback();
                    }
                }, time);
            }
            else
            {
                mbcms.admin.tab._deactiveTab($tab, $window);
            }
        }
    };

    $tab.setCloser = function ()
    {
        mbcms.admin.tab.setCloser($tab, $window);
    };

    $tab.notRefresh = function ()
    {
        $tab.find('.tab-refresher').remove();
    };

    $tab.isActive = function ()
    {
        if ($tab.hasClass('active'))
        {
            return true;
        }

        return false;
    };

    $tab.close = function ($backTab)
    {
        $backTab = typeof $backTab === 'undefined' ? true : false;
        function removeInClose(fmbcms_tab_id)
        {
            for (var i in $this.stack)
            {
                if ($this.stack[i].mbcms_tab_id === fmbcms_tab_id)
                {
                    $this.stack.splice(i, 1);
                }
            }
        }

        if (typeof mbcms.admin.tab._allTabs[tabID] !== 'undefined')
        {
            if (typeof mbcms.admin.tab._allTabs[tabID][$tab.allTabsID] !== 'undefined')
            {
                delete mbcms.admin.tab._allTabs[tabID][$tab.allTabsID];
                mbcms.admin.tab._allTabs[tabID].numb--;
                if (mbcms.admin.tab._allTabs[tabID].numb <= 0)
                {
                    delete mbcms.admin.tab._allTabs[tabID].numb;
                    delete mbcms.admin.tab._allTabs[tabID];
                }
            }
        }

        removeInClose($tab.mbcms_tab_id);
        $tab.remove();
        $tab.mbcms_window.remove();

        var $lastTab = $this.getLastStackTab();
        if (typeof $lastTab !== 'undefined' && $backTab)
        {
            $lastTab.active(true);
        }
    };

    $tab.position = function (position)
    {
        $(this).attr('position', position);
    };

    $window.setWindow = function (set)
    {
        if (set) // дальше будет немного ни так
        {
            $window.addClass('window');
            $window.removeClass('fixed-window');
        }
        else
        {
            $window.removeClass('window');
            $window.addClass('fixed-window');
        }
    };

    if (typeof moduleClass !== 'undefined')
    {
        $tab.refresh = function (callback)
        {
            var tabName = $tab.children('.text').text();
            var data = typeof serverData === 'undefined' ? {} : serverData;
            var tabDataI = typeof tabData === 'undefined' ? {} : tabData;
            data['class'] = moduleClass;
            data[mbcms.ADMIN_STATUS] = true;
            mbcms.admin.tab.createTab(tabName, moduleClass, $tab.attr('id'), data, tabDataI);
            mbcms.admin.tab.$currentRefreshTab[$tab.attr('id')] = $tab;

            mbcms.admin.tab.addListner($tab.attr('id'), function (e)
            {
                if (typeof mbcms.admin.tab.$currentRefreshTab[e.tabID] !== 'undefined')
                {
                    mbcms.admin.tab.$currentRefreshTab[e.tabID].after(e.tab);
                    if (mbcms.admin.tab.$currentRefreshTab[e.tabID].hasClass('closeble'))
                    {
                        e.tab.setCloser();
                    }
                    else
                    {
                        mbcms.admin.tab.$currentRefreshTab[e.tabID].setCloser();
                    }

                    var oldModule = mbcms.admin.tab.$currentRefreshTab[e.tabID].mbcms_window;
                    if (!oldModule.hasClass('window'))
                    {
                        e.module.setWindow(false);
                    }

                    mbcms.admin.tab.$currentRefreshTab[e.tabID].close(false);

                }

                delete mbcms.admin.tab.$currentRefreshTab[e.tabID];

                if (typeof mbcms.admin.tab._allTabs[tabID] === 'undefined')
                {
                    mbcms.admin.tab._allTabs[tabID] = {};
                    mbcms.admin.tab._allTabs[tabID].numb = 0;
                }

                mbcms.admin.tab._allTabs[e.tabID][mbcms.admin.tab._allTabs[tabID].numb] = e.tab;
                e.tab.data('currentScroll', $tab.data('currentScroll'));

                if ($tab.hasClass('active'))
                {
                    e.tab.active(true);
                }

            }, true);

            if (typeof callback === 'function')
            {
                callback();
            }
        };
    }
};

/**
 * 
 * @param {int} tid - unical ID tab
 * @param {bool} first return 1 first tab or all tabs this ID
 * @returns {mbcms.admin.tab._allTabs|mbcms.admin.tab@arr;@arr;_allTabs} $tab
 */
mbcms.admin.tab.getTab = function (tid, first)
{
    first = typeof first === 'undefined' ? false : first;
    if (first)
    {
        for (var i in this._allTabs[tid])
        {
            if (typeof this._allTabs[tid][i] === 'object')
            {
                return this._allTabs[tid][i];
            }
            else
            {
                return undefined;
            }
        }
    }
    return this._allTabs[tid];
};

mbcms.admin.tab.clickk = function ($tab)
{
    mbcms.admin.tab._setTop($tab);

    if (!$tab.hasClass('active'))
    {
        $tab.active(true);
        return;
    }

    if ($tab.hasClass('active') && $tab.mbcms_window.hasClass('window'))
    {
        $tab.active(false);
        return;
    }

};

mbcms.admin.tab.funcs = [];

/**
 * @syntax addListner(tid,f)
 * @param {string} tid уникальный ID
 * @param {function callable} f подписаная функция
 * @param {bool} unshift добавить в начало массива
 * @returns {object} 
 *                  tab: $tab, 
 *                  module: $module,  <br>
 *                  name: tabName, 
 *                  tabID: tabID 
 */
mbcms.admin.tab.addListner = function (tid, f, unshift)
{
    unshift = typeof unshift === 'undefined' ? false : unshift;

    if (typeof tid === 'object')
    {
        for (var key in tid)
        {
            this.addListner(tid[key], f, unshift);
        }
    }
    else if (tid === this.MBCMS_ALL_STANDART_TABS_INIT)
    {
        this.addListner(this.MBCMS_MAIN_CONTROLL_TAB_CREATE, f, unshift);
        this.addListner(this.MBCMS_MAIN_SCHEMATIC_TAB_CREATE, f, unshift);
        this.addListner(this.MBCMS_MAIN_TEMPLATES_TAB_CREATE, f, unshift);
        this.addListner(this.MBCMS_MAIN_IFRAME_TAB_CREATE, f, unshift);
    }
    else if (tid === this.MBCMS_ALL_ALL_TABS_INIT)
    {
        this._allAllArray.push(f);
    }
    else
    {
        var obj = {func: f, tabID: tid};

        if (unshift)
        {
            this.funcs.unshift(obj);
        }
        else
        {
            this.funcs.push(obj);
        }
    }

};

mbcms.admin.tab.removeListner = function (tid)
{
    for (var i = 0; i < this.funcs.length; i++)
    {
        if (tid === this.funcs[i].tabID)
        {
            delete this.funcs.splice(i, 1);
        }
    }
};

mbcms.admin.tab.listnersCall = function (info, tid)
{
    for (var i = 0; i < this.funcs.length; i++)
    {
        if (tid === this.funcs[i].tabID)
        {
            if (typeof this.funcs[i].func === 'undefined')
            {
                console.log(tid);
            }
            else
            {
                this.funcs[i].func(info);
            }
        }
    }

    for (var uid in this._allAllArray)
    {
        if (typeof this._allAllArray[uid] === 'undefined')
        {
            console.log(this._allAllArray[uid]);
        }
        else
        {
            this._allAllArray[uid](info);
        }
    }
};

(function ()
{
    $(document).keyup(function (e)
    {
        if (e.keyCode === 27)
        {
            var id = $('#admin_tabs .tab.active:last').prop('id');
            var ids = mbcms.admin.tab.getTab(id);
            for (var i in ids)
            {
                if (i !== 'numb' && ids[i].hasClass('active'))
                {
                    ids[i].click();
                    if (ids[i].hasClass('closeble'))
                    {
//                        ids[i].close(); // если нужно закрывать.
                    }
                }
            }
        }
    });
})();