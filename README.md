# Illyrian WP
ClickJacking plugin in every element you want.

<br />

Used Libraries:
<ul>
    <li><a href="https://github.com/sindresorhus/devtools-detect">devtools-detect</a> <i>Detect if DevTools is open and its orientation</i></li>
    <li><a href="https://github.com/zswang/jdetects">jdetects</a> <i>Detect if DevTools is open</i></li> 
    <li><a href="https://gist.github.com/thoov/984751">cookie</a> <i>Javascript Cookie Functions</i></li>
    <li><a href="https://github.com/vincepare/iframeTracker-jquery">iframeTracker-jquery</a> <i>jQuery Plugin to track click on iframes</i></li>
</ul>

<br />

See this links:
<ul>
<li><a href="http://javascript-array.com/scripts/window_open/">Open popup window with open.window method</a>
</ul>

<br />

See this javascript functions in InnerAds.php **function VentanaEmerg()** of ForoInner:
    
    function f_open_window_max() {
        var wOpen;
        var sOptions;

        sOptions = 'status=yes,menubar=no,scrollbars=yes,resizable=yes,toolbar=no';
        sOptions = sOptions + ',width=' + (screen.availWidth - 10).toString();
        sOptions = sOptions + ',height=' + (screen.availHeight - 122).toString();
        sOptions = sOptions + ',screenX=0,screenY=0,left=0,top=0';

        wOpen = window.open(window.location + '#iv', '', sOptions);

        wOpen.moveTo(0, 0);
        wOpen.resizeTo(screen.availWidth, screen.availHeight);
        wOpen.focus();

        return wOpen;
    }
