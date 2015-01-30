/**
 * JoomGallery PhotoSwipe Plugin 'Integrate PhotoSwipe (4.0+)'
 *
 * With this plugin JoomGallery is able to use the PhotoSwipe Javascript
 * (http://www.photoswipe.com/) for displaying images.
 *
 * By:        Erftralle, based on JoomPhotoswipe by JoomGallery::ProjectTeam 
 * Copyright: (c) 2015 Erftralle, (c) 2013 - 2014 JoomGallery::ProjectTeam
 * License:   This software is released under the GNU/GPL License
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free Software
 * Foundation, either version 2 of the License, or (at your option) any later
 * version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY, without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin St, Fifth Floor, Boston, MA 02110, USA
 */

/**
 * Add PhotoSwipe's HTML code to the DOM and bind click events
 *
 */
(function(window, document, $, undefined) {
  $(document).ready(function(){ 
    
    $('body').append('<div class="pswp" tabindex="-1" role="dialog" aria-hidden="true"/>');
    $( ".pswp" ).append('<div class="pswp__bg"/>');
    $( ".pswp" ).append('<div class="pswp__scroll-wrap"/>');
    $(".pswp__scroll-wrap").append('<div class="pswp__container"/>');
    $(".pswp__container").append('<div class="pswp__item"/>');
    $(".pswp__container").append('<div class="pswp__item"/>');
    $(".pswp__container").append('<div class="pswp__item"/>');
    $(".pswp__scroll-wrap").append('<div class="pswp__ui pswp__ui--hidden"/>');
    $(".pswp__ui").append('<div class="pswp__top-bar"/>');
    $(".pswp__top-bar").append('<div class="pswp__counter"/>');
    $(".pswp__top-bar").append('<button class="pswp__button pswp__button--close" title="Close (Esc)"/>');
    $(".pswp__top-bar").append('<button class="pswp__button pswp__button--share" title="Share"/>');
    $(".pswp__top-bar").append('<button class="pswp__button pswp__button--fs" title="Toggle fullscreen"/>');
    $(".pswp__top-bar").append('<button class="pswp__button pswp__button--zoom" title="Zoom in/out"/>');
    $(".pswp__top-bar").append('<div class="pswp__preloader"/>');
    $(".pswp__preloader").append('<div class="pswp__preloader__icn"/>');
    $(".pswp__preloader__icn").append('<div class="pswp__preloader__cut"/>');
    $(".pswp__preloader__cut").append('<div class="pswp__preloader__donut"/>');
    $(".pswp__ui").append('<div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap"/>');
    $(".pswp__share-modal").append('<div class="pswp__share-tooltip"/>');
    $(".pswp__ui").append('<button class="pswp__button pswp__button--arrow--left" title="Previous (arrow left)"/>');
    $(".pswp__ui").append('<button class="pswp__button pswp__button--arrow--right" title="Next (arrow right)"/>');
    $(".pswp__ui").append('<div class="pswp__caption"/>');
    $(".pswp__caption").append('<div class="pswp__caption__center"/>');
    
    $("a[rel^='PhotoSwipe-v4plus']").on("click", function(event) {
      
      var pswpElement = document.querySelectorAll('.pswp')[0];
      var pswp, options, items = [], index = 0;
      var href = $(this).attr('href');
      
      $('a[data-group=' + $(this).attr('data-group') + ']').each(function() {
        size = $(this).attr('data-size').split('x');
        var item = {
          src: $(this).attr('href'),
          w: parseInt(size[0], 10),
          h: parseInt(size[1], 10),
          title: $(this).attr('data-title')
        };
        items.push(item);

        if(item.src === href) {
          index = items.length - 1;
        }
        
      });
      
      options = {
        index: index,
        loop: jg_pswp_options.loop,
        history: false,
        shareEl: false,
        captionEl: jg_pswp_options.captionEl,
        closeOnScroll: false,
        escKey: true,
        arrowKeys : true,
      };
      
      pswp = new PhotoSwipe( pswpElement, PhotoSwipeUI_Default, items, options);
      pswp.init();
      
      var photoswipe_onkeydownsave = window.document.onkeydown;
      window.document.onkeydown = null;
      
      pswp.listen('destroy', function() {
        window.document.onkeydown = photoswipe_onkeydownsave;
      });
      
      return false;
    });
  });
}(window, document, window.jQuery));