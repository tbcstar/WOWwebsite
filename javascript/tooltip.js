if (typeof $CoT == "undefined") {
    $CoT = {
		cdnUrl: '//cdn.cavernoftime.com',
		expUrl: '.cavernoftime.com'
	};
}

if (typeof $CoTTooltip == "undefined") {
    var $CoTTooltip = new function () {
        var external = true;
		var headtag = document.getElementsByTagName("head")[0];
        var
		  	method  = { type: 3 },
            currentType,
            currentId,
            currentLocale,
            currentDomain,
            currentParams,
			currentExpansion,
            currentA,

            cursorX,
            cursorY,

            mode = 0,

            eventAttached = false,

            npcs = {},
            objects = {},
            items = {},
            quests = {},
            spells = {},
            achievements = {},
			
            STATUS_NONE     = 0,
            STATUS_QUERYING = 1,
            STATUS_ERROR    = 2,
            STATUS_NOTFOUND = 3,
            STATUS_OK       = 4,
            STATUS_SCALES   = 5,
			STATUS_INVALID  = 6,
			/*
            SCALES_NONE     = 0,
            SCALES_LOADED   = 1,
            SCALES_QUERYING = 2,
			*/
            TYPE_NPC         = 1,
            TYPE_OBJECT      = 2,
            TYPE_ITEM        = 3,
            TYPE_QUEST       = 5,
            TYPE_SPELL       = 6,
            TYPE_ACHIEVEMENT = 10

            CURSOR_HSPACE = 15,
            CURSOR_VSPACE = 15,

            _LANG = {
                loading: "加载...",
                noresponse: "未响应",
				notfound: " 未发现",
				invalid: "无效的 tooltip 数据"
            },
			
            LOOKUPS = {
                  1: [npcs,         "npc",         "NPC"        ],
                  2: [objects,      "object",      "Object"     ],
                  3: [items,        "item",        "Item"       ],
                  5: [quests,       "quest",       "Quest"      ],
                  6: [spells,       "spell",       "Spell"      ],
                 10: [achievements, "achievement", "Achievement"]
            },
			
            SCALING = {
 
            },
			
            LOCALES = {
                0: "enus",
            },

			EXPANSIONS = {
				0: "classic",
				1: "tbc",
				2: "wotlk",
				3: "cata",
				4: "mop",
				5: "wod"
			};

        if (external) {
            var Locale = { id: 0, name: "enus" };
        }

        function init()
		{
            if (external)
			{
                var script = document.createElement("script");
                script.src = $CoT.cdnUrl + "/api/global.js";
                headtag.appendChild(script);
            }
            else {
                attachEvent();
            }
        }

        function attachEvent() {
            if (eventAttached) {
                return;
            }

            eventAttached = true;
            $CoT.aE(document, "mouseover", onMouseOver);
        }

        function onDOMReady(func) {
            if (typeof jQuery != "undefined") {
                jQuery(func);
                return
            }
            /in/.test(document.readyState) ? setTimeout(onDOMReady.bind(null, func), 9) : func();
        }

        this.init = function () {
            if (external) {
                $CoT.ae(headtag, $CoT.ce("link", {
                    type: "text/css",
                    href: $CoT.cdnUrl + "/api/global.css",
                    rel:  "stylesheet"
                }));
            }
            attachEvent();

            onDOMReady(function () {
                if (typeof CoTTooltips != "undefined") {
                    for (var i = 0; i < document.links.length; i++) {
                        var link = document.links[i];
                        scanElement(link);
                    }
                }
            });
        };

        function updateCursorPos(e) {
            var pos = $CoT.getCursorPos(e);
            cursorX = pos.x;
            cursorY = pos.y;
        }

        function scanElement(t, e) {
            if (t.nodeName != "A" && t.nodeName != "AREA") {
                return -9999;
            }


            if (!t.href.length && !t.rel) {
                return;
            }

            if (t.rel && t.rel.indexOf("np") != -1 && t.rel.indexOf("np") != t.rel.indexOf("npc=")) {
                return;
            }

            var
                i0,
                i1,
                i2,
                url,
                params = {};

            currentParams = params;

            var p = function (url, k, v) {
                if (k == "buff" || k == "sock" || k == "map") {
                    params[k] = true;
                }
                else  if (k == "rand" || k == "ench" || k == "lvl" || k == "c" || k == "transmog") {
                    params[k] = parseInt(v);
                }
                else if (k == "gems" || k == "pcs" || k == "know") {
                    params[k] = v.split(":");
                }
                else if (k == "text") {
                    params[k] = true;
                }
            };

            if (method.type & 1) {
                i1 = 2;
                i2 = 3;
                if (t.href.indexOf("http://") == 0 || t.href.indexOf("https://") == 0) {
                    i0 = 1;
                    url = t.href.match(/^https?:\/\/(www|classic|tbc|wotlk|cata|mop|wod)?\.?cavernoftime\.com\/.?(item|quest|spell|achievement|npc|object)=([0-9]+)/);
                }
                else {
                    url = t.href.match(/()\?(item|quest|spell|achievement|npc|object)=([0-9]+)/);
                }
            }

            if (url == null && t.rel && (method.type & 2)) {
                i0 = 0;
                i1 = 1;
                i2 = 2;
                url = t.rel.match(/(item|quest|spell|achievement|npc|object).?([0-9]+)/);
            }

            t.href.replace(/([a-zA-Z]+)=?([a-zA-Z0-9:-]*)/g, p);
            if (t.rel) {
                t.rel.replace(/([a-zA-Z]+)=?([a-zA-Z0-9:-]*)/g, p);
            }

            if (params.gems && params.gems.length > 0) {
                var i;
                for (i = Math.min(3, params.gems.length - 1); i >= 0; --i) {
                    if (parseInt(params.gems[i])) {
                        break;
                    }
                }
                ++i;

                if (i == 0) {
                    delete params.gems;
                }
                else if (i < params.gems.length) {
                    params.gems = params.gems.slice(0, i);
                }
            }

            if (url) {
                var
                    locale,
                    domain = "www",
					expansion = "wotlk";

                currentA = t;
                if (i0 && url[i0]) {
                    domain = url[i0].split(".")[0];
                }

                locale = $CoT.getLocaleFromDomain(domain);
				expansion = $CoT.getExpansionFromDomain(domain);
				
                currentDomain = domain;
                if (t.href.indexOf("#") != -1 && document.location.href.indexOf(url[i1] + "=" + url[i2]) != -1) {
                    return;
                }

                mode = t.parentNode.className.indexOf("icon") == 0 && t.parentNode.nodeName == "DIV" ? 1 : 0;
                if (!t.onmouseout) {
                    if (mode == 0) {
                        t.onmousemove = onMouseMove;
                    }
                    t.onmouseout = onMouseOut;
                }

                if (e) {
                    updateCursorPos(e);
                }

                var
                    type = $CoT.getIdFromTypeName(url[i1]),
                    typeId = url[i2];

                display(type, typeId, locale, params, expansion);

               	if (e || typeof CoTTooltips == "undefined") {
                    return;
                }

                var data = LOOKUPS[type][0][expansion][getFullId(typeId, params)];

                var timeout = function (t) {
                    if (data.status[locale] != STATUS_OK && data.status[locale] != STATUS_NOTFOUND && data.status[locale] != STATUS_INVALID && data.status[locale] != STATUS_ERROR) {
                        window.setTimeout(function () {
                            timeout(t);
                        }, 5);
                        return;
                    }
					
					if (CoTTooltips.rename) {
						t.innerHTML = "";
					}
					
                    if (CoTTooltips.icons && data.icon && (type == TYPE_ITEM || type == TYPE_ACHIEVEMENT || type == TYPE_SPELL))
					{
						if(external) {
							t.className += " icontiny";
							if(CoTTooltips.iconsize) {
								t.innerHTML = t.innerHTML + '<img style="width: ' + CoTTooltips.iconsize + 'px; height: ' + CoTTooltips.iconsize + 'px;" src="//cdn.cavernoftime.com/' + domain + '/icons/tiny/' + data.icon.toLocaleLowerCase() + '.gif">';
							} else {
								t.innerHTML = t.innerHTML + '<img style="width:15px; height:15px;" src="//cdn.cavernoftime.com/' + domain + '/icons/tiny/' + data.icon.toLocaleLowerCase() + '.gif">';
							}
						} else {
							t.className += " icontinyl";
							t.style.background = "url(//cdn.cavernoftime.com/" + domain + "/icons/tiny/" + data.icon.toLocaleLowerCase() + ".gif)";
						}
                    }

                    if (CoTTooltips.rename)
					{
                        eval("name = data.name_" + LOCALES[locale]);
                        if (typeof name != "undefined" && name != "undefined")
						{
                            t.innerHTML = t.innerHTML + name;
                        }
                    }

                    if (CoTTooltips.qualitycolor)
					{
						
						if (type == TYPE_ITEM) {
							if(typeof CoTTooltips.overridecolor != "undefined" && typeof CoTTooltips.overridecolor.items != "undefined" && CoTTooltips.overridecolor.items) {
								t.style.color = CoTTooltips.overridecolor.items;
							} else {
								t.className += " q" + data.quality;
							}
						}
						
						if(typeof CoTTooltips.overridecolor != "undefined") {							
							if (type == TYPE_SPELL) {
								if(typeof CoTTooltips.overridecolor.spells != "undefined" && CoTTooltips.overridecolor.spells) {
									t.style.color = CoTTooltips.overridecolor.spells;
								}
							}
							
							if (type == TYPE_ACHIEVEMENT) {
								if(typeof CoTTooltips.overridecolor.achievements != "undefined" && CoTTooltips.overridecolor.achievements) {
									t.style.color = CoTTooltips.overridecolor.achievements;
								}
							}
							
							if (type == TYPE_QUEST) {
								if(typeof CoTTooltips.overridecolor.quests != "undefined" && CoTTooltips.overridecolor.quests) {
									t.style.color = CoTTooltips.overridecolor.quests;
								}
							}
							
							if (type == TYPE_NPC) {
								if(typeof CoTTooltips.overridecolor.npcs != "undefined" && CoTTooltips.overridecolor.npcs) {
									t.style.color = CoTTooltips.overridecolor.npcs;
								}
							}
							
							if (type == TYPE_OBJECT) {
								if(typeof CoTTooltips.overridecolor.objects != "undefined" && CoTTooltips.overridecolor.objects) {
									t.style.color = CoTTooltips.overridecolor.objects;
								}
							}
						}
                    }
                };
                timeout(t);
            }
        }

        function onMouseOver(a) {
            a = $CoT.evt(a);
            var t = a._target;
            var i = 0;
            while (t != null && i < 5 && scanElement(t, a) == -9999) {
                t = t.parentNode;
                ++i;
            }
        }

        function onMouseMove(a) {
            a = $CoT.evt(a);
            updateCursorPos(a);
            $CoT.Tooltip.move(cursorX, cursorY, 0, 0, CURSOR_HSPACE, CURSOR_VSPACE);
        }

        function onMouseOut() {
            currentType = null;
            currentA = null;
            $CoT.Tooltip.hide();
        }

        function getTooltipField(locale, n) {
            var prefix = "tooltip";

            if (currentParams && currentParams.buff) {
                prefix = "buff";
            }

            if (currentParams && currentParams.text) {
                prefix = "text";
            }

            if (currentParams && currentParams.premium) {
                prefix = "tooltip_premium";
            }

            return prefix + (n ? n : "") + "_" + LOCALES[locale];
        }

        function getIconField() {
            return (currentParams && currentParams.text) ? "text_icon" : "icon";
        }

        function getSpellsField(locale) {
            return (currentParams && currentParams.buff ? "buff" : "") + "spells_" + LOCALES[locale];
        }

        function initElement(type, id, locale, expansion) {
            var arr = LOOKUPS[type][0];
			
			if (arr[expansion] == null) {
                arr[expansion] = {};
            }

            if (arr[expansion][id] == null) {
                arr[expansion][id] = {};
            }

            if (arr[expansion][id].status == null) {
                arr[expansion][id].status = {};
            }

            if (arr[expansion][id].response == null) {
                arr[expansion][id].response = {};
            }

            if (arr[expansion][id].status[locale] == null) {
                arr[expansion][id].status[locale] = STATUS_NONE;
            }
        }

        function display(type, id, locale, params, expansion) {
            if (!params) {
                params = {};
            }

            var fullId    = getFullId(id, params);
            currentType   = type;
            currentId     = fullId;
            currentLocale = locale;
            currentParams = params;
			currentExpansion = expansion;

            initElement(type, fullId, locale, expansion);

            var arr = LOOKUPS[type][0];
			if(arr[expansion][fullId].status[locale] == STATUS_INVALID) {
				showTooltip(_LANG.invalid);
			}
			else if (arr[expansion][fullId].status[locale] == STATUS_OK || arr[expansion][fullId].status[locale] == STATUS_NOTFOUND) {
                showTooltip(arr[expansion][fullId][getTooltipField(locale)], arr[expansion][fullId][getIconField()], arr[expansion][fullId].map, arr[expansion][fullId][getSpellsField(locale)], arr[expansion][fullId][getTooltipField(locale, 2)], expansion);
            }
            else if (arr[expansion][fullId].status[locale] == STATUS_QUERYING || arr[expansion][fullId].status[locale] == STATUS_SCALES) {
                showTooltip(_LANG.loading);
            }
            else {
                request(type, id, locale, null, params, expansion);
            }
        }

        function request(type, id, locale, stealth, params, expansion) {
            var fullId = getFullId(id, params);
            var arr = LOOKUPS[type][0];

            if (arr[expansion][fullId].status[locale] != STATUS_NONE && arr[expansion][fullId].status[locale] != STATUS_ERROR) {
                return;
            }

            arr[expansion][fullId].status[locale] = STATUS_QUERYING;

            if (!stealth) {
                arr[expansion][fullId].timer = setTimeout(function () {
                    showLoading.apply(this, [type, fullId, locale, expansion]);
                }, 333);
            }

            var p = "";
            for (var i in params) {
                if (i != "rand" && i != "ench" && i != "gems" && i != "sock" && i != "lvl" && i != "pcs" && i != "transmog") {
                    continue;
                }
                if (typeof params[i] == "object") {
                    p += "&" + i + "=" + params[i].join(":");
                }
                else if (params[i] === true) {
                    p += "&" + i;
                }
                else {
                    p += "&" + i + "=" + params[i];
                }
            }

            var xpac = $CoT.getDomainFromExpansion(expansion);
			var	url = "//" + xpac + $CoT.expUrl + "/";

            $CoT.ajaxRequest(url + LOOKUPS[type][1] + "=" + id + "&power=true" + p);
        }

        function showTooltip(html, icon, map, spellData, html2, expansion) {
            if (currentA && currentA._fixTooltip) {
                html = currentA._fixTooltip(html, currentType, currentId, currentA);
            }

            var notFound = false;
            if (!html) {
                html = LOOKUPS[currentType][2] + _LANG.notfound;
                icon = "inv_misc_questionmark";
                notFound = true;
            }
            else {
                if (currentParams != null) {
					/*
                    if (currentParams.pcs && currentParams.pcs.length) {
                        var n = 0;
                        for (var i = 0, len = currentParams.pcs.length; i < len; ++i) {
                            var m;
                            if (m = html.match(new RegExp("<span><!--si([0-9]+:)*" + currentParams.pcs[i] + '(:[0-9]+)*--><a href="\\?item=(\\d+)">(.+?)</a></span>'))) {
                                html = html.replace(m[0], '<span class="q8"><!--si' + currentParams.pcs[i] + '--><a href="?item=' + m[3] + '">' + (($CoT.isset("itemData") && itemData[currentParams.pcs[i]]) ? itemData[currentParams.pcs[i]]["name_" + LOCALES[currentLocale]] : m[4]) + "</a></span>");
                                ++n;
                            }
                        }

                        if (n > 0) {
                            html = html.replace("(0/", "(" + n + "/");
                            html = html.replace(new RegExp("<span>\\(([0-" + n + "])\\)", "g"), '<span class="q2">($1)');
                        }
                    }*/
                    if (currentParams.c) {
                        html = html.replace(/<span class="c([0-9]+?)">(.+?)<\/span><br \/>/g, '<span class="c$1" style="display: none">$2</span>');
                        html = html.replace(new RegExp('<span class="c(' + currentParams.c + ')" style="display: none">(.+?)</span>', "g"), '<span class="c$1">$2</span><br />');
                    }
					/*
                    if (currentParams.know && currentParams.know.length) {
                        html = $CoT.setTooltipSpells(html, currentParams.know, spellData);
                    }

                    if (currentParams.lvl) {
                        html = $CoT.setTooltipLevel(html, currentParams.lvl, currentParams.buff);
                    }
                    else if ($CoT.gc('compare_level') && window.location.href.match(/\?compare/i)) {
                        html = $CoT.setTooltipLevel(html, $CoT.gc('compare_level'), currentParams.buff);
                    }
					*/
                }
            }

			/*
            if (currentParams.map && map && map.getMap) {
                html2 = map.getMap();
            }
			
			*/

            if (mode == 1) {
                $CoT.Tooltip.setIcon(null);
                $CoT.Tooltip.show(currentA, html, null, null, null, html2);
            }
            else {
                $CoT.Tooltip.setIcon(icon, expansion);
               	$CoT.Tooltip.showAtXY(html, cursorX, cursorY, CURSOR_HSPACE, CURSOR_VSPACE, html2);
            }
        }

        function showLoading(type, id, locale, expansion) {			
            if (currentType == type && currentId == id && currentLocale == locale && currentExpansion == expansion) {
                showTooltip(_LANG.loading);
                var arr = LOOKUPS[type][0];

                arr[expansion][id].timer = setTimeout(function () {
                    notFound.apply(this, [type, id, locale, expansion]);
                }, 3800);
            }
        }

        function notFound(type, id, locale, expansion) {
            var arr = LOOKUPS[type][0];
            arr[expansion][id].status[locale] = STATUS_ERROR;

            if (currentType == type && currentId == id && currentLocale == locale && currentExpansion == expansion) {
                showTooltip(_LANG.noresponse);
            }
        }

        function getFullId(id, params, expansion) {
            return id; //+ (params.rand ? "r" + params.rand : "") + (params.ench ? "e" + params.ench : "") + (params.gems ? "g" + params.gems.join(",") : "") + (params.sock ? "s" : "");
        }
		/*
        this.loadScales = function (type, locale, expansion) {
			if(expansion < EXPANSIONS.mop)
				return;
				
            var arr = LOOKUPS[type][0];
            for (var i in LOCALES) {
                if (locale == i || (!locale && !isNaN(i))) {
                    SCALING[type][i] = SCALES_LOADED;
                    for (var id in arr) {
                        if (arr[id].status[i, expansion] == STATUS_SCALES && arr[id].response[i]) {
                            arr[id].response[i]();
                        }
                    }
                }
            }
        };
		*/
        this.register = function (type, id, locale, json, expansion) {
            var arr = LOOKUPS[type][0];
			initElement(type, id, locale, expansion);

            if (SCALING[type] && SCALING[type][locale] != SCALES_LOADED) {
                arr[expansion][id].status[locale] = STATUS_SCALES;
                arr[expansion][id].response[locale] = this.register.bind(this, type, id, locale, json, expansion);
                return;
            }

            if (arr[expansion][id].timer) {
                clearTimeout(arr[expansion][id].timer);
                arr[expansion][id].timer = null;
            }

            if (!this.external && json.map) {
                if (arr[expansion][id].map == null) {
                    arr[expansion][id].map = new Mapper({
                        parent: $CoT.ce("div"),
                        zoom: 3,
                        zoomable: false,
                        buttons: false
                    });
                }
                arr[expansion][id].map.update(json.map);
                delete json.map;
            }


			/* TODO */
            $CoT.cO(arr[expansion][id], json);

            if (arr[expansion][id].status[locale] == STATUS_QUERYING || arr[expansion][id].status[locale] == STATUS_SCALES) {
                if (arr[expansion][id][getTooltipField(locale)]) {
                    arr[expansion][id].status[locale] = STATUS_OK;
                }
                else {
                    arr[expansion][id].status[locale] = STATUS_NOTFOUND;
                }
            }

            if (currentType == type && id == currentId && currentLocale == locale && currentExpansion == expansion) {
                showTooltip(arr[expansion][id][getTooltipField(locale)], arr[expansion][id].icon, arr[expansion][id].map, arr[expansion][id][getSpellsField(locale)], arr[expansion][id][getTooltipField(locale, 2)], expansion);
            }
        };

        this.regNpc = function (id, locale, json, expansion) {
            this.register(TYPE_NPC, id, locale, json, expansion);
        };

        this.regObject = function (id, locale, json, expansion) {
            this.register(TYPE_OBJECT, id, locale, json, expansion);
        };

        this.regItem = function (id, locale, json, expansion) {
            this.register(TYPE_ITEM, id, locale, json, expansion);
        };

        this.regQuest = function (id, locale, json, expansion) {
            this.register(TYPE_QUEST, id, locale, json, expansion);
        };

        this.regSpell = function (id, locale, json, expansion) {
            this.register(TYPE_SPELL, id, locale, json, expansion);
        };

        this.regAchievement = function (id, locale, json, expansion) {
            this.register(TYPE_ACHIEVEMENT, id, locale, json, expansion);
        };
		
		this.displayTextTooltip = function(text) {
			showTooltip(text);
		}

        this.displayTooltip = function (type, id, locale, params, expansion) {
            display(type, id, locale, params, expansion);
        };

        this.getStatus = function (type, id, locale) {
            var arr = LOOKUPS[type][0];
            if (arr[id] != null) {
                return arr[id].status[locale];
            }
            else {
                return STATUS_NONE;
            }
        };
		
        this.refreshLinks = function () {
            if (typeof CoTTooltips != "undefined") {
                for (var i = 0; i < document.links.length; i++) {
                    var link = document.links[i];
                    var node = link.parentNode;
                    var isTooltipChild = false;

                    while (node != null) {
                        if ((" " + node.className + " ").replace(/[\n\t]/g, " ").indexOf(" cavernoftime-tt ") > -1) {
                            isTooltipChild = true;
                            break;
                        }
                        node = node.parentNode
                    }

                    if (!isTooltipChild) {
                        scanElement(link);
                    }
                }
            }

            this.hideTooltip();
        };

        this.setParent = function (newParent) {
            $CoT.Tooltip.reset();
            $CoT.Tooltip.prepare(newParent);
        };

        if (external) {
            this.set = function (foo) {
                $CoT.cO(method, foo);
            };

            this.showTooltip = function (e, tooltip, icon, expansion) {
                updateCursorPos(e);
                showTooltip(tooltip, icon, null, null, null, expansion);
            };

            this.hideTooltip = function () {
                $CoT.Tooltip.hide();
            };

            this.moveTooltip = function (e) {
                onMouseMove(e);
            }
        }

        init();
    }
};

if(typeof $utilGrp == "undefined") {
	$utilGrp = $CoTTooltip;
}