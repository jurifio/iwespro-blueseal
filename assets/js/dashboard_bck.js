/**
 * Created by Fabrizio Marconi on 12/04/2016.
 */

 // Widget 13
 $('.widget-13-map').mapplic({
 source: '/assets/charts/map.json',
 height: 438,
 sidebar: false,
 minimap: false,
 locations: true,
 deeplinking: true,
 fullscreen: false,
 developer: false,
 maxscale: 3
 });

 // Disable scroll to zoom
 setTimeout(function() {
 $('.mapplic-layer').unbind('mousewheel DOMMouseScroll');
 }, 1000);

 setInterval(function() {
 var hash = ["#usa","#af","#ru"];
 window.location.hash = hash[Math.floor(Math.random() * 3)];
 }, 3000);
