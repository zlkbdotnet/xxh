layui.define(function(exports){
var jQuery = layui.jquery;
;(function($){var jstar_call_id=0;$.jstars={};$.fn.jstars=function(settings)
{var ua=window.navigator.userAgent,msie=ua.indexOf("MSIE ");if(msie>0){if(parseInt(ua.substring(msie+5,ua.indexOf(".",msie)))<9)return;}
settings=$.extend({},$.fn.jstars.defaults,settings);settings.frequency=20-Math.max(1,Math.min(settings.frequency,19));var jstar_timer=null;var jstar_index=0;var jstar_dindex=0;var jstar_image=null;var jstar_id='jstar_span_'+jstar_call_id++;return this.each(function()
{if(!jstar_timer){var jstar_uptade_star_bg=function(){if(!$('span.jstar_span').size)return;$('span.jstar_span.'+jstar_id).each(function(){var bg_pos=$(this).css('background-position').split(' ');var bg_pos_x=parseInt(bg_pos[0]);var bg_pos_y=parseInt(bg_pos[1]);$(this).css('background-position',(bg_pos_x-settings.width)+'px '+bg_pos_y+'px');})}
jstar_timer=setInterval(jstar_uptade_star_bg,settings.delay/9);jstar_image=new Image();jstar_image.src=settings.image_path+'/'+settings.image;}
$(this).mousemove(function(e){if((jstar_dindex++%settings.frequency)!=0)return;var sideX=jstar_rand(-1,1);var sideY=jstar_rand(-1,1);var randX=jstar_rand(5,30);var randY=jstar_rand(5,30);var opacity=Math.min(Math.random()+0.4,1);var x=e.pageX+(sideX*randX);var y=e.pageY+(sideY*randY);var id='jstar_'+jstar_index++;if(settings.style!='rand'){var bg_pos='0px '+settings.style_map[settings.style]+'px';}
else{var ind=jstar_rand(0,5);var i=0;for(var key in settings.style_map){if(i++==ind){var bg_pos='0px '+settings.style_map[key]+'px';break;}}}
var span='<span id="'+id+'" class="jstar_span '+jstar_id+'" style="display:block; width:27px; height:27px; background:url('+jstar_image.src+') no-repeat '+bg_pos+'; margin:0; padding:0; position:absolute; top:-50px; left:0;">&nbsp;</span>';$(document.body).append(span);var star=$('#'+id);star.css({top:y,left:x,'opacity':opacity}).animate({opacity:0},settings.delay,function(){star.remove();});})})};function jstar_rand(from,to){var r=Math.random();r=r*(to-from);r=r+from;r=Math.round(r);return r;}
$.fn.jstars.defaults={image_path:'',image:'jstar-map.png',style:'white',frequency:12,style_map:{white:0,blue:-27,green:-54,red:-81,yellow:-108},width:27,height:27,delay:300};}(jQuery));

	exports('mousejqtxstars',null)
});