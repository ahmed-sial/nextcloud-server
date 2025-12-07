describe("TC-FS-10", () => {
    it("TC-FS-10", () => {
        cy.login();
        cy.viewport(1089, 825);
        cy.visit("/apps/files/files");

        cy.get(".files-fileList", { timeout: 10000 }).should("be.visible");
        cy.get(".action-share").first().click();

        cy.contains("button", "Create public link").click();

        cy.contains("label", "Password protect").click();

        cy.get('input[type="password"]').type("12345678");

        cy.contains("button", "Create").click();

        cy.get(".share-link").should("be.visible");
    });
});