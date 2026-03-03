<?php
/**
 * Business Configuration — Single Source of Truth
 *
 * This file is the canonical source for all business contact information,
 * NAP (Name, Address, Phone) data, and Schema.org configuration.
 *
 * USED BY:
 *   includes/footer.php   — Business name, address, phone display
 *   includes/schema.php   — Schema.org JSON-LD structured data
 *   contact.php           — Address, phone, email display
 *   Any page needing NAP   — <?php echo $business['phone_display']; ?>
 *
 * USAGE:
 *   Include this file at the top of any page or include that needs business data:
 *     <?php include_once('includes/business-config.php'); ?>
 *   Then reference values as:
 *     <?php echo $business['name']; ?>
 *     <a href="tel:<?php echo $business['phone']; ?>"><?php echo $business['phone_display']; ?></a>
 *
 * IMPORTANT:
 *   When updating business info (address change, new phone number), update HERE ONLY.
 *   All pages and Schema pull from this file — one change propagates everywhere.
 *   After updating, verify: footer, contact page, and Schema output all match.
 */

$business = [
    // Business identity
    'name'          => '[Business Legal Name]',
    'dba'           => '',                          // "doing business as" — leave blank if same as name

    // Physical address
    'address'       => '[Street Address]',
    'address2'      => '',                          // Suite, unit, floor — leave blank if none
    'city'          => '[City]',
    'state'         => '[State]',                   // Two-letter abbreviation (e.g., ID, CA)
    'zip'           => '[ZIP]',

    // Contact
    'phone'         => '+1-XXX-XXX-XXXX',           // E.164 format for tel: links
    'phone_display' => '(XXX) XXX-XXXX',            // Human-readable for display
    'fax'           => '',                           // Leave blank if none
    'email'         => 'contact@domain.com',         // Primary contact email

    // Web
    'url'           => 'https://domain.com',         // No trailing slash
    'contact_url'   => 'contact.php',                // Relative URL to contact page

    // Schema.org configuration
    'schema_type'   => 'ProfessionalService',        // Common types:
                                                     //   LegalService — law firms
                                                     //   AccountingService — CPA firms
                                                     //   InsuranceAgency — insurance agencies
                                                     //   ProfessionalService — general professional services

    // Social media (used in Schema.org sameAs — leave blank if not applicable)
    'facebook'      => '',                           // Full URL: https://www.facebook.com/pagename
    'linkedin'      => '',                           // Full URL: https://www.linkedin.com/company/name

    // Business hours (used in Schema.org openingHoursSpecification)
    // Format: 'Mo-Fr 08:00-17:00' or leave blank if not applicable
    'hours'         => '',
];
?>
