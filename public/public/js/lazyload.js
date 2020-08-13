"use strict";
window.LazyLoad = function(n) {
    var t, e = {},
    c = 0,
    a = {
        js: []
    },
    l = {
        async: n.createElement("script").async === !0
    },
    r = function(t, e) {
        var c = n.createElement("script");
        return c.src = e,
        c.async = !1,
        c
    },
    o = function(n) {
        var t, l, r = e[n];
        r && (t = r.callback, l = r.urls, l.shift(), c = 0, l.length || (t && t.call(r.context, r.obj), e[n] = null, a[n].length && i(n)))
    },
    u = function(n, t, e, c, r) {
        if (n) if ("string" == typeof n && (n = [n]), n = n.filter(function(n) {
            return n
        }), l.async) a[t].push({
            urls: n,
            callback: e,
            obj: c,
            context: r
        });
        else for (var o = 0,
        u = n.length; u > o; ++o) a[t].push({
            urls: [n[o]],
            callback: o === u - 1 ? e: null,
            obj: c,
            context: r
        })
    },
    s = function(n, t, e, c) {
        t.onload = t.onerror = function() {
            o(n)
        }
    },
    i = function(c, l, o, i, f) {
        var h, p, d, y, g, b = [];
        if (u(l, c, o, i, f), !e[c] && (d = e[c] = a[c].shift())) {
            for (t || (t = n.head || n.getElementsByTagName("head")[0]), y = d.urls.concat(), h = 0, p = y.length; p > h; ++h) {
                g = y[h];
                var m = r(c, g);
                m.className = "lazyload",
                m.setAttribute("charset", "utf-8"),
                s(c, m, g, d.urls[h]),
                b.push(m)
            }
            var v = function(n, e) {
                setTimeout(function() {
                    t.appendChild(n)
                },
                e)
            };
            for (h = 0, p = b.length; p > h; ++h) v(b[h], h + 2)
        }
    };
    return function(n) {
        "js" === n && i.apply(null, arguments)
    }
} (document);