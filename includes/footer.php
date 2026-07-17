<?php include_once __DIR__ . '/business-config.php'; ?>

  <!-- Footer -->
  <footer class="site-footer" aria-label="Site footer">
    <div class="container">
      <div class="row">

        <!-- Brand / About -->
        <div class="col-lg-4 mb-4 mb-lg-0">
          <h2 class="footer-heading"><?php echo $business['name']; ?></h2>
          <p class="footer-about">[Brief description of the business — one or two sentences.]</p>
        </div>

        <!-- Quick Links -->
        <div class="col-lg-2 col-md-4 mb-4 mb-md-0">
          <h3 class="footer-heading">Pages</h3>
          <ul class="footer-links">
            <li><a href="/">Home</a></li>
            <li><a href="/about">About</a></li>
            <li><a href="/services">Services</a></li>
            <li><a href="/contact">Contact</a></li>
          </ul>
        </div>

        <!-- Legal Links -->
        <div class="col-lg-2 col-md-4 mb-4 mb-md-0">
          <h3 class="footer-heading">Legal</h3>
          <ul class="footer-links">
            <li><a href="/privacy-policy">Privacy Policy</a></li>
            <li><a href="/accessibility-statement">Accessibility</a></li>
          </ul>
        </div>

        <!-- Contact Info -->
        <div class="col-lg-4 col-md-4">
          <h3 class="footer-heading">Contact</h3>
          <address class="footer-contact">
            <?php echo $business['address']; ?><br>
            <?php if (!empty($business['address2'])) echo $business['address2'] . '<br>'; ?>
            <?php echo $business['city'] . ', ' . $business['state'] . ' ' . $business['zip']; ?><br>
            <a href="tel:<?php echo $business['phone']; ?>"><?php echo $business['phone_display']; ?></a><br>
            <a href="mailto:<?php echo $business['email']; ?>"><?php echo $business['email']; ?></a>
          </address>
        </div>

      </div>

      <!-- Footer Bottom -->
      <div class="footer-bottom">
        <p>&copy; <?php echo date('Y'); ?> <?php echo $business['name']; ?>. All rights reserved.</p>
        <p class="site-version" aria-label="Site version">[YYMMA]</p>
      </div>

    </div>
  </footer>

  <!-- Back to Top -->
  <a href="#" class="back-to-top" id="backToTop" aria-label="Back to top">
    <i class="bi bi-chevron-up" aria-hidden="true"></i>
  </a>

  <!-- Schema.org JSON-LD -->
  <?php include __DIR__ . '/schema.php'; ?>

  <!-- Bootstrap 5.3.8 JS Bundle (local) -->
  <script src="/js/bootstrap.bundle.min.js"></script>

  <!-- Site Scripts -->
  <script src="/js/scripts.js?v=20260717"></script>

</body>
</html>
