<?php
/**
 * Schema.org JSON-LD
 *
 * Pulls all values from $business array (business-config.php).
 * Included by footer.php — do not include directly.
 */

// Build the schema array
$schema = [
    '@context' => 'https://schema.org',
    '@type'    => !empty($business['schema_type']) ? $business['schema_type'] : 'Organization',
    'name'     => $business['name'],
    'url'      => $business['url'],
    'telephone' => $business['phone'],
    'email'    => $business['email'],
    'address'  => [
        '@type'           => 'PostalAddress',
        'streetAddress'   => $business['address'],
        'addressLocality' => $business['city'],
        'addressRegion'   => $business['state'],
        'postalCode'      => $business['zip'],
        'addressCountry'  => 'US'
    ]
];

// Add DBA / alternate name if set
if (!empty($business['dba'])) {
    $schema['alternateName'] = $business['dba'];
}

// Add second address line if set
if (!empty($business['address2'])) {
    $schema['address']['streetAddress'] .= ', ' . $business['address2'];
}

// Add contact page URL
if (!empty($business['contact_url'])) {
    $schema['contactPoint'] = [
        '@type'       => 'ContactPoint',
        'contactType' => 'customer service',
        'url'         => $business['contact_url']
    ];
}

// Add social profiles
$sameAs = [];
if (!empty($business['facebook']))  $sameAs[] = $business['facebook'];
if (!empty($business['linkedin']))  $sameAs[] = $business['linkedin'];
if (!empty($sameAs)) {
    $schema['sameAs'] = $sameAs;
}

// Add opening hours if set
if (!empty($business['hours'])) {
    $schema['openingHours'] = $business['hours'];
}
?>
<script type="application/ld+json">
<?php echo json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES); ?>

</script>
