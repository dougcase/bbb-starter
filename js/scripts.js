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

})();
