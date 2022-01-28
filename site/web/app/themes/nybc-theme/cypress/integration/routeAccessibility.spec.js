const routes = [
  '/', // Homepage.
];

describe('Component accessibility test', () => {
  routes.forEach((route) => {

    const testName = `${route} has no detectable accessibility violations on load.`;
      it (testName, () => {
        cy.visit(route);
        cy.injectAxe();

        // Utility classes that should be ignored by axe scan.
        const axeRuntimeContext = {
          exclude: [['.field--name-field-codesnippet']]
        };

        const axeRuntimeOptions = {
          runOnly: {
            type: 'tag',
            values: ['wcag2a', 'wcag2aa'],
          }
        };

        cy.get('body').each((element, index) => {
          cy.checkA11y(axeRuntimeContext, axeRuntimeOptions, cy.terminalLog);
        });
    });
  });
});
