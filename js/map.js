/*
 Highmaps JS v5.0.10 (2017-03-31)
 Highmaps as a plugin for Highcharts 4.1.x or Highstock 2.1.x (x being the patch version of this file)

 (c) 2011-2017 Torstein Honsi

 License: www.highcharts.com/license
*/
(function(y){"object"===typeof module&&module.exports?module.exports=y:y(Highcharts)})(function(y){(function(a){var m=a.Axis,p=a.each,l=a.pick;a=a.wrap;a(m.prototype,"getSeriesExtremes",function(a){var e=this.isXAxis,w,m,v=[],d;e&&p(this.series,function(b,a){b.useMapGeometry&&(v[a]=b.xData,b.xData=[])});a.call(this);e&&(w=l(this.dataMin,Number.MAX_VALUE),m=l(this.dataMax,-Number.MAX_VALUE),p(this.series,function(b,a){b.useMapGeometry&&(w=Math.min(w,l(b.minX,w)),m=Math.max(m,l(b.maxX,w)),b.xData=v[a],
d=!0)}),d&&(this.dataMin=w,this.dataMax=m))});a(m.prototype,"setAxisTranslation",function(a){var q=this.chart,e=q.plotWidth/q.plotHeight,q=q.xAxis[0],l;a.call(this);"yAxis"===this.coll&&void 0!==q.transA&&p(this.series,function(a){a.preserveAspectRatio&&(l=!0)});if(l&&(this.transA=q.transA=Math.min(this.transA,q.transA),a=e/((q.max-q.min)/(this.max-this.min)),a=1>a?this:q,e=(a.max-a.min)*a.transA,a.pixelPadding=a.len-e,a.minPixelPadding=a.pixelPadding/2,e=a.fixTo)){e=e[1]-a.toValue(e[0],!0);e*=a.transA;
if(Math.abs(e)>a.minPixelPadding||a.min===a.dataMin&&a.max===a.dataMax)e=0;a.minPixelPadding-=e}});a(m.prototype,"render",function(a){a.call(this);this.fixTo=null})})(y);(function(a){var m=a.Axis,p=a.Chart,l=a.color,e,q=a.each,w=a.extend,x=a.isNumber,v=a.Legend,d=a.LegendSymbolMixin,b=a.noop,c=a.merge,g=a.pick,t=a.wrap;e=a.ColorAxis=function(){this.init.apply(this,arguments)};w(e.prototype,m.prototype);w(e.prototype,{defaultColorAxisOptions:{lineWidth:0,minPadding:0,maxPadding:0,gridLineWidth:1,tickPixelInterval:72,
startOnTick:!0,endOnTick:!0,offset:0,marker:{animation:{duration:50},width:.01,color:"#999999"},labels:{overflow:"justify",rotation:0},minColor:"#e6ebf5",maxColor:"#003399",tickLength:5,showInLegend:!0},keepProps:["legendGroup","legendItemHeight","legendItemWidth","legendItem","legendSymbol"].concat(m.prototype.keepProps),init:function(a,f){var b="vertical"!==a.options.legend.layout,k;this.coll="colorAxis";k=c(this.defaultColorAxisOptions,{side:b?2:1,reversed:!b},f,{opposite:!b,showEmpty:!1,title:null});
m.prototype.init.call(this,a,k);f.dataClasses&&this.initDataClasses(f);this.initStops(f);this.horiz=b;this.zoomEnabled=!1;this.defaultLegendLength=200},tweenColors:function(a,f,b){var k;f.rgba.length&&a.rgba.length?(a=a.rgba,f=f.rgba,k=1!==f[3]||1!==a[3],a=(k?"rgba(":"rgb(")+Math.round(f[0]+(a[0]-f[0])*(1-b))+","+Math.round(f[1]+(a[1]-f[1])*(1-b))+","+Math.round(f[2]+(a[2]-f[2])*(1-b))+(k?","+(f[3]+(a[3]-f[3])*(1-b)):"")+")"):a=f.input||"none";return a},initDataClasses:function(a){var b=this,k=this.chart,
r,h=0,u=k.options.chart.colorCount,g=this.options,d=a.dataClasses.length;this.dataClasses=r=[];this.legendItems=[];q(a.dataClasses,function(a,f){a=c(a);r.push(a);a.color||("category"===g.dataClassColor?(f=k.options.colors,u=f.length,a.color=f[h],a.colorIndex=h,h++,h===u&&(h=0)):a.color=b.tweenColors(l(g.minColor),l(g.maxColor),2>d?.5:f/(d-1)))})},initStops:function(a){this.stops=a.stops||[[0,this.options.minColor],[1,this.options.maxColor]];q(this.stops,function(a){a.color=l(a[1])})},setOptions:function(a){m.prototype.setOptions.call(this,
a);this.options.crosshair=this.options.marker},setAxisSize:function(){var a=this.legendSymbol,b=this.chart,n=b.options.legend||{},c,h;a?(this.left=n=a.attr("x"),this.top=c=a.attr("y"),this.width=h=a.attr("width"),this.height=a=a.attr("height"),this.right=b.chartWidth-n-h,this.bottom=b.chartHeight-c-a,this.len=this.horiz?h:a,this.pos=this.horiz?n:c):this.len=(this.horiz?n.symbolWidth:n.symbolHeight)||this.defaultLegendLength},toColor:function(a,b){var f=this.stops,k,h,c=this.dataClasses,g,d;if(c)for(d=
c.length;d--;){if(g=c[d],k=g.from,f=g.to,(void 0===k||a>=k)&&(void 0===f||a<=f)){h=g.color;b&&(b.dataClass=d,b.colorIndex=g.colorIndex);break}}else{this.isLog&&(a=this.val2lin(a));a=1-(this.max-a)/(this.max-this.min||1);for(d=f.length;d--&&!(a>f[d][0]););k=f[d]||f[d+1];f=f[d+1]||k;a=1-(f[0]-a)/(f[0]-k[0]||1);h=this.tweenColors(k.color,f.color,a)}return h},getOffset:function(){var a=this.legendGroup,b=this.chart.axisOffset[this.side];a&&(this.axisParent=a,m.prototype.getOffset.call(this),this.added||
(this.added=!0,this.labelLeft=0,this.labelRight=this.width),this.chart.axisOffset[this.side]=b)},setLegendColor:function(){var a,b=this.options,n=this.reversed;a=n?1:0;n=n?0:1;a=this.horiz?[a,0,n,0]:[0,n,0,a];this.legendColor={linearGradient:{x1:a[0],y1:a[1],x2:a[2],y2:a[3]},stops:b.stops||[[0,b.minColor],[1,b.maxColor]]}},drawLegendSymbol:function(a,b){var f=a.padding,k=a.options,h=this.horiz,c=g(k.symbolWidth,h?this.defaultLegendLength:12),d=g(k.symbolHeight,h?12:this.defaultLegendLength),e=g(k.labelPadding,
h?16:30),k=g(k.itemDistance,10);this.setLegendColor();b.legendSymbol=this.chart.renderer.rect(0,a.baseline-11,c,d).attr({zIndex:1}).add(b.legendGroup);this.legendItemWidth=c+f+(h?k:e);this.legendItemHeight=d+f+(h?e:0)},setState:b,visible:!0,setVisible:b,getSeriesExtremes:function(){var a=this.series,b=a.length;this.dataMin=Infinity;for(this.dataMax=-Infinity;b--;)void 0!==a[b].valueMin&&(this.dataMin=Math.min(this.dataMin,a[b].valueMin),this.dataMax=Math.max(this.dataMax,a[b].valueMax))},drawCrosshair:function(a,
b){var f=b&&b.plotX,c=b&&b.plotY,h,k=this.pos,d=this.len;b&&(h=this.toPixels(b[b.series.colorKey]),h<k?h=k-2:h>k+d&&(h=k+d+2),b.plotX=h,b.plotY=this.len-h,m.prototype.drawCrosshair.call(this,a,b),b.plotX=f,b.plotY=c,this.cross&&(this.cross.addClass("highcharts-coloraxis-marker").add(this.legendGroup),this.cross.attr({fill:this.crosshair.color})))},getPlotLinePath:function(a,b,c,d,h){return x(h)?this.horiz?["M",h-4,this.top-6,"L",h+4,this.top-6,h,this.top,"Z"]:["M",this.left,h,"L",this.left-6,h+6,
this.left-6,h-6,"Z"]:m.prototype.getPlotLinePath.call(this,a,b,c,d)},update:function(a,b){var f=this.chart,k=f.legend;q(this.series,function(a){a.isDirtyData=!0});a.dataClasses&&k.allItems&&(q(k.allItems,function(a){a.isDataClass&&a.legendGroup.destroy()}),f.isDirtyLegend=!0);f.options[this.coll]=c(this.userOptions,a);m.prototype.update.call(this,a,b);this.legendItem&&(this.setLegendColor(),k.colorizeItem(this,!0))},getDataClassLegendSymbols:function(){var c=this,f=this.chart,n=this.legendItems,g=
f.options.legend,h=g.valueDecimals,e=g.valueSuffix||"",t;n.length||q(this.dataClasses,function(k,g){var u=!0,r=k.from,z=k.to;t="";void 0===r?t="\x3c ":void 0===z&&(t="\x3e ");void 0!==r&&(t+=a.numberFormat(r,h)+e);void 0!==r&&void 0!==z&&(t+=" - ");void 0!==z&&(t+=a.numberFormat(z,h)+e);n.push(w({chart:f,name:t,options:{},drawLegendSymbol:d.drawRectangle,visible:!0,setState:b,isDataClass:!0,setVisible:function(){u=this.visible=!u;q(c.series,function(a){q(a.points,function(a){a.dataClass===g&&a.setVisible(u)})});
f.legend.colorizeItem(this,u)}},k))});return n},name:""});q(["fill","stroke"],function(b){a.Fx.prototype[b+"Setter"]=function(){this.elem.attr(b,e.prototype.tweenColors(l(this.start),l(this.end),this.pos),null,!0)}});t(p.prototype,"getAxes",function(a){var b=this.options.colorAxis;a.call(this);this.colorAxis=[];b&&new e(this,b)});t(v.prototype,"getAllItems",function(a){var b=[],c=this.chart.colorAxis[0];c&&c.options&&(c.options.showInLegend&&(c.options.dataClasses?b=b.concat(c.getDataClassLegendSymbols()):
b.push(c)),q(c.series,function(a){a.options.showInLegend=!1}));return b.concat(a.call(this))});t(v.prototype,"colorizeItem",function(a,b,c){a.call(this,b,c);c&&b.legendColor&&b.legendSymbol.attr({fill:b.legendColor})})})(y);(function(a){var m=a.defined,p=a.each,l=a.noop,e=a.seriesTypes;a.colorPointMixin={isValid:function(){return null!==this.value},setVisible:function(a){var e=this,q=a?"show":"hide";p(["graphic","dataLabel"],function(a){if(e[a])e[a][q]()})},setState:function(e){a.Point.prototype.setState.call(this,
e);this.graphic&&this.graphic.attr({zIndex:"hover"===e?1:0})}};a.colorSeriesMixin={pointArrayMap:["value"],axisTypes:["xAxis","yAxis","colorAxis"],optionalAxis:"colorAxis",trackerGroups:["group","markerGroup","dataLabelsGroup"],getSymbol:l,parallelArrays:["x","y","value"],colorKey:"value",pointAttribs:e.column.prototype.pointAttribs,translateColors:function(){var a=this,e=this.options.nullColor,l=this.colorAxis,m=this.colorKey;p(this.data,function(d){var b=d[m];if(b=d.options.color||(d.isNull?e:l&&
void 0!==b?l.toColor(b,d):d.color||a.color))d.color=b})},colorAttribs:function(a){var e={};m(a.color)&&(e[this.colorProp||"fill"]=a.color);return e}}})(y);(function(a){function m(a){a&&(a.preventDefault&&a.preventDefault(),a.stopPropagation&&a.stopPropagation(),a.cancelBubble=!0)}function p(a){this.init(a)}var l=a.addEvent,e=a.Chart,q=a.doc,w=a.each,x=a.extend,v=a.merge,d=a.pick;a=a.wrap;p.prototype.init=function(a){this.chart=a;a.mapNavButtons=[]};p.prototype.update=function(a){var b=this.chart,
g=b.options.mapNavigation,e,k,f,n,r,h=function(a){this.handler.call(b,a);m(a)},u=b.mapNavButtons;a&&(g=b.options.mapNavigation=v(b.options.mapNavigation,a));for(;u.length;)u.pop().destroy();if(d(g.enableButtons,g.enabled)&&!b.renderer.forExport)for(e in a=g.buttons,a)a.hasOwnProperty(e)&&(f=v(g.buttonOptions,a[e]),k=f.theme,k.style=v(f.theme.style,f.style),r=(n=k.states)&&n.hover,n=n&&n.select,k=b.renderer.button(f.text,0,0,h,k,r,n,0,"zoomIn"===e?"topbutton":"bottombutton").addClass("highcharts-map-navigation").attr({width:f.width,
height:f.height,title:b.options.lang[e],padding:f.padding,zIndex:5}).add(),k.handler=f.onclick,k.align(x(f,{width:k.width,height:2*k.height}),null,f.alignTo),l(k.element,"dblclick",m),u.push(k));this.updateEvents(g)};p.prototype.updateEvents=function(a){var b=this.chart;d(a.enableDoubleClickZoom,a.enabled)||a.enableDoubleClickZoomTo?this.unbindDblClick=this.unbindDblClick||l(b.container,"dblclick",function(a){b.pointer.onContainerDblClick(a)}):this.unbindDblClick&&(this.unbindDblClick=this.unbindDblClick());
d(a.enableMouseWheelZoom,a.enabled)?this.unbindMouseWheel=this.unbindMouseWheel||l(b.container,void 0===q.onmousewheel?"DOMMouseScroll":"mousewheel",function(a){b.pointer.onContainerMouseWheel(a);m(a);return!1}):this.unbindMouseWheel&&(this.unbindMouseWheel=this.unbindMouseWheel())};x(e.prototype,{fitToBox:function(a,c){w([["x","width"],["y","height"]],function(b){var d=b[0];b=b[1];a[d]+a[b]>c[d]+c[b]&&(a[b]>c[b]?(a[b]=c[b],a[d]=c[d]):a[d]=c[d]+c[b]-a[b]);a[b]>c[b]&&(a[b]=c[b]);a[d]<c[d]&&(a[d]=c[d])});
return a},mapZoom:function(a,c,e,q,k){var b=this.xAxis[0],n=b.max-b.min,g=d(c,b.min+n/2),h=n*a,n=this.yAxis[0],u=n.max-n.min,z=d(e,n.min+u/2),u=u*a,g=this.fitToBox({x:g-h*(q?(q-b.pos)/b.len:.5),y:z-u*(k?(k-n.pos)/n.len:.5),width:h,height:u},{x:b.dataMin,y:n.dataMin,width:b.dataMax-b.dataMin,height:n.dataMax-n.dataMin}),h=g.x<=b.dataMin&&g.width>=b.dataMax-b.dataMin&&g.y<=n.dataMin&&g.height>=n.dataMax-n.dataMin;q&&(b.fixTo=[q-b.pos,c]);k&&(n.fixTo=[k-n.pos,e]);void 0===a||h?(b.setExtremes(void 0,
void 0,!1),n.setExtremes(void 0,void 0,!1)):(b.setExtremes(g.x,g.x+g.width,!1),n.setExtremes(g.y,g.y+g.height,!1));this.redraw()}});a(e.prototype,"render",function(a){this.mapNavigation=new p(this);this.mapNavigation.update();a.call(this)})})(y);(function(a){var m=a.extend,p=a.pick,l=a.Pointer;a=a.wrap;m(l.prototype,{onContainerDblClick:function(a){var e=this.chart;a=this.normalize(a);e.options.mapNavigation.enableDoubleClickZoomTo?e.pointer.inClass(a.target,"highcharts-tracker")&&e.hoverPoint&&e.hoverPoint.zoomTo():
e.isInsidePlot(a.chartX-e.plotLeft,a.chartY-e.plotTop)&&e.mapZoom(.5,e.xAxis[0].toValue(a.chartX),e.yAxis[0].toValue(a.chartY),a.chartX,a.chartY)},onContainerMouseWheel:function(a){var e=this.chart,l;a=this.normalize(a);l=a.detail||-(a.wheelDelta/120);e.isInsidePlot(a.chartX-e.plotLeft,a.chartY-e.plotTop)&&e.mapZoom(Math.pow(e.options.mapNavigation.mouseWheelSensitivity,l),e.xAxis[0].toValue(a.chartX),e.yAxis[0].toValue(a.chartY),a.chartX,a.chartY)}});a(l.prototype,"zoomOption",function(a){var e=
this.chart.options.mapNavigation;p(e.enableTouchZoom,e.enabled)&&(this.chart.options.chart.pinchType="xy");a.apply(this,[].slice.call(arguments,1))});a(l.prototype,"pinchTranslate",function(a,l,m,p,v,d,b){a.call(this,l,m,p,v,d,b);"map"===this.chart.options.chart.type&&this.hasZoom&&(a=p.scaleX>p.scaleY,this.pinchTranslateDirection(!a,l,m,p,v,d,b,a?p.scaleX:p.scaleY))})})(y);(function(a){var m=a.color,p=a.ColorAxis,l=a.colorPointMixin,e=a.each,q=a.extend,w=a.isNumber,x=a.map,v=a.merge,d=a.noop,b=a.pick,
c=a.isArray,g=a.Point,t=a.Series,k=a.seriesType,f=a.seriesTypes,n=a.splat,r=void 0!==a.doc.documentElement.style.vectorEffect;k("map","scatter",{allAreas:!0,animation:!1,nullColor:"#f7f7f7",borderColor:"#cccccc",borderWidth:1,marker:null,stickyTracking:!1,joinBy:"hc-key",dataLabels:{formatter:function(){return this.point.value},inside:!0,verticalAlign:"middle",crop:!1,overflow:!1,padding:0},turboThreshold:0,tooltip:{followPointer:!0,pointFormat:"{point.name}: {point.value}\x3cbr/\x3e"},states:{normal:{animation:!0},
hover:{brightness:.2,halo:null},select:{color:"#cccccc"}}},v(a.colorSeriesMixin,{type:"map",supportsDrilldown:!0,getExtremesFromAll:!0,useMapGeometry:!0,forceDL:!0,searchPoint:d,directTouch:!0,preserveAspectRatio:!0,pointArrayMap:["value"],getBox:function(h){var c=Number.MAX_VALUE,f=-c,d=c,k=-c,n=c,g=c,l=this.xAxis,t=this.yAxis,r;e(h||[],function(h){if(h.path){"string"===typeof h.path&&(h.path=a.splitPath(h.path));var e=h.path||[],u=e.length,l=!1,t=-c,z=c,q=-c,m=c,p=h.properties;if(!h._foundBox){for(;u--;)w(e[u])&&
(l?(t=Math.max(t,e[u]),z=Math.min(z,e[u])):(q=Math.max(q,e[u]),m=Math.min(m,e[u])),l=!l);h._midX=z+(t-z)*(h.middleX||p&&p["hc-middle-x"]||.5);h._midY=m+(q-m)*(h.middleY||p&&p["hc-middle-y"]||.5);h._maxX=t;h._minX=z;h._maxY=q;h._minY=m;h.labelrank=b(h.labelrank,(t-z)*(q-m));h._foundBox=!0}f=Math.max(f,h._maxX);d=Math.min(d,h._minX);k=Math.max(k,h._maxY);n=Math.min(n,h._minY);g=Math.min(h._maxX-h._minX,h._maxY-h._minY,g);r=!0}});r&&(this.minY=Math.min(n,b(this.minY,c)),this.maxY=Math.max(k,b(this.maxY,
-c)),this.minX=Math.min(d,b(this.minX,c)),this.maxX=Math.max(f,b(this.maxX,-c)),l&&void 0===l.options.minRange&&(l.minRange=Math.min(5*g,(this.maxX-this.minX)/5,l.minRange||c)),t&&void 0===t.options.minRange&&(t.minRange=Math.min(5*g,(this.maxY-this.minY)/5,t.minRange||c)))},getExtremes:function(){t.prototype.getExtremes.call(this,this.valueData);this.chart.hasRendered&&this.isDirtyData&&this.getBox(this.options.data);this.valueMin=this.dataMin;this.valueMax=this.dataMax;this.dataMin=this.minY;this.dataMax=
this.maxY},translatePath:function(a){var b=!1,h=this.xAxis,c=this.yAxis,f=h.min,d=h.transA,h=h.minPixelPadding,k=c.min,e=c.transA,c=c.minPixelPadding,n,g=[];if(a)for(n=a.length;n--;)w(a[n])?(g[n]=b?(a[n]-f)*d+h:(a[n]-k)*e+c,b=!b):g[n]=a[n];return g},setData:function(b,f,d,k){var h=this.options,g=this.chart.options.chart,l=g&&g.map,u=h.mapData,r=h.joinBy,z=null===r,q=h.keys||this.pointArrayMap,m=[],p={},A,B=this.chart.mapTransforms;!u&&l&&(u="string"===typeof l?a.maps[l]:l);z&&(r="_i");r=this.joinBy=
n(r);r[1]||(r[1]=r[0]);b&&e(b,function(a,f){var d=0;if(w(a))b[f]={value:a};else if(c(a)){b[f]={};!h.keys&&a.length>q.length&&"string"===typeof a[0]&&(b[f]["hc-key"]=a[0],++d);for(var k=0;k<q.length;++k,++d)q[k]&&(b[f][q[k]]=a[d])}z&&(b[f]._i=f)});this.getBox(b);if(this.chart.mapTransforms=B=g&&g.mapTransforms||u&&u["hc-transform"]||B)for(A in B)B.hasOwnProperty(A)&&A.rotation&&(A.cosAngle=Math.cos(A.rotation),A.sinAngle=Math.sin(A.rotation));if(u){"FeatureCollection"===u.type&&(this.mapTitle=u.title,
u=a.geojson(u,this.type,this));this.mapData=u;this.mapMap={};for(A=0;A<u.length;A++)g=u[A],l=g.properties,g._i=A,r[0]&&l&&l[r[0]]&&(g[r[0]]=l[r[0]]),p[g[r[0]]]=g;this.mapMap=p;b&&r[1]&&e(b,function(a){p[a[r[1]]]&&m.push(p[a[r[1]]])});h.allAreas?(this.getBox(u),b=b||[],r[1]&&e(b,function(a){m.push(a[r[1]])}),m="|"+x(m,function(a){return a&&a[r[0]]}).join("|")+"|",e(u,function(a){r[0]&&-1!==m.indexOf("|"+a[r[0]]+"|")||(b.push(v(a,{value:null})),k=!1)})):this.getBox(m)}t.prototype.setData.call(this,
b,f,d,k)},drawGraph:d,drawDataLabels:d,doFullTranslate:function(){return this.isDirtyData||this.chart.isResizing||this.chart.renderer.isVML||!this.baseTrans},translate:function(){var a=this,b=a.xAxis,c=a.yAxis,f=a.doFullTranslate();a.generatePoints();e(a.data,function(h){h.plotX=b.toPixels(h._midX,!0);h.plotY=c.toPixels(h._midY,!0);f&&(h.shapeType="path",h.shapeArgs={d:a.translatePath(h.path)})});a.translateColors()},pointAttribs:function(a,b){b=f.column.prototype.pointAttribs.call(this,a,b);a.isFading&&
delete b.fill;r?b["vector-effect"]="non-scaling-stroke":b["stroke-width"]="inherit";return b},drawPoints:function(){var a=this,b=a.xAxis,c=a.yAxis,d=a.group,k=a.chart,g=k.renderer,n,l,t,m,q=this.baseTrans,p,w,v,x,y;a.transformGroup||(a.transformGroup=g.g().attr({scaleX:1,scaleY:1}).add(d),a.transformGroup.survive=!0);a.doFullTranslate()?(k.hasRendered&&e(a.points,function(b){b.shapeArgs&&(b.shapeArgs.fill=a.pointAttribs(b,b.state).fill)}),a.group=a.transformGroup,f.column.prototype.drawPoints.apply(a),
a.group=d,e(a.points,function(a){a.graphic&&(a.name&&a.graphic.addClass("highcharts-name-"+a.name.replace(/ /g,"-").toLowerCase()),a.properties&&a.properties["hc-key"]&&a.graphic.addClass("highcharts-key-"+a.properties["hc-key"].toLowerCase()))}),this.baseTrans={originX:b.min-b.minPixelPadding/b.transA,originY:c.min-c.minPixelPadding/c.transA+(c.reversed?0:c.len/c.transA),transAX:b.transA,transAY:c.transA},this.transformGroup.animate({translateX:0,translateY:0,scaleX:1,scaleY:1})):(n=b.transA/q.transAX,
l=c.transA/q.transAY,t=b.toPixels(q.originX,!0),m=c.toPixels(q.originY,!0),.99<n&&1.01>n&&.99<l&&1.01>l&&(l=n=1,t=Math.round(t),m=Math.round(m)),p=this.transformGroup,k.renderer.globalAnimation?(w=p.attr("translateX"),v=p.attr("translateY"),x=p.attr("scaleX"),y=p.attr("scaleY"),p.attr({animator:0}).animate({animator:1},{step:function(a,b){p.attr({translateX:w+(t-w)*b.pos,translateY:v+(m-v)*b.pos,scaleX:x+(n-x)*b.pos,scaleY:y+(l-y)*b.pos})}})):p.attr({translateX:t,translateY:m,scaleX:n,scaleY:l}));
r||a.group.element.setAttribute("stroke-width",a.options[a.pointAttrToOptions&&a.pointAttrToOptions["stroke-width"]||"borderWidth"]/(n||1));this.drawMapDataLabels()},drawMapDataLabels:function(){t.prototype.drawDataLabels.call(this);this.dataLabelsGroup&&this.dataLabelsGroup.clip(this.chart.clipRect)},render:function(){var a=this,b=t.prototype.render;a.chart.renderer.isVML&&3E3<a.data.length?setTimeout(function(){b.call(a)}):b.call(a)},animate:function(a){var b=this.options.animation,c=this.group,
h=this.xAxis,f=this.yAxis,k=h.pos,d=f.pos;this.chart.renderer.isSVG&&(!0===b&&(b={duration:1E3}),a?c.attr({translateX:k+h.len/2,translateY:d+f.len/2,scaleX:.001,scaleY:.001}):(c.animate({translateX:k,translateY:d,scaleX:1,scaleY:1},b),this.animate=null))},animateDrilldown:function(a){var b=this.chart.plotBox,c=this.chart.drilldownLevels[this.chart.drilldownLevels.length-1],h=c.bBox,f=this.chart.options.drilldown.animation;a||(a=Math.min(h.width/b.width,h.height/b.height),c.shapeArgs={scaleX:a,scaleY:a,
translateX:h.x,translateY:h.y},e(this.points,function(a){a.graphic&&a.graphic.attr(c.shapeArgs).animate({scaleX:1,scaleY:1,translateX:0,translateY:0},f)}),this.animate=null)},drawLegendSymbol:a.LegendSymbolMixin.drawRectangle,animateDrillupFrom:function(a){f.column.prototype.animateDrillupFrom.call(this,a)},animateDrillupTo:function(a){f.column.prototype.animateDrillupTo.call(this,a)}}),q({applyOptions:function(a,b){a=g.prototype.applyOptions.call(this,a,b);b=this.series;var c=b.joinBy;b.mapData&&
((c=void 0!==a[c[1]]&&b.mapMap[a[c[1]]])?(b.xyFromShape&&(a.x=c._midX,a.y=c._midY),q(a,c)):a.value=a.value||null);return a},onMouseOver:function(a){clearTimeout(this.colorInterval);if(null!==this.value)g.prototype.onMouseOver.call(this,a);else this.series.onMouseOut(a)},onMouseOut:function(){var a=this,b=+new Date,c=m(this.series.pointAttribs(a).fill),f=m(this.series.pointAttribs(a,"hover").fill),k=a.series.options.states.normal.animation,d=k&&(k.duration||500);d&&4===c.rgba.length&&4===f.rgba.length&&
"select"!==a.state&&(clearTimeout(a.colorInterval),a.colorInterval=setInterval(function(){var k=(new Date-b)/d,h=a.graphic;1<k&&(k=1);h&&h.attr("fill",p.prototype.tweenColors.call(0,f,c,k));1<=k&&clearTimeout(a.colorInterval)},13),a.isFading=!0);g.prototype.onMouseOut.call(a);a.isFading=null},zoomTo:function(){var a=this.series;a.xAxis.setExtremes(this._minX,this._maxX,!1);a.yAxis.setExtremes(this._minY,this._maxY,!1);a.chart.redraw()}},l))})(y);(function(a){var m=a.seriesType,p=a.seriesTypes;m("mapline",
"map",{lineWidth:1,fillColor:"none"},{type:"mapline",colorProp:"stroke",pointAttrToOptions:{stroke:"color","stroke-width":"lineWidth"},pointAttribs:function(a,e){a=p.map.prototype.pointAttribs.call(this,a,e);a.fill=this.options.fillColor;return a},drawLegendSymbol:p.line.prototype.drawLegendSymbol})})(y);(function(a){var m=a.merge,p=a.Point;a=a.seriesType;a("mappoint","scatter",{dataLabels:{enabled:!0,formatter:function(){return this.point.name},crop:!1,defer:!1,overflow:!1,style:{color:"#000000"}}},
{type:"mappoint",forceDL:!0},{applyOptions:function(a,e){a=void 0!==a.lat&&void 0!==a.lon?m(a,this.series.chart.fromLatLonToPoint(a)):a;return p.prototype.applyOptions.call(this,a,e)}})})(y);(function(a){var m=a.arrayMax,p=a.arrayMin,l=a.Axis,e=a.color,q=a.each,w=a.isNumber,x=a.noop,v=a.pick,d=a.pInt,b=a.Point,c=a.Series,g=a.seriesType,t=a.seriesTypes;g("bubble","scatter",{dataLabels:{formatter:function(){return this.point.z},inside:!0,verticalAlign:"middle"},marker:{lineColor:null,lineWidth:1,radius:null,
states:{hover:{radiusPlus:0}},symbol:"circle"},minSize:8,maxSize:"20%",softThreshold:!1,states:{hover:{halo:{size:5}}},tooltip:{pointFormat:"({point.x}, {point.y}), Size: {point.z}"},turboThreshold:0,zThreshold:0,zoneAxis:"z"},{pointArrayMap:["y","z"],parallelArrays:["x","y","z"],trackerGroups:["markerGroup","dataLabelsGroup"],bubblePadding:!0,zoneAxis:"z",directTouch:!0,pointAttribs:function(a,b){var f=v(this.options.marker.fillOpacity,.5);a=c.prototype.pointAttribs.call(this,a,b);1!==f&&(a.fill=
e(a.fill).setOpacity(f).get("rgba"));return a},getRadii:function(a,b,c,d){var f,k,g,n=this.zData,e=[],r=this.options,t="width"!==r.sizeBy,l=r.zThreshold,m=b-a;k=0;for(f=n.length;k<f;k++)g=n[k],r.sizeByAbsoluteValue&&null!==g&&(g=Math.abs(g-l),b=Math.max(b-l,Math.abs(a-l)),a=0),null===g?g=null:g<a?g=c/2-1:(g=0<m?(g-a)/m:.5,t&&0<=g&&(g=Math.sqrt(g)),g=Math.ceil(c+g*(d-c))/2),e.push(g);this.radii=e},animate:function(a){var b=this.options.animation;a||(q(this.points,function(a){var c=a.graphic,f;c&&c.width&&
(f={x:c.x,y:c.y,width:c.width,height:c.height},c.attr({x:a.plotX,y:a.plotY,width:1,height:1}),c.animate(f,b))}),this.animate=null)},translate:function(){var b,c=this.data,d,g,h=this.radii;t.scatter.prototype.translate.call(this);for(b=c.length;b--;)d=c[b],g=h?h[b]:0,w(g)&&g>=this.minPxSize/2?(d.marker=a.extend(d.marker,{radius:g,width:2*g,height:2*g}),d.dlBox={x:d.plotX-g,y:d.plotY-g,width:2*g,height:2*g}):d.shapeArgs=d.plotY=d.dlBox=void 0},alignDataLabel:t.column.prototype.alignDataLabel,buildKDTree:x,
applyZones:x},{haloPath:function(a){return b.prototype.haloPath.call(this,0===a?0:(this.marker?this.marker.radius||0:0)+a)},ttBelow:!1});l.prototype.beforePadding=function(){var a=this,b=this.len,c=this.chart,g=0,h=b,e=this.isXAxis,t=e?"xData":"yData",l=this.min,x={},y=Math.min(c.plotWidth,c.plotHeight),D=Number.MAX_VALUE,E=-Number.MAX_VALUE,F=this.max-l,C=b/F,G=[];q(this.series,function(b){var g=b.options;!b.bubblePadding||!b.visible&&c.options.chart.ignoreHiddenSeries||(a.allowZoomOutside=!0,G.push(b),
e&&(q(["minSize","maxSize"],function(a){var b=g[a],c=/%$/.test(b),b=d(b);x[a]=c?y*b/100:b}),b.minPxSize=x.minSize,b.maxPxSize=Math.max(x.maxSize,x.minSize),b=b.zData,b.length&&(D=v(g.zMin,Math.min(D,Math.max(p(b),!1===g.displayNegative?g.zThreshold:-Number.MAX_VALUE))),E=v(g.zMax,Math.max(E,m(b))))))});q(G,function(b){var c=b[t],d=c.length,f;e&&b.getRadii(D,E,b.minPxSize,b.maxPxSize);if(0<F)for(;d--;)w(c[d])&&a.dataMin<=c[d]&&c[d]<=a.dataMax&&(f=b.radii[d],g=Math.min((c[d]-l)*C-f,g),h=Math.max((c[d]-
l)*C+f,h))});G.length&&0<F&&!this.isLog&&(h-=b,C*=(b+g-h)/b,q([["min","userMin",g],["max","userMax",h]],function(b){void 0===v(a.options[b[0]],a[b[1]])&&(a[b[0]]+=b[2]/C)}))}})(y);(function(a){var m=a.merge,p=a.Point,l=a.seriesType,e=a.seriesTypes;e.bubble&&l("mapbubble","bubble",{animationLimit:500,tooltip:{pointFormat:"{point.name}: {point.z}"}},{xyFromShape:!0,type:"mapbubble",pointArrayMap:["z"],getMapData:e.map.prototype.getMapData,getBox:e.map.prototype.getBox,setData:e.map.prototype.setData},
{applyOptions:function(a,l){return a&&void 0!==a.lat&&void 0!==a.lon?p.prototype.applyOptions.call(this,m(a,this.series.chart.fromLatLonToPoint(a)),l):e.map.prototype.pointClass.prototype.applyOptions.call(this,a,l)},ttBelow:!1})})(y);(function(a){var m=a.colorPointMixin,p=a.each,l=a.merge,e=a.noop,q=a.pick,w=a.Series,x=a.seriesType,v=a.seriesTypes;x("heatmap","scatter",{animation:!1,borderWidth:0,nullColor:"#f7f7f7",dataLabels:{formatter:function(){return this.point.value},inside:!0,verticalAlign:"middle",
crop:!1,overflow:!1,padding:0},marker:null,pointRange:null,tooltip:{pointFormat:"{point.x}, {point.y}: {point.value}\x3cbr/\x3e"},states:{normal:{animation:!0},hover:{halo:!1,brightness:.2}}},l(a.colorSeriesMixin,{pointArrayMap:["y","value"],hasPointSpecificOptions:!0,supportsDrilldown:!0,getExtremesFromAll:!0,directTouch:!0,init:function(){var a;v.scatter.prototype.init.apply(this,arguments);a=this.options;a.pointRange=q(a.pointRange,a.colsize||1);this.yAxis.axisPointRange=a.rowsize||1},translate:function(){var a=
this.options,b=this.xAxis,c=this.yAxis,g=function(a,b,c){return Math.min(Math.max(b,a),c)};this.generatePoints();p(this.points,function(d){var k=(a.colsize||1)/2,f=(a.rowsize||1)/2,e=g(Math.round(b.len-b.translate(d.x-k,0,1,0,1)),-b.len,2*b.len),k=g(Math.round(b.len-b.translate(d.x+k,0,1,0,1)),-b.len,2*b.len),l=g(Math.round(c.translate(d.y-f,0,1,0,1)),-c.len,2*c.len),f=g(Math.round(c.translate(d.y+f,0,1,0,1)),-c.len,2*c.len);d.plotX=d.clientX=(e+k)/2;d.plotY=(l+f)/2;d.shapeType="rect";d.shapeArgs=
{x:Math.min(e,k),y:Math.min(l,f),width:Math.abs(k-e),height:Math.abs(f-l)}});this.translateColors()},drawPoints:function(){v.column.prototype.drawPoints.call(this);p(this.points,function(a){a.graphic.attr(this.colorAttribs(a))},this)},animate:e,getBox:e,drawLegendSymbol:a.LegendSymbolMixin.drawRectangle,alignDataLabel:v.column.prototype.alignDataLabel,getExtremes:function(){w.prototype.getExtremes.call(this,this.valueData);this.valueMin=this.dataMin;this.valueMax=this.dataMax;w.prototype.getExtremes.call(this)}}),
m)})(y);(function(a){function m(a,b){var c,g,d,e=!1,f=a.x,n=a.y;a=0;for(c=b.length-1;a<b.length;c=a++)g=b[a][1]>n,d=b[c][1]>n,g!==d&&f<(b[c][0]-b[a][0])*(n-b[a][1])/(b[c][1]-b[a][1])+b[a][0]&&(e=!e);return e}var p=a.Chart,l=a.each,e=a.extend,q=a.format,w=a.merge,x=a.win,v=a.wrap;p.prototype.transformFromLatLon=function(d,b){if(void 0===x.proj4)return a.error(21),{x:0,y:null};d=x.proj4(b.crs,[d.lon,d.lat]);var c=b.cosAngle||b.rotation&&Math.cos(b.rotation),g=b.sinAngle||b.rotation&&Math.sin(b.rotation);
d=b.rotation?[d[0]*c+d[1]*g,-d[0]*g+d[1]*c]:d;return{x:((d[0]-(b.xoffset||0))*(b.scale||1)+(b.xpan||0))*(b.jsonres||1)+(b.jsonmarginX||0),y:(((b.yoffset||0)-d[1])*(b.scale||1)+(b.ypan||0))*(b.jsonres||1)-(b.jsonmarginY||0)}};p.prototype.transformToLatLon=function(d,b){if(void 0===x.proj4)a.error(21);else{d={x:((d.x-(b.jsonmarginX||0))/(b.jsonres||1)-(b.xpan||0))/(b.scale||1)+(b.xoffset||0),y:((-d.y-(b.jsonmarginY||0))/(b.jsonres||1)+(b.ypan||0))/(b.scale||1)+(b.yoffset||0)};var c=b.cosAngle||b.rotation&&
Math.cos(b.rotation),g=b.sinAngle||b.rotation&&Math.sin(b.rotation);b=x.proj4(b.crs,"WGS84",b.rotation?{x:d.x*c+d.y*-g,y:d.x*g+d.y*c}:d);return{lat:b.y,lon:b.x}}};p.prototype.fromPointToLatLon=function(d){var b=this.mapTransforms,c;if(b){for(c in b)if(b.hasOwnProperty(c)&&b[c].hitZone&&m({x:d.x,y:-d.y},b[c].hitZone.coordinates[0]))return this.transformToLatLon(d,b[c]);return this.transformToLatLon(d,b["default"])}a.error(22)};p.prototype.fromLatLonToPoint=function(d){var b=this.mapTransforms,c,g;
if(!b)return a.error(22),{x:0,y:null};for(c in b)if(b.hasOwnProperty(c)&&b[c].hitZone&&(g=this.transformFromLatLon(d,b[c]),m({x:g.x,y:-g.y},b[c].hitZone.coordinates[0])))return g;return this.transformFromLatLon(d,b["default"])};a.geojson=function(a,b,c){var g=[],d=[],k=function(a){var b,c=a.length;d.push("M");for(b=0;b<c;b++)1===b&&d.push("L"),d.push(a[b][0],-a[b][1])};b=b||"map";l(a.features,function(a){var c=a.geometry,f=c.type,c=c.coordinates;a=a.properties;var h;d=[];"map"===b||"mapbubble"===
b?("Polygon"===f?(l(c,k),d.push("Z")):"MultiPolygon"===f&&(l(c,function(a){l(a,k)}),d.push("Z")),d.length&&(h={path:d})):"mapline"===b?("LineString"===f?k(c):"MultiLineString"===f&&l(c,k),d.length&&(h={path:d})):"mappoint"===b&&"Point"===f&&(h={x:c[0],y:-c[1]});h&&g.push(e(h,{name:a.name||a.NAME,properties:a}))});c&&a.copyrightShort&&(c.chart.mapCredits=q(c.chart.options.credits.mapText,{geojson:a}),c.chart.mapCreditsFull=q(c.chart.options.credits.mapTextFull,{geojson:a}));return g};v(p.prototype,
"addCredits",function(a,b){b=w(!0,this.options.credits,b);this.mapCredits&&(b.href=null);a.call(this,b);this.credits&&this.mapCreditsFull&&this.credits.attr({title:this.mapCreditsFull})})})(y);(function(a){function m(a,b,d,e,f,l,m,h){return["M",a+f,b,"L",a+d-l,b,"C",a+d-l/2,b,a+d,b+l/2,a+d,b+l,"L",a+d,b+e-m,"C",a+d,b+e-m/2,a+d-m/2,b+e,a+d-m,b+e,"L",a+h,b+e,"C",a+h/2,b+e,a,b+e-h/2,a,b+e-h,"L",a,b+f,"C",a,b+f/2,a+f/2,b,a+f,b,"Z"]}var p=a.Chart,l=a.defaultOptions,e=a.each,q=a.extend,w=a.merge,x=a.pick,
v=a.Renderer,d=a.SVGRenderer,b=a.VMLRenderer;q(l.lang,{zoomIn:"Zoom in",zoomOut:"Zoom out"});l.mapNavigation={buttonOptions:{alignTo:"plotBox",align:"left",verticalAlign:"top",x:0,width:18,height:18,padding:5,style:{fontSize:"15px",fontWeight:"bold"},theme:{"stroke-width":1,"text-align":"center"}},buttons:{zoomIn:{onclick:function(){this.mapZoom(.5)},text:"+",y:0},zoomOut:{onclick:function(){this.mapZoom(2)},text:"-",y:28}},mouseWheelSensitivity:1.1};a.splitPath=function(a){var b;a=a.replace(/([A-Za-z])/g,
" $1 ");a=a.replace(/^\s*/,"").replace(/\s*$/,"");a=a.split(/[ ,]+/);for(b=0;b<a.length;b++)/[a-zA-Z]/.test(a[b])||(a[b]=parseFloat(a[b]));return a};a.maps={};d.prototype.symbols.topbutton=function(a,b,d,e,f){return m(a-1,b-1,d,e,f.r,f.r,0,0)};d.prototype.symbols.bottombutton=function(a,b,d,e,f){return m(a-1,b-1,d,e,0,0,f.r,f.r)};v===b&&e(["topbutton","bottombutton"],function(a){b.prototype.symbols[a]=d.prototype.symbols[a]});a.Map=a.mapChart=function(b,d,e){var c="string"===typeof b||b.nodeName,
f=arguments[c?1:0],g={endOnTick:!1,visible:!1,minPadding:0,maxPadding:0,startOnTick:!1},l,h=a.getOptions().credits;l=f.series;f.series=null;f=w({chart:{panning:"xy",type:"map"},credits:{mapText:x(h.mapText,' \u00a9 \x3ca href\x3d"{geojson.copyrightUrl}"\x3e{geojson.copyrightShort}\x3c/a\x3e'),mapTextFull:x(h.mapTextFull,"{geojson.copyright}")},tooltip:{followTouchMove:!1},xAxis:g,yAxis:w(g,{reversed:!0})},f,{chart:{inverted:!1,alignTicks:!1}});f.series=l;return c?new p(b,f,e):new p(f,d)}})(y)});