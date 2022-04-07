<?php

namespace FSVendor;

/**
 * Displays Beacon script.
 *
 * @var $beacon_id string .
 * @var $confirmation_message string .
 * @var $beacon_search_elements_class string .
 * @var $beacon_image_content string .
 */
$beacon_button_class = 'wpdesk-helpscout-beacon-button';
$beacon_image_content = !empty($beacon_image_content) ? $beacon_image_content : 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAGwAAAA7CAMAAACpM5+wAAABfGlDQ1BpY2MAACiRfZE9SMNAHMVfU6Ui9QPsUMQhQ3VqQVTEUatQhAqhVmjVweTSL2jSkKS4OAquBQc/FqsOLs66OrgKguAHiJOjk6KLlPi/pNAi1oPjfry797h7Bwj1MtOsrnFA020zlYiLmeyqGHiFH2H0YxBRmVnGnCQl0XF83cPH17sYz+p87s/Rp+YsBvhE4llmmDbxBvH0pm1w3icOsaKsEp8TR026IPEj1xWP3zgXXBZ4ZshMp+aJQ8RioY2VNmZFUyOeIo6omk75QsZjlfMWZ61cZc178hcGc/rKMtdpjiCBRSxBgggFVZRQho0YrTopFlK0H+/gH3b9ErkUcpXAyLGACjTIrh/8D353a+UnJ7ykYBzofnGcj1EgsAs0ao7zfew4jRPA/wxc6S1/pQ7MfJJea2mRI2BgG7i4bmnKHnC5A4SfDNmUXclPU8jngfcz+qYsMHQL9K55vTX3cfoApKmr5A1wcAiMFSh7vcO7e9p7+/dMs78fZnByomahL+IAAAAgY0hSTQAAeiYAAICEAAD6AAAAgOgAAHUwAADqYAAAOpgAABdwnLpRPAAAAnNQTFRF8fHx8PDw7+/v7u7u2unhqdzAj9Wuf9GkcM2aX8iPT8OESMJ/gNGk6+3sxuPTldezZMqSl9i0x+TU7O7t7e7tsN3FXMeNXceNst7Gu+HMZcmSZsqTvuHO6OzqgtKm2ejgZ8qU7e3tveHNVcWIw+PRm9i2ScKASsKBpNu9m9i3tt/Jtd7IzuXYTsODzeXY3+nkW8eM7OzsgdGl0OXZz+XZ6+vrds6evuDN6evqdtGfmN23o+C/gdWnX8qP0uXaU8aH9Pv3////3fTnn9i56fjvpOG/6urqbMuXxuzXjdmv2uXfu+jP6Pfv2eXeqdq/S8OB7vnzrtzD6enpi9SsVMaH9/z54/XraMyV6Ojoe8+hXsmOxevX9fv49vz5werUVsaJf9SmtubMs+XK8vr2nd66as2X2fLl+f37ldu1yu3aetKi+Pz6rePGh9erbs6aYsmRTcODdNCewurVTsSDXMiNedKhnt67ruTHUsSG5+fn/f7+1PDhkNqxwurUgtWoxevWj9mxcM+bVMaIw+vVhdaqxOvWYciQ0/DgTcOCp+HCrOPFcc2b0vDf2/PmsOTIn9+84PTpod+9r+THX8mPx+zY9Pv4wOrT7/n0j9SusdvE3eTg3OPf5eXlnda3uujP0fDf5ubmzeDV5OTkutzJ4+PjcMyawN3Nyt/T4uLigNCk0t/YWseL0uDYxt3Qx93R4eHhsdrEstnE4ODgk9SwmNW0m9a2mtW1tNnFUcSF39/fxNzPyt3S2N7bj9OujtOt3t7eydzRds2dsNnDX8iOt9nG3N7dvdrKY8mRkdOv3d3dzdzUo9a6jNKrfs+ibsuYfc+i3NzcDLGCXwAAAAFiS0dEQYnebE4AAAAJcEhZcwAACxMAAAsTAQCanBgAAAAHdElNRQfkBBATKBS9f2WVAAAEb0lEQVRYw7XY6WPURBQA8GAAgfFARasiR0QXURFSTbHtlvZBm6VSLQahwSKetdV6cSnxwnrUA8RbQRddllXXczWouIouHoALiP5JvpmkSdZOst0meR/aD7OZ3743s5OZEQRvTHDjtBDh6Ubwi0pHHHdUitUoUZw4afLpU6ZOIzXHtKlTzpg8aSIT/TlXOvOss6fXrlTG9HPOPc/1/CxxxvkXhJWsqLtwhsjVHEq86OJoKBozLxE5nGPNmh0dRWP2rFGak9acudFahMyd4yRXaUmXzovaImTeZZJXc6zLE9FbhMy/wquNWAvioGgsGNFc68q4LEKucjTbunphfNjCa2yNYaK0aHF8FiGLF6FmYWjJ9XFahNTLTBNoEeVr47UIuU6mhRRoYkpD3FiDQlMTaBGXxG0RsoQWEjH5et/Ft7GpuTnZEgU2U8FCCpjYUn57a3MbsFjWGIG2FFMTRFlZzreWUai9nf7tCI8tx9QESVZT3NYVACtYSp3odYbGUqosCZJyA3cBbgFY6ebYFRpL3KhIgqzexG1sAnBmRhJgDMPWDauCmm9WZUHRVnPbGjs8AwXQ9P/2W2AN/bcWesaIrdYUQdHXVf/SnClSM7ZOVwRVT5GqEZzZrb3rb9twO8PugDvvarv7Hl4fKV1FrPrWrROgzx9L3Ns/MNB/H8Puh8EHHlwFD3E6eRgxTX+kmtXSBe1kNGZHz0bYtHnzBthCsa2wlpBH4TFOL9t0TdCMqnnhQpLkYI8/gTEIPU9a6FYLe4qQp2E7rx+DYtuCpNbmLoA2zm/aLeMz0DuE8ayFPUfI8/ACLzPEdGM4wOpDClbylmIX2wgvEjI09JKFdSXIy/AK54FhQ0dsRwC204eqmCCvwq7X+l+fb2Ftm94YHD2dMHYwLGhLAPCmT4tn6r/19pp3ercQC3t31/r3BngP1DNsdyBW24KPmF/TbobtCXi4o6O2d1kAtgcxzXg/wm23P5b4gE399BjWq/CRSlNMT+/1/UTrhx/11dBhUOxN4wqi6pl9vp/Al9rOiLB9GbYQZ7K+S3HSfV2HjOEsxRTNyO73/UyyqTUabH/WoC9PHLTcxxHVyjcacjhkCu5B9Ezuk7ixT3NYRRl3Vzj582PYGoSJz/I48XF3JckapvZ5rOezL75kiUl0R6xjal/FefL8GhPT6Y4YD0w0tcI38WHfFjAxje718RRDRy1nHojL+s7M0RGT2ZEJd/u6kc2b38dyD5L4wcxnDTpiiLGTJxYStYMxjNuPB9HCIlonT3amtoat+FNd1Fbdz0U2YIp9pp5gFzKdK5iHfumOkur+9ZBZwAGzimhdTTha3iwdjnDl+u1wycx7LOeGh2p03Iql3yP6ff/xZ6lIx8u2RrARDWcJlrJ45OiBY2GlY38dPVLEEuLccC3PTZmsaqyUyJXKx0+c/PvU+G6/T/1z8sTxMmZVYCXUvJZ730jnpJGxuVK5XP53XIEPlmwKS4jzkH+7KSuqzaFnFotI1h74mImSTakV1n+TS46xWCBrIQAAABl0RVh0Q29tbWVudABDcmVhdGVkIHdpdGggR0lNUFeBDhcAAAAldEVYdGRhdGU6Y3JlYXRlADIwMjAtMDQtMTZUMTk6NDA6MjArMDM6MDDLEPk4AAAAJXRFWHRkYXRlOm1vZGlmeQAyMDIwLTA0LTE2VDE5OjQwOjIwKzAzOjAwuk1BhAAAABt0RVh0aWNjOmNvcHlyaWdodABQdWJsaWMgRG9tYWlutpExWwAAACJ0RVh0aWNjOmRlc2NyaXB0aW9uAEdJTVAgYnVpbHQtaW4gc1JHQkxnQRMAAAAVdEVYdGljYzptYW51ZmFjdHVyZXIAR0lNUEyekMoAAAAOdEVYdGljYzptb2RlbABzUkdCW2BJQwAAAABJRU5ErkJggg==';
if ('' !== $confirmation_message) {
    ?><div id="wpdesk-helpscout-beacon">
	<div class="wpdesk-helpscout-beacon-frame">
        <div style="position: fixed; bottom: 37px; right: 37px; outline: none;">
            <img class="<?php 
    echo \esc_attr($beacon_button_class);
    ?>" id="image0" src="<?php 
    echo \esc_attr($beacon_image_content);
    ?>" />
        </div>
	</div>
</div><?php 
}
?>

<script type="text/javascript">
	jQuery(document).ready(function () {
		(new HsBeacon(
			'<?php 
echo \esc_attr($beacon_id);
?>',
			'<?php 
echo \esc_attr($confirmation_message);
?>',
			'<?php 
echo \esc_attr($beacon_search_elements_class);
?>'
		)).attachBeaconEvents('<?php 
echo \esc_attr($beacon_button_class);
?>');
	});
</script>
<?php 
