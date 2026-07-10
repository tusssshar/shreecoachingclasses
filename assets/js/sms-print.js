/* ============================================================
 *  Shared print / nav helpers
 *  ------------------------------------------------------------
 *  All helpers are attached to window.SMS so views don't have to
 *  re-implement URL building / print-window opening.
 * ============================================================ */
(function (w) {
    'use strict';

    var SMS = w.SMS || {};

    /**
     * Builds an admin URL. baseRoute is the route (e.g. "admin/report_teachers/print");
     * params is a plain object whose keys/values are URL-encoded into the query string.
     *
     * Example:
     *   SMS.adminUrl('admin/report_teachers/print', { q: 'sakshi & co.' })
     *   -> "http://.../index.php?admin/report_teachers/print&q=sakshi%20%26%20co."
     */
    SMS.adminUrl = function (baseRoute, params) {
        var root = (w.SMS_BASE_URL || (document.querySelector('base') && document.querySelector('base').href) || '/');
        if (root.slice(-1) !== '/') root += '/';
        var url = root + 'index.php?' + baseRoute;
        if (params && typeof params === 'object') {
            var parts = [];
            for (var k in params) {
                if (!Object.prototype.hasOwnProperty.call(params, k)) continue;
                var v = params[k];
                if (v === null || typeof v === 'undefined' || v === '') continue;
                parts.push(encodeURIComponent(k) + '=' + encodeURIComponent(v));
            }
            if (parts.length) url += '&' + parts.join('&');
        }
        return url;
    };

    /**
     * Navigates the current tab to an admin URL. Use for search-form Submit buttons
     * (so we can drop the buggy `<input type="hidden" name="admin/foo">` pattern that
     * produced "admin/foo=" in the URI and triggered CI's permitted_uri_chars block).
     */
    SMS.go = function (baseRoute, params) {
        w.location.href = SMS.adminUrl(baseRoute, params);
    };

    /**
     * Opens a print-friendly view in a new window and triggers print after load.
     * Wraps window.open + a setTimeout fallback in case the target page itself
     * doesn't auto-print. Safe to call repeatedly — the target window is reused.
     */
    SMS.openPrintView = function (baseRoute, params, opts) {
        opts = opts || {};
        var url    = SMS.adminUrl(baseRoute, params);
        var name   = opts.windowName || 'sms_print_view';
        var feat   = opts.features   || 'width=1100,height=820,scrollbars=1,resizable=1';
        var winRef = w.open(url, name, feat);
        if (!winRef) {
            // popup blocked — fall back to same-tab
            w.location.href = url;
            return null;
        }
        winRef.focus();
        return winRef;
    };

    /**
     * Prints the current document. Used by the buttons embedded inside
     * already-loaded print views.
     */
    SMS.printNow = function () { w.print(); };

    w.SMS = SMS;
})(window);
