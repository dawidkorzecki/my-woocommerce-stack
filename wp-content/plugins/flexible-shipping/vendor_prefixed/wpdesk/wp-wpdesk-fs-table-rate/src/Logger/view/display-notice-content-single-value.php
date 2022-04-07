<?php

namespace FSVendor;

/**
 * @var string $section
 * @var string $section_content
 */
?><br/>
<div class="flexible-shipping-log">
	<button class="small show"><?php 
echo \sprintf(\__('Show %1$s', 'flexible-shipping'), $section);
?></button>
	<button class="small hide"><?php 
echo \sprintf(\__('Hide %1$s', 'flexible-shipping'), $section);
?></button>
	<button class="small clipboard"><?php 
echo \sprintf(\__('Copy %1$s', 'flexible-shipping'), $section);
?></button>
	<pre><?php 
echo \esc_html($section_content);
?> </pre>
</div><?php 
