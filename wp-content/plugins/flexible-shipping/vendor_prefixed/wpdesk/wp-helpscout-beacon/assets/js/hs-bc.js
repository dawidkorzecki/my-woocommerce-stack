'use strict';

/**
 * Creates a Beacon object with given params.
 *
 * @param {string} beaconId Id of the beacon from HelpScout.
 * @param {string} confirmationMessage Message shown to user to get his confirmation for Beacon initialization.
 * @param {string} searchElementsClass Css class of elements which modifies beacon search.
 * @constructor
 */
function HsBeacon(beaconId, confirmationMessage, searchElementsClass) {
	this.beaconId = beaconId;
	this.confirmationMessage = confirmationMessage;
	this.searchElementsClass = searchElementsClass;
}



/**
 * HelpScout Beacon implementation. Can ask for permission before initialize&show.
 */
HsBeacon.prototype = {
	initialized: false,
	confirmationMessage: '',
	beaconId: '',
	searchElementsClass: '',
	searchQuery: '',

	/**
	 * Attach ask&show event to given class.
	 *
	 * @param {string} buttonClass
	 */
	attachBeaconEvents: function (buttonClass) {
		const self = this;
		if (this.confirmationMessage !== '') {
			jQuery('.' + buttonClass).click(function () {
				jQuery(this).blur();
				if (self.showBeaconIfConfirmed()) {
					jQuery(this).fadeOut("slow");
				}
			});
		} else {
			this.ensureBeaconInitialization();
		}
		if (this.searchElementsClass !== '') {
			this.attachSearchListener();
		}
	},

	/**
	 * Listen on focus events to modify search query on beacon.
	 */
	attachSearchListener: function () {
		let beacon = this;
		jQuery(document).on('focus','.' + this.searchElementsClass,function(){
			if (jQuery(this).attr('data-beacon_search') !== undefined) {
				beacon.searchQuery = jQuery(this).attr('data-beacon_search');
				if (window.Beacon !== undefined) {
					window.Beacon('search', beacon.searchQuery);
				}
			}
		});
	},

	/**
	 * Show confirmation dialog and then show Beacon if user confirmed. Must be initialized first.
	 *
	 * @returns {boolean}
	 */
	showBeaconIfConfirmed: function () {
		let wantBeaconRun = confirm(this.confirmationMessage);
		if (wantBeaconRun) {
			this.ensureBeaconInitialization();
			this.beaconShow();
		}
		return wantBeaconRun;
	},

	/**
	 * Initilize Beacon libs.
	 */
	ensureBeaconInitialization: function () {
		if (!this.initialized) {
			this.initialized = true;
			!function (e, t, n) {
				function a() {
					var e = t.getElementsByTagName("script")[0], n = t.createElement("script");
					n.type = "text/javascript", n.async = !0, n.src = "https://beacon-v2.helpscout.net", e.parentNode.insertBefore(n, e)
				}

				if (e.Beacon = n = function (t, n, a) {
					e.Beacon.readyQueue.push({method: t, options: n, data: a})
				}, n.readyQueue = [], "complete" === t.readyState) return a();
				e.attachEvent ? e.attachEvent("onload", a) : e.addEventListener("load", a, !1)
			}(window, document, window.Beacon || function () {
			});

			window.Beacon('init', this.beaconId);
		}
	},

	/**
	 * Show Beacon. Must be initialized first.
	 */
	beaconShow: function () {
		window.Beacon('open');
		if (this.searchQuery !== '') {
			window.Beacon('search', this.searchQuery);
		}
	}
};
