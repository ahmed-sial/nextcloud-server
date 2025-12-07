
describe("TC-FU-06", () => {
    it("tests TC-FU-06", () => {
        cy.login();
        cy.viewport(1089, 825);
        cy.visit("http://localhost:8080/apps/files/files");

        cy.get("div.files-list__header > div > div svg").click();
        cy.get("li.active span.action-button__longtext-wrapper > span").click();

        // Click upload without selecting a file
        cy.get('input[data-cy-upload-picker-input][type="file"]')
            .then($input => {
                // Trigger upload with empty selection
                $input[0].dispatchEvent(new Event('change', { bubbles: true }));
            });

        // Verify error message
        cy.contains("Error: Please select a file").should("be.visible");
    });
});