<?php
$page_title       = 'Page Not Found | [Site Name]';
$page_description = 'The page you are looking for could not be found.';
$page_noindex     = true;

include 'includes/head.php';
include 'includes/nav.php';
?>

  <main id="main-content">
    <section class="section-padding">
      <div class="container text-center">
        <h1 class="display-1 fw-bold text-muted mb-3">404</h1>
        <h2>Page Not Found</h2>
        <p class="lead text-muted-custom mb-4">The page you're looking for doesn't exist or has been moved.</p>
        <div class="d-flex justify-content-center gap-3">
          <a href="/" class="btn btn-primary">Back to Home</a>
          <a href="/contact" class="btn btn-outline-secondary">Contact Us</a>
        </div>
      </div>
    </section>
  </main>

<?php include 'includes/footer.php'; ?>
