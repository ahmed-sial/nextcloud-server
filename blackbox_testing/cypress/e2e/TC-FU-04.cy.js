
describe("TC-FU-04", () => {
    it("tests TC-FU-04", () => {
        cy.login();
        cy.viewport(1089, 825);
        cy.visit("http://localhost:8080/apps/files/files");

        cy.get("div.files-list__header > div > div svg").click();
        cy.get("li.active span.action-button__longtext-wrapper > span").click();

        cy.get('input[data-cy-upload-picker-input][type="file"]')
            .selectFile('cypress/fixtures/large.zip', { force: true });

        // Verify error message for file too large
        cy.contains("Error: File too large").should("be.visible");
    });
});