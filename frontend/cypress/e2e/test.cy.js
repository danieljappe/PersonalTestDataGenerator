
describe('passing', () => {

    beforeEach(() => {
        cy.visit('index.html')});

    it('Retrieve items based on default input', () => {
        cy.get('#submit input').click();
        cy.get('#output')
            .should('be.visible')
            .children()
            .should('have.length', 1)
            .and('be.visible')
    })
    const amount = [5, 100];
    amount.forEach((num) => {
        it(`Retrieve items based on amount from input of: ${num}`, () => {
               cy.get('#txtNumberPersons').clear().type(`${num}`)
                   .should('have.value', `${num}`);
               cy.get('#submit input').click();
               cy.get('#output')
                   .should('be.visible')
                   .children()
                   .should('have.length', num)
                   .and('be.visible');
        });
    });
    const options = ['name-gender', 'cpr-name-gender-dob', 'address'];
    options.forEach((option) => {
        it(`Retrieve items based on option from select of: ${option}`, () => {
            cy.get('#chkPartialOptions').click()
            cy.get('#cmbPartialOptions').select(option);
            cy.get('#submit input').click();
            cy.get('#output')
                .should('be.visible')
                .children().should('have.length', 1);

            const props = option.split('-');
            cy.get('#output .personCard').within(() => {
                props.forEach(prop => {
                    if (prop === 'name') {
                        cy.get('.firstName').should('be.visible');
                        cy.get('.lastName').should('be.visible');
                    } else {
                        cy.get(`.${prop}`).should('be.visible');
                    }
                });
            });
        });
    });
})