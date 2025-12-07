describe("TC-FS-02", () => {
    it("TC-FS-02", () => {
        cy.login();
        cy.viewport(1089, 825);
        cy.visit("/apps/files/files");

        cy.get(".files-fileList", { timeout: 10000 }).should("be.visible");
        cy.get(".action-share").first().click();

        cy.contains("button", "Create public link").click();

        cy.contains("label", "Password protect").click();
        cy.get('input[type="password"]').type("SecurePass123!");

        cy.contains("label", "Set expiration date").click();

        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        const formattedDate = tomorrow.toISOString().split('T')[0];

        cy.get('input[type="date"]').type(formattedDate);

        cy.contains("button", "Create").click();

        cy.get(".link-shares").should("be.visible");
        cy.get(".share-link").should("be.visible");
    });
});