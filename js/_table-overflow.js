/* eslint-disable no-unused-vars */

/**
 * @class TableOverflow
 * @summary Class used to store local state and make DOM calls relative to a particular element.
 *
 * @param {object} config                    - Module configuration
 * @param {string} config.hintText           - Hint to display when table is wider than screen.
 * @param {null|Node} config.instanceElement - The outermost DOM element
 */
class TableOverflow {
    constructor(config = {}) {
        const options = {
            hintText: 'Swipe or scroll to see full table.',
            instanceElement: null
        };

        // merge objects
        const settings = { ...options, ...config };

        // public settings
        this.hintText = settings.hintText;
        this.instanceElement = settings.instanceElement;
    }

    /**
     * @function assignInstanceId
     * @summary Assign a unique ID to an instance to allow querying of descendant selectors sans :scope (Edge 79)
     * @memberof TabbedCarousel
     */
    assignInstanceId() {
        if (this.instanceElement.getAttribute('id') === null) {
            const randomNumber = () => {
                return Math.floor((1 + Math.random()) * 0x10000)
                    .toString(16)
                    .substring(1);
            };

            this.instanceElement.setAttribute('id', `wpdtrt-table-${randomNumber()}-${randomNumber()}`);
        }

        this.instanceId = this.instanceElement.getAttribute('id');
    }

    toggleHint() {
        const wrapper = document.getElementById(this.instanceId);
        const caption = document.querySelector(`#${this.instanceId} .wpdtrt-table__caption-liner`);
        const captionHint = document.querySelector(`#${this.instanceId} .wpdtrt-table__caption-hint`);
        const hintText = this.hintText;

        if (wrapper.scrollWidth > wrapper.offsetWidth) {
            wrapper.classList.add('wpdtrt-table--scrollbar');

            // limit width of caption
            wrapper.setAttribute('style', `--wpdtrt-table-wrapper-width: ${wrapper.offsetWidth}px`);

            // if WordPress output a caption, inject a hint
            if ((caption !== null) && (captionHint === null)) {
                const hintEl = document.createElement('span');
                const hintTextNode = document.createTextNode(hintText);

                hintEl.classList.add('wpdtrt-table__caption-hint');
                hintEl.appendChild(hintTextNode);
                caption.appendChild(hintEl);
            }
        } else {
            wrapper.classList.remove('wpdtrt-table--scrollbar');

            // relax width of caption
            wrapper.setAttribute('style', '--wpdtrt-table-wrapper-width: 100%');

            // if a hint was injected remove it
            if (captionHint !== null) {
                captionHint.parentNode.removeChild(captionHint);
            }
        }
    }

    init() {
        this.assignInstanceId();
        this.toggleHint();

        window.addEventListener('resize', () => {
            this.toggleHint();
        });
    }
}
