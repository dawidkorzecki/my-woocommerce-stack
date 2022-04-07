<?php

namespace FSVendor;

/**
 * Form ending with hoverable tip snippet in js.
 */
?>
</tbody>
</table>
</form>

<script type="text/javascript">
	(function($) {
		$( '.tips, .help_tip, .woocommerce-help-tip' ).tipTip( {
			'attribute': 'data-tip',
			'fadeIn': 50,
			'fadeOut': 50,
			'delay': 200
		} );
	})(jQuery);
</script>
<?php 
