<?php
$page_title       = '[Site Name] | [Tagline]';
$page_description = '[Homepage meta description - 120 to 158 characters]';
$page_canonical   = 'https://[domain.com]/';

include 'includes/head.php';
include 'includes/nav.php';
?>

  <main id="main-content">

    <!-- Hero -->
    <section class="hero">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-lg-7">
            <h1>[Headline That Speaks to the Visitor's Need]</h1>
            <p class="lead">[Supporting statement — one or two sentences that reinforce the headline and build trust.]</p>
            <div class="d-flex flex-wrap gap-3">
              <a href="/contact" class="btn btn-primary btn-lg">[Primary CTA]</a>
              <a href="/about" class="btn btn-outline-secondary btn-lg">[Secondary CTA]</a>
            </div>
          </div>
          <div class="col-lg-5 mt-4 mt-lg-0 text-center">
            <!-- Hero image placeholder -->
            <img src="/img/[hero-image.webp]" alt="[Descriptive alt text]" class="img-fluid rounded" width="600" height="400">
          </div>
        </div>
      </div>
    </section>

    <!-- Services / Features Overview -->
    <section class="section-padding">
      <div class="container">
        <div class="text-center mb-5">
          <h2>[What We Offer]</h2>
          <p class="text-muted-custom">[Brief intro to the services or features below.]</p>
        </div>
        <div class="row g-4">
          <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
              <div class="card-body text-center p-4">
                <i class="bi bi-[icon-name] fs-1 text-primary mb-3 d-block" aria-hidden="true"></i>
                <h3 class="h5">[Service One]</h3>
                <p class="text-muted-custom">[Brief description of this service or feature.]</p>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
              <div class="card-body text-center p-4">
                <i class="bi bi-[icon-name] fs-1 text-primary mb-3 d-block" aria-hidden="true"></i>
                <h3 class="h5">[Service Two]</h3>
                <p class="text-muted-custom">[Brief description of this service or feature.]</p>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
              <div class="card-body text-center p-4">
                <i class="bi bi-[icon-name] fs-1 text-primary mb-3 d-block" aria-hidden="true"></i>
                <h3 class="h5">[Service Three]</h3>
                <p class="text-muted-custom">[Brief description of this service or feature.]</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="section-padding bg-light-custom">
      <div class="container text-center">
        <h2>[Ready to Get Started?]</h2>
        <p class="lead text-muted-custom mb-4">[Reinforcing statement that drives the visitor toward the conversion action.]</p>
        <a href="/contact" class="btn btn-primary btn-lg">[Call to Action]</a>
      </div>
    </section>

  </main>

<?php include 'includes/footer.php'; ?>
