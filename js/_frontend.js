/**
 * @file js/_frontend.js
 * @summary Scripting for the public front-end.
 * @description PHP variables are provided in wpdtrt_table_config.
 * @requires DTRT WordPress Plugin Boilerplate Generator 0.9.3
 */

/* globals jQuery, wpdtrt_table_config, TableOverflow */
/* eslint-disable camelcase, no-unused-vars */

/**
 * jQuery object
 *
 * @external jQuery
 * @see {@link http://api.jquery.com/jQuery/}
 */

/**
 * @namespace wpdtrtTableUi
 */
const wpdtrtTableUi = {

    /**
     * Method: init
     *
     * Initialise front-end scripting.
     */
    init: () => {
        document.querySelectorAll('.wpdtrt-table').forEach((table) => {
            const tableOverflow = new TableOverflow({
                instanceElement: table
            });
            tableOverflow.init();
        });
    }
};

jQuery(($) => {
    const config = wpdtrt_table_config; // eslint-disable-line

    wpdtrtTableUi.init();

    console.log('wpdtrtTableUi.init'); // eslint-disable-line no-console
});
