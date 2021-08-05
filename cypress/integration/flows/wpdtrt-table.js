/**
 * @file cypress/integration/flows/wpdtrt-table.js
 * @summary Cypress spec for End-to-End UI testing.
 * @requires DTRT WordPress Plugin Boilerplate Generator 0.9.3
 * {@link https://github.com/dotherightthing/wpdtrt-plugin-boilerplate/wiki/Testing-&-Debugging:-Cypress.io|Testing & Debugging: Cypress.io}
 */

/* eslint-disable prefer-arrow-callback */
/* eslint-disable max-len */

// Test principles:
// ARRANGE: SET UP APP STATE > ACT: INTERACT WITH IT > ASSERT: MAKE ASSERTIONS

// Aliases are cleared between tests
// https://stackoverflow.com/questions/49431483/using-aliases-in-cypress

// Passing arrow functions (“lambdas”) to Mocha is discouraged
// https://mochajs.org/#arrow-functions
/* eslint-disable func-names */

const componentClass = 'wpdtrt-table';

describe('Overflow', function () {
    before(function () {
        // load local web page
        cy.visit('/tourdiaries/asia/east-asia/mongolia/39/kharkhorin/');
    });

    beforeEach(function () {
        // refresh the page to reset the UI state
        cy.reload();

        // @aliases
        cy.get(`.${componentClass}`).eq(0).as('tableWrapper');
        cy.get(`.${componentClass}__caption-liner`).eq(0).as('tableCaptionLiner');

        // scroll component into view,
        // as Cypress can't always 'see' elements below the fold
        cy.get('@tableWrapper')
            .scrollIntoView({
                offset: {
                    top: 100,
                    left: 0
                }
            })
            .should('be.visible');
    });

    describe('Setup', function () {
        it('Has prerequisites', function () {
            // check that the plugin object is available
            cy.window().should('have.property', 'wpdtrtTableUi');

            // check that it's an object
            cy.window().then((win) => {
                expect(win.wpdtrtTableUi).to.be.a('object');
            });
        });
    });

    describe('Scrollbar hint', function () {
        it('Only displays when there isn\'t room to display the entire table width', function () {
            // Wide viewport - scroll hint removed

            cy.viewport(1024, 768);

            cy.get('@tableWrapper')
                .scrollIntoView({
                    offset: {
                        top: 100,
                        left: 0
                    }
                })
                .should('be.visible');

            cy.get('@tableWrapper')
                .should('not.have.class', `${componentClass}--scrollbar`);

            cy.get('@tableWrapper')
                .should('have.attr', 'style', `--${componentClass}-wrapper-width: 100%`);

            cy.get('@tableCaptionLiner').find(`.${componentClass}__caption-hint`)
                .should('not.exist');

            cy.viewport(250, 768);

            // Narrow viewport - scroll hint shown

            cy.get('@tableWrapper')
                .scrollIntoView({
                    offset: {
                        top: 100,
                        left: 0
                    }
                })
                .should('be.visible');

            cy.get('@tableWrapper')
                .should('have.class', `${componentClass}--scrollbar`);

            cy.get('@tableWrapper')
                .should('have.attr', 'style', `--${componentClass}-wrapper-width: 241px`);

            cy.get('@tableCaptionLiner').find(`.${componentClass}__caption-hint`)
                .should('exist');

            // Wide viewport - scroll hint removed

            cy.viewport(1024, 768);

            cy.get('@tableWrapper')
                .scrollIntoView({
                    offset: {
                        top: 100,
                        left: 0
                    }
                })
                .should('be.visible');

            cy.get('@tableWrapper')
                .should('not.have.class', `${componentClass}--scrollbar`);

            cy.get('@tableWrapper')
                .should('have.attr', 'style', `--${componentClass}-wrapper-width: 100%`);

            cy.get('@tableCaptionLiner').find(`.${componentClass}__caption-hint`)
                .should('not.exist');

            cy.viewport(250, 768);
        });
    });
});
