/*!	jdetect */
!function (e) {
    function n(e) {
        function n() {
            return u
        }

        function o() {
            return window.Firebug && window.Firebug.chrome && window.Firebug.chrome.isInitialized ? void i("on") : (c = "off", console.log(f), console.clear(), void i(c))
        }

        function i(e) {
            u !== e && (u = e, "function" == typeof d.onchange && d.onchange(e))
        }

        function t() {
            w || (w = !0, window.removeEventListener("resize", o), clearInterval(a))
        }

        "function" == typeof e && (e = {onchange: e}), e = e || {};
        var r = e.delay || 1e3, d = {};
        d.onchange = e.onchange;
        var c, f = new Image;
        f.__defineGetter__("id", function () {
            c = "on"
        });
        var u = "unknown";
        d.getStatus = n;
        var a = setInterval(o, r);
        window.addEventListener("resize", o);
        var w;
        return d.free = t, d
    }

    var o = o || {};
    o.create = n, "function" == typeof define ? (define.amd || define.cmd) && define(function () {
        return o
    }) : "undefined" != typeof module && module.exports ? module.exports = o : window[e] = o
}("jdetects");
