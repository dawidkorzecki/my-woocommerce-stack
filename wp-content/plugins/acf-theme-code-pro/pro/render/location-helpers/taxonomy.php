<?php
// Taxonomy 

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

echo htmlspecialchars("<?php
// Define taxonomy prefix
// Replace NULL with the name of the taxonomy eg 'category'
\$taxonomy_prefix = NULL;

// Define term ID
// Replace NULL with ID of term to be queried eg '123' 
\$term_id = NULL;

// Define prefixed term ID
\$term_id_prefixed = \$taxonomy_prefix .'_'. \$term_id;
?>");
