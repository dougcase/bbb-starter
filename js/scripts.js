/* ==========================================================================
   [Site Name] — Site Scripts
   ========================================================================== */

(function () {
  'use strict';

  /* ========================================================================
     External Link Auto-Detection
     Adds .external-link class + target/rel attributes.
     Skips: same-domain, image links, icon-only links, buttons.
     ======================================================================== */

  document.addEventListener('DOMContentLoaded', function () {
    var ownHost = location.hostname.replace('www.', '');

    document.querySelectorAll('a[href^="http"]').forEach(function (a) {
      try {
        var linkHost = new URL(a.href).hostname.replace('www.', '');
        if (linkHost === ownHost) return;
        if (a.querySelector('img')) return;
        if (a.querySelector('i, svg') && !a.textContent.trim().length) return;
        if (a.classList.contains('btn')) return;
        a.classList.add('external-link');
        if (!a.getAttribute('target')) a.setAttribute('target', '_blank');
        if (!a.getAttribute('rel')) a.setAttribute('rel', 'noopener noreferrer');
      } catch (e) {}
    });

    /* ========================================================================
       PDF Link Auto-Detection
       Adds .pdf-link class + target="_blank".
       Skips: image links.
       ======================================================================== */

    document.querySelectorAll('a[href$=".pdf"]').forEach(function (a) {
      if (a.querySelector('img')) return;
      a.classList.add('pdf-link');
      if (!a.getAttribute('target')) a.setAttribute('target', '_blank');
    });
  });


  /* ========================================================================
     Back to Top Button
     Shows after 300px scroll. Smooth scrolls to top on click.
     ======================================================================== */

  var backToTop = document.getElementById('backToTop');

  if (backToTop) {
    window.addEventListener('scroll', function () {
      if (window.scrollY > 300) {
        backToTop.classList.add('visible');
      } else {
        backToTop.classList.remove('visible');
      }
    });

    backToTop.addEventListener('click', function (e) {
      e.preventDefault();
      window.scrollTo({ top: 0, behavior: 'smooth' });
      // Return focus to skip link or top of page
      var skipLink = document.querySelector('.skip-link');
      if (skipLink) skipLink.focus();
    });
  }


  /* ========================================================================
     Smooth Scroll for Anchor Links
     Offsets for fixed navbar height.
     ======================================================================== */

  document.querySelectorAll('a[href^="#"]').forEach(function (a) {
    a.addEventListener('click', function (e) {
      var targetId = this.getAttribute('href');
      if (targetId === '#' || targetId === '#main-content') return;

      var target = document.querySelector(targetId);
      if (target) {
        e.preventDefault();
        var navHeight = document.querySelector('.site-header') ? document.querySelector('.site-header').offsetHeight : 0;
        var targetPosition = target.getBoundingClientRect().top + window.scrollY - navHeight - 16;
        window.scrollTo({ top: targetPosition, behavior: 'smooth' });
      }
    });
  });


  /* ========================================================================
     Slider / Carousel
     Ported verbatim from the Risingline Framework (.rl-slider).
     Manual prev/next + dot navigation. No auto-rotation (WCAG 2.2.2).
     Keyboard: ArrowLeft/Right on slider or pagination dots.
     Random start slide so repeat visitors see different content.
     ======================================================================== */

  document.querySelectorAll('.rl-slider').forEach(function (slider) {
    var slides = slider.querySelectorAll('.rl-slide');
    var dots = slider.querySelectorAll('.rl-slider-dot');
    var prevBtn = slider.querySelector('.rl-slider-prev');
    var nextBtn = slider.querySelector('.rl-slider-next');
    var totalSlides = slides.length;
    if (!totalSlides) return;

    var currentIndex = Math.floor(Math.random() * totalSlides);

    function showSlide(index) {
      if (index < 0) index = totalSlides - 1;
      if (index >= totalSlides) index = 0;
      currentIndex = index;

      slides.forEach(function (slide, i) {
        if (i === currentIndex) {
          slide.classList.add('active');
          slide.removeAttribute('aria-hidden');
        } else {
          slide.classList.remove('active');
          slide.setAttribute('aria-hidden', 'true');
        }
      });

      dots.forEach(function (dot, i) {
        if (i === currentIndex) {
          dot.classList.add('active');
          dot.setAttribute('aria-selected', 'true');
        } else {
          dot.classList.remove('active');
          dot.setAttribute('aria-selected', 'false');
        }
      });
    }

    if (prevBtn) prevBtn.addEventListener('click', function () { showSlide(currentIndex - 1); });
    if (nextBtn) nextBtn.addEventListener('click', function () { showSlide(currentIndex + 1); });
    dots.forEach(function (dot, i) { dot.addEventListener('click', function () { showSlide(i); }); });

    slider.addEventListener('keydown', function (e) {
      if (e.key === 'ArrowLeft') { e.preventDefault(); showSlide(currentIndex - 1); if (prevBtn) prevBtn.focus(); }
      else if (e.key === 'ArrowRight') { e.preventDefault(); showSlide(currentIndex + 1); if (nextBtn) nextBtn.focus(); }
    });

    var pagination = slider.querySelector('.rl-slider-pagination');
    if (pagination) {
      pagination.addEventListener('keydown', function (e) {
        var focusedDot = document.activeElement;
        var dotIndex = Array.from(dots).indexOf(focusedDot);
        if (e.key === 'ArrowLeft') {
          e.preventDefault();
          var newIdx = dotIndex > 0 ? dotIndex - 1 : totalSlides - 1;
          dots[newIdx].focus(); showSlide(newIdx);
        } else if (e.key === 'ArrowRight') {
          e.preventDefault();
          var newIdx2 = dotIndex < totalSlides - 1 ? dotIndex + 1 : 0;
          dots[newIdx2].focus(); showSlide(newIdx2);
        }
      });
    }

    showSlide(currentIndex);
  });

})();
